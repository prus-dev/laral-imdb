<?php

namespace App\Services;

use App\Models\TitleVote;
use Carbon\CarbonInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class RatingInsightsService
{
    private const MIN_PUBLIC_VOTES = 25;
    private const NEW_TITLE_PROTECTION_DAYS = 14;
    private const NEW_TITLE_MIN_VOTES = 100;

    private const GLOBAL_MEAN = 6.8;
    private const BAYESIAN_M = 100;

    public function recordVote(string $titleId, int $score, array $context): TitleVote
    {
        $country = $context['country'] ?? null;
        $deviceHash = $context['device_hash'] ?? null;
        $accountHash = $context['account_hash'] ?? null;
        $ipHash = $context['ip_hash'] ?? null;

        $reasons = $this->suspiciousReasons($titleId, $deviceHash, $accountHash, $ipHash);

        return TitleVote::create([
            'title_id' => $titleId,
            'score' => $score,
            'country_code' => $country,
            'device_hash' => $deviceHash,
            'account_hash' => $accountHash,
            'ip_hash' => $ipHash,
            'is_suspicious' => $reasons !== [],
            'suspicious_reasons' => $reasons,
            'voted_at' => now(),
        ]);
    }

    public function insightsForTitle(string $titleId, ?int $releaseYear, ?string $accountHash = null): array
    {
        $trusted = TitleVote::query()->where('title_id', $titleId)->where('is_suspicious', false);

        $totalVotes = (clone $trusted)->count();
        $averageScore = round((float) ((clone $trusted)->avg('score') ?? 0), 2);

        $weightedRanking = round($this->weightedRanking($averageScore, $totalVotes), 2);

        $isNewTitle = $releaseYear !== null && $releaseYear >= (int) now()->format('Y') - 1;
        $publicRankingEligible = $totalVotes >= self::MIN_PUBLIC_VOTES
            && (!$isNewTitle || $totalVotes >= self::NEW_TITLE_MIN_VOTES);

        $histogram = $this->histogram((clone $trusted)->get(['score']));

        $trendRows = (clone $trusted)
            ->where('voted_at', '>=', now()->subDays(14))
            ->selectRaw('DATE(voted_at) as day, ROUND(AVG(score), 2) as avg_score, COUNT(*) as votes')
            ->groupBy('day')
            ->orderBy('day')
            ->get()
            ->map(fn ($row) => [
                'day' => $row->day,
                'avg_score' => (float) $row->avg_score,
                'votes' => (int) $row->votes,
            ])
            ->values()
            ->all();

        $personalRating = null;

        if ($accountHash !== null) {
            $personalRating = TitleVote::query()
                ->where('title_id', $titleId)
                ->where('account_hash', $accountHash)
                ->latest('voted_at')
                ->value('score');
        }

        $abuseSignals = [
            'suspicious_votes' => TitleVote::query()->where('title_id', $titleId)->where('is_suspicious', true)->count(),
            'countries' => TitleVote::query()
                ->where('title_id', $titleId)
                ->whereNotNull('country_code')
                ->selectRaw('country_code, COUNT(*) as votes')
                ->groupBy('country_code')
                ->orderByDesc('votes')
                ->limit(5)
                ->get()
                ->map(fn ($row) => ['country' => $row->country_code, 'votes' => (int) $row->votes])
                ->values()
                ->all(),
            'devices' => TitleVote::query()
                ->where('title_id', $titleId)
                ->whereNotNull('device_hash')
                ->distinct('device_hash')
                ->count('device_hash'),
            'accounts' => TitleVote::query()
                ->where('title_id', $titleId)
                ->whereNotNull('account_hash')
                ->distinct('account_hash')
                ->count('account_hash'),
        ];

        return [
            'average_score' => $averageScore,
            'total_votes' => $totalVotes,
            'weighted_ranking' => $weightedRanking,
            'public_ranking_eligible' => $publicRankingEligible,
            'minimum_votes_threshold' => self::MIN_PUBLIC_VOTES,
            'new_title_min_votes' => self::NEW_TITLE_MIN_VOTES,
            'histogram' => $histogram,
            'trend' => $trendRows,
            'personal_rating' => $personalRating,
            'community_average' => $averageScore,
            'demographic_breakdown' => $abuseSignals['countries'],
            'top_liked_reviews_proxy' => $this->topLikedProxy($histogram),
            'abuse_signals' => $abuseSignals,
        ];
    }

    private function weightedRanking(float $average, int $votes): float
    {
        if ($votes === 0) {
            return self::GLOBAL_MEAN;
        }

        return ($votes / ($votes + self::BAYESIAN_M) * $average)
            + (self::BAYESIAN_M / ($votes + self::BAYESIAN_M) * self::GLOBAL_MEAN);
    }

    private function histogram(Collection $votes): array
    {
        $bucket = collect(range(1, 10))->mapWithKeys(fn ($score) => [$score => 0])->all();

        foreach ($votes as $vote) {
            $bucket[(int) $vote->score]++;
        }

        return $bucket;
    }

    private function topLikedProxy(array $histogram): array
    {
        return collect($histogram)
            ->sortDesc()
            ->take(3)
            ->map(fn ($count, $score) => ['score' => (int) $score, 'votes' => $count])
            ->values()
            ->all();
    }

    private function suspiciousReasons(string $titleId, ?string $deviceHash, ?string $accountHash, ?string $ipHash): array
    {
        $reasons = [];

        if ($accountHash !== null) {
            $accountBurst = TitleVote::query()
                ->where('title_id', $titleId)
                ->where('account_hash', $accountHash)
                ->where('voted_at', '>=', now()->subDay())
                ->count();

            if ($accountBurst >= 3) {
                $reasons[] = 'account_repeat_voting';
            }
        }

        if ($deviceHash !== null) {
            $deviceBurst = TitleVote::query()
                ->where('title_id', $titleId)
                ->where('device_hash', $deviceHash)
                ->where('voted_at', '>=', now()->subDay())
                ->count();

            if ($deviceBurst >= 4) {
                $reasons[] = 'device_repeat_voting';
            }
        }

        if ($ipHash !== null) {
            $ipBurst = TitleVote::query()
                ->where('title_id', $titleId)
                ->where('ip_hash', $ipHash)
                ->where('voted_at', '>=', now()->subHour())
                ->count();

            if ($ipBurst >= 10) {
                $reasons[] = 'ip_burst';
            }
        }

        $titleBurst = TitleVote::query()
            ->where('title_id', $titleId)
            ->where('voted_at', '>=', now()->subMinutes(10))
            ->count();

        if ($titleBurst >= 30) {
            $reasons[] = 'title_velocity_spike';
        }

        return array_values(array_unique($reasons));
    }
}

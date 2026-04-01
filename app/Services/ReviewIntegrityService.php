<?php

namespace App\Services;

use App\Models\Review;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ReviewIntegrityService
{
    public function moderate(string $movieId, string $content, string $fingerprint): array
    {
        $flags = [];
        $normalized = Str::of($content)->lower()->squish()->value();

        if ($this->containsProfanity($normalized)) {
            $flags[] = 'profanity';
        }

        if ($this->isSpam($content)) {
            $flags[] = 'spam';
        }

        if ($this->isDuplicate($movieId, $normalized)) {
            $flags[] = 'duplicate';
        }

        $isAiSpam = config('services.review_integrity.ai_spam_detection', false)
            ? $this->isLikelyAiSpam($content)
            : false;

        if ($isAiSpam) {
            $flags[] = 'ai_spam';
        }

        $rateKey = "review-rate:{$movieId}:{$fingerprint}";

        if (!Cache::add($rateKey, 0, now()->addHour())) {
            $current = (int) Cache::get($rateKey, 0);
            if ($current >= 3) {
                $flags[] = 'rate_limited';
            }
        }

        Cache::increment($rateKey);

        $qualityScore = $this->qualityScore($content, $flags);
        $status = empty($flags) ? 'approved' : 'pending';

        return [
            'status' => $status,
            'flags' => array_values(array_unique($flags)),
            'quality_score' => $qualityScore,
            'is_ai_spam' => $isAiSpam,
        ];
    }

    private function containsProfanity(string $content): bool
    {
        $blocked = ['fuck', 'shit', 'bitch', 'asshole', 'bastard'];

        foreach ($blocked as $word) {
            if (Str::contains($content, $word)) {
                return true;
            }
        }

        return false;
    }

    private function isSpam(string $content): bool
    {
        $lower = Str::lower($content);
        $linkCount = preg_match_all('/https?:\/\//i', $content);
        $repeatedChars = preg_match('/(.)\1{6,}/', $content) === 1;

        return $linkCount >= 2
            || $repeatedChars
            || Str::contains($lower, ['buy now', 'free money', 'click here']);
    }

    private function isDuplicate(string $movieId, string $normalizedContent): bool
    {
        return Review::query()
            ->where('movie_imdb_id', $movieId)
            ->whereRaw('LOWER(TRIM(content)) = ?', [$normalizedContent])
            ->exists();
    }

    private function isLikelyAiSpam(string $content): bool
    {
        $wordCount = str_word_count($content);
        $sentenceCount = max(1, preg_match_all('/[\.!?]+/', $content));
        $avgSentenceWords = $wordCount / $sentenceCount;

        return $wordCount > 120 && $avgSentenceWords > 24;
    }

    private function qualityScore(string $content, array $flags): int
    {
        $wordCount = str_word_count($content);
        $lengthScore = min(40, (int) floor($wordCount / 2));
        $varietyScore = preg_match('/[\.!?]/', $content) ? 20 : 5;
        $penalty = count($flags) * 20;

        return max(0, min(100, $lengthScore + $varietyScore + 40 - $penalty));
    }
}

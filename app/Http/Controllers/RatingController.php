<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\RatingEvent;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    private const LOCAL_USER_KEY = 'local-user';

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'imdb_id' => ['required', 'string', 'max:50'],
            'context_type' => ['required', 'in:title,series,episode'],
            'score' => ['required', 'integer', 'between:1,10'],
        ]);

        $rating = Rating::query()->where([
            'user_key' => self::LOCAL_USER_KEY,
            'imdb_id' => $data['imdb_id'],
            'context_type' => $data['context_type'],
        ])->first();

        $action = 'rated';
        $oldScore = null;

        if ($rating) {
            $action = 'rerated';
            $oldScore = $rating->score;
            $rating->score = $data['score'];
            $rating->watched_at ??= now();
            $rating->save();
        } else {
            $rating = Rating::query()->create([
                'user_key' => self::LOCAL_USER_KEY,
                'imdb_id' => $data['imdb_id'],
                'context_type' => $data['context_type'],
                'score' => $data['score'],
                'watched_at' => now(),
            ]);
        }

        RatingEvent::query()->create([
            'user_key' => self::LOCAL_USER_KEY,
            'imdb_id' => $data['imdb_id'],
            'context_type' => $data['context_type'],
            'action' => $action,
            'old_score' => $oldScore,
            'new_score' => $rating->score,
        ]);

        return response()->json($this->payload($data['imdb_id'], $data['context_type']));
    }

    public function destroy(Request $request): JsonResponse
    {
        $data = $request->validate([
            'imdb_id' => ['required', 'string', 'max:50'],
            'context_type' => ['required', 'in:title,series,episode'],
        ]);

        $rating = Rating::query()->where([
            'user_key' => self::LOCAL_USER_KEY,
            'imdb_id' => $data['imdb_id'],
            'context_type' => $data['context_type'],
        ])->first();

        if ($rating) {
            RatingEvent::query()->create([
                'user_key' => self::LOCAL_USER_KEY,
                'imdb_id' => $data['imdb_id'],
                'context_type' => $data['context_type'],
                'action' => 'removed',
                'old_score' => $rating->score,
                'new_score' => null,
            ]);
            $rating->delete();
        }

        return response()->json($this->payload($data['imdb_id'], $data['context_type']));
    }

    private function payload(string $imdbId, string $contextType): array
    {
        $aggregate = Rating::query()->where([
            'imdb_id' => $imdbId,
            'context_type' => $contextType,
        ])->selectRaw('ROUND(AVG(score), 1) as average_score, COUNT(*) as ratings_count')->first();

        $profile = Rating::query()->where('user_key', self::LOCAL_USER_KEY)
            ->selectRaw('COUNT(*) as ratings_count, ROUND(AVG(score), 1) as average_score, COUNT(watched_at) as watched_count')
            ->first();

        $history = RatingEvent::query()
            ->where('user_key', self::LOCAL_USER_KEY)
            ->latest()
            ->limit(8)
            ->get(['action', 'old_score', 'new_score', 'context_type', 'created_at'])
            ->map(fn (RatingEvent $event): array => [
                'action' => $event->action,
                'old_score' => $event->old_score,
                'new_score' => $event->new_score,
                'context_type' => $event->context_type,
                'created_at' => $event->created_at?->toIso8601String(),
            ]);

        return [
            'aggregate' => [
                'average_score' => (float) ($aggregate->average_score ?? 0),
                'ratings_count' => (int) ($aggregate->ratings_count ?? 0),
            ],
            'profile' => [
                'ratings_count' => (int) ($profile->ratings_count ?? 0),
                'average_score' => (float) ($profile->average_score ?? 0),
                'watched_count' => (int) ($profile->watched_count ?? 0),
            ],
            'history' => $history,
            'toast' => [
                'message' => 'Your rating was saved.',
            ],
            'show_review_prompt' => true,
        ];
    }
}

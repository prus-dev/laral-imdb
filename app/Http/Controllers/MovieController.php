<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function search(Request $request, ImdbApiService $imdb): View
    {
        $query = (string) $request->string('q');

        return view('movies.search', [
            'query' => $query,
            'results' => $query !== '' ? $imdb->search($query) : [],
        ]);
    }

    public function show(Request $request, string $id, ImdbApiService $imdb): View
    {
        $movie = $imdb->findById($id);
        $reviews = rescue(fn (): array => $imdb->reviews($id), []);
        $normalizedReviews = array_map([$this, 'normalizeReview'], $reviews);
        $filteredReviews = $this->applyReviewFilters($request, $normalizedReviews);

        return view('movies.show', [
            'movie' => $movie,
            'similar' => $imdb->similar($id),
            'reviews' => $filteredReviews,
            'reviewFilters' => [
                'sort' => (string) $request->string('sort', 'most_helpful'),
                'spoilers_only' => $request->boolean('spoilers_only'),
                'score_min' => $request->input('score_min'),
                'score_max' => $request->input('score_max'),
                'verified_only' => $request->boolean('verified_only'),
            ],
        ]);
    }

    private function normalizeReview(array $review): array
    {
        return [
            'title' => $review['title'] ?? $review['summary'] ?? null,
            'content' => $review['content'] ?? $review['text'] ?? $review['reviewText'] ?? null,
            'author' => $review['author'] ?? $review['authorName'] ?? $review['user']['displayName'] ?? 'Anonymous',
            'score' => $review['score'] ?? $review['rating'] ?? $review['authorRating'] ?? null,
            'reviewer_rating' => $review['reviewerRating'] ?? $review['authorRating'] ?? $review['score'] ?? null,
            'helpful_votes' => $review['helpfulVotes'] ?? $review['helpful']['upVotes'] ?? 0,
            'date' => $review['date'] ?? $review['createdAt'] ?? $review['created_at'] ?? null,
            'spoiler' => (bool) ($review['spoiler'] ?? $review['containsSpoiler'] ?? false),
            'verified_watcher' => (bool) ($review['verifiedWatcher'] ?? $review['isVerifiedWatcher'] ?? false),
        ];
    }

    private function applyReviewFilters(Request $request, array $reviews): array
    {
        $spoilersOnly = $request->boolean('spoilers_only');
        $verifiedOnly = $request->boolean('verified_only');
        $scoreMin = is_numeric($request->input('score_min')) ? (float) $request->input('score_min') : null;
        $scoreMax = is_numeric($request->input('score_max')) ? (float) $request->input('score_max') : null;
        $sort = (string) $request->string('sort', 'most_helpful');

        $filtered = array_values(array_filter($reviews, function (array $review) use ($spoilersOnly, $verifiedOnly, $scoreMin, $scoreMax): bool {
            if ($spoilersOnly && !$review['spoiler']) {
                return false;
            }

            if ($verifiedOnly && !$review['verified_watcher']) {
                return false;
            }

            if (is_float($scoreMin) && is_numeric($review['score']) && (float) $review['score'] < $scoreMin) {
                return false;
            }

            if (is_float($scoreMax) && is_numeric($review['score']) && (float) $review['score'] > $scoreMax) {
                return false;
            }

            return true;
        }));

        usort($filtered, function (array $a, array $b) use ($sort): int {
            return match ($sort) {
                'newest' => strtotime((string) ($b['date'] ?? '')) <=> strtotime((string) ($a['date'] ?? '')),
                'highest_rated_reviewer' => (float) ($b['reviewer_rating'] ?? 0) <=> (float) ($a['reviewer_rating'] ?? 0),
                default => (int) ($b['helpful_votes'] ?? 0) <=> (int) ($a['helpful_votes'] ?? 0),
            };
        });

        return $filtered;
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Rating;
use App\Models\RatingEvent;
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

    public function show(string $id, ImdbApiService $imdb): View
    {
        $movie = $imdb->findById($id);
        $contextType = match ($movie['titleType'] ?? null) {
            'tvEpisode' => 'episode',
            'tvSeries' => 'series',
            default => 'title',
        };

        $userRating = Rating::query()->where([
            'user_key' => 'local-user',
            'imdb_id' => $id,
            'context_type' => $contextType,
        ])->first();

        $aggregate = Rating::query()->where([
            'imdb_id' => $id,
            'context_type' => $contextType,
        ])->selectRaw('ROUND(AVG(score), 1) as average_score, COUNT(*) as ratings_count')->first();

        $profile = Rating::query()->where('user_key', 'local-user')
            ->selectRaw('COUNT(*) as ratings_count, ROUND(AVG(score), 1) as average_score, COUNT(watched_at) as watched_count')
            ->first();

        $history = RatingEvent::query()->where('user_key', 'local-user')->latest()->limit(8)->get();

        return view('movies.show', [
            'movie' => $movie,
            'similar' => $imdb->similar($id),
            'contextType' => $contextType,
            'userRating' => $userRating,
            'aggregate' => $aggregate,
            'profile' => $profile,
            'history' => $history,
        ]);
    }
}

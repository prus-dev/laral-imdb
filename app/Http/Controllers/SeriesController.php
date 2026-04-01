<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class SeriesController extends Controller
{
    public function show(string $id, ImdbApiService $imdb): View
    {
        $series = $imdb->findById($id);
        $seasons = $imdb->seasons($id);
        $episodes = $imdb->episodes($id);

        return view('series.show', [
            'series' => $series,
            'seasons' => $seasons,
            'episodes' => $episodes,
            'seasonRanking' => $this->seasonRanking($episodes),
            'topEpisodes' => collect($episodes)->sortByDesc(fn ($episode) => (float) ($episode['averageRating'] ?? 0))->take(5)->values()->all(),
            'lowestEpisodes' => collect($episodes)->sortBy(fn ($episode) => (float) ($episode['averageRating'] ?? 10))->take(5)->values()->all(),
        ]);
    }

    public function season(string $id, int $season, ImdbApiService $imdb): View
    {
        $series = $imdb->findById($id);

        return view('series.season', [
            'series' => $series,
            'seasonNumber' => $season,
            'episodes' => $imdb->episodes($id, $season),
            'poster' => $imdb->seasonPoster($id, $season) ?? ($series['primaryImage'] ?? null),
        ]);
    }

    public function episode(string $id, int $season, string $episodeId, ImdbApiService $imdb): View
    {
        $series = $imdb->findById($id);
        $episode = $imdb->findById($episodeId);

        return view('series.episode', [
            'series' => $series,
            'seasonNumber' => $season,
            'episode' => $episode,
            'credits' => $imdb->credits($episodeId),
        ]);
    }

    protected function seasonRanking(array $episodes): array
    {
        return collect($episodes)
            ->groupBy(fn ($episode) => (int) ($episode['seasonNumber'] ?? 0))
            ->map(function ($seasonEpisodes, $season) {
                return [
                    'season' => (int) $season,
                    'rating' => round($seasonEpisodes->avg(fn ($episode) => (float) ($episode['averageRating'] ?? 0)), 2),
                    'episodes' => $seasonEpisodes->count(),
                ];
            })
            ->filter(fn ($item) => $item['season'] > 0)
            ->sortByDesc('rating')
            ->values()
            ->all();
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ImdbApiService $imdb): View
    {
        $collections = [
            'trendingNow' => $imdb->trending(limit: 12),
            'topRatedMovies' => $imdb->search('top rated movies', 12),
            'topRatedTvShows' => $imdb->search('top rated tv series', 12),
            'mostAnticipated' => $imdb->search('most anticipated movies', 12),
            'inTheaters' => $imdb->search('in theaters now', 12),
            'streamingNow' => $imdb->search('streaming now series', 12),
            'recentlyAdded' => $imdb->search('new releases', 12),
            'popularCelebrities' => $imdb->search('popular actors', 12),
            'latestTrailers' => $imdb->search('official trailer', 12),
            'latestReviews' => $imdb->search('critics choice reviews', 12),
            'featuredLists' => $imdb->search('best movies list', 12),
            'editorialPicks' => $imdb->search('editor picks films', 12),
            'awardSeasonSpotlight' => $imdb->search('awards season contenders', 12),
        ];

        return view('home', [
            'hero' => $collections['trendingNow'][0] ?? null,
            'collections' => $collections,
            'taxonomyLinks' => [
                'genre' => ['Action', 'Comedy', 'Drama', 'Sci-Fi', 'Thriller', 'Animation'],
                'mood' => ['Feel-good', 'Dark', 'Mind-bending', 'Family', 'Epic'],
                'language' => ['English', 'Spanish', 'Korean', 'Japanese', 'Hindi'],
                'country' => ['USA', 'UK', 'India', 'South Korea', 'France'],
                'year' => ['2026', '2025', '2024', '2023', '2022'],
                'network' => ['Netflix', 'Prime Video', 'Hulu', 'Max', 'Apple TV+'],
            ],
        ]);
    }
}

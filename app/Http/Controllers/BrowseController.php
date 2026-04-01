<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class BrowseController extends Controller
{
    public function collection(string $slug, Request $request, ImdbApiService $imdb): View
    {
        $labels = [
            'trending-now' => 'Trending now',
            'top-rated-movies' => 'Top rated movies',
            'top-rated-tv-shows' => 'Top rated TV shows',
            'most-anticipated' => 'Most anticipated',
            'in-theaters' => 'In theaters',
            'streaming-now' => 'Streaming now',
            'recently-added' => 'Recently added titles',
            'popular-celebrities' => 'Popular celebrities',
            'latest-trailers' => 'Latest trailers',
            'latest-reviews' => 'Latest reviews',
            'featured-lists' => 'Featured lists',
            'editorial-picks' => 'Editorial picks',
            'award-season-spotlight' => 'Award season spotlight',
        ];

        $query = $labels[$slug] ?? Str::headline(str_replace('-', ' ', $slug));
        $results = $slug === 'trending-now' ? $imdb->trending(limit: 36) : $imdb->search($query, 36);

        if ($request->filled('sort') && $request->string('sort')->toString() === 'year_desc') {
            usort($results, fn (array $a, array $b): int => (int) ($b['startYear'] ?? 0) <=> (int) ($a['startYear'] ?? 0));
        }

        return view('browse.collection', [
            'heading' => $labels[$slug] ?? $query,
            'slug' => $slug,
            'results' => $results,
        ]);
    }

    public function discover(Request $request, ImdbApiService $imdb): View
    {
        $filters = $request->only(['genre', 'mood', 'language', 'country', 'year', 'network']);
        $query = collect($filters)->filter()->implode(' ');
        $query = $query !== '' ? $query : 'popular movies';

        return view('browse.discover', [
            'filters' => $filters,
            'results' => $imdb->search($query, 36),
            'query' => $query,
        ]);
    }
}

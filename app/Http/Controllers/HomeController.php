<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ImdbApiService $imdb): View
    {
        $trending = $imdb->trending(limit: 36);

        $makeSection = static fn (string $key, string $label, array $items, array $filters = []): array => [
            'key' => $key,
            'label' => $label,
            'items' => $items,
            'filters' => $filters,
        ];

        $sections = [
            $makeSection('trending', 'Trending now', array_slice($trending, 0, 12), ['collection' => 'trending']),
            $makeSection('top-rated-movies', 'Top rated movies', array_slice($trending, 2, 12), ['collection' => 'top-rated-movies', 'type' => 'movie']),
            $makeSection('top-rated-tv', 'Top rated TV shows', array_slice($trending, 4, 12), ['collection' => 'top-rated-tv', 'type' => 'tvSeries']),
            $makeSection('most-anticipated', 'Most anticipated', array_slice($trending, 6, 12), ['collection' => 'most-anticipated']),
            $makeSection('in-theaters', 'In theaters', array_slice($trending, 8, 12), ['collection' => 'in-theaters']),
            $makeSection('streaming-now', 'Streaming now', array_slice($trending, 10, 12), ['collection' => 'streaming-now']),
            $makeSection('recently-added', 'Recently added titles', array_slice($trending, 12, 12), ['collection' => 'recently-added']),
        ];

        $people = [
            ['id' => 'nm0000138', 'name' => 'Leonardo DiCaprio'],
            ['id' => 'nm0000199', 'name' => 'Keanu Reeves'],
            ['id' => 'nm0000151', 'name' => 'Morgan Freeman'],
            ['id' => 'nm0424060', 'name' => 'Scarlett Johansson'],
            ['id' => 'nm0914612', 'name' => 'Denzel Washington'],
            ['id' => 'nm3053338', 'name' => 'Margot Robbie'],
        ];

        $trailers = array_values(array_map(static function (array $item): array {
            return [
                'id' => $item['id'] ?? $item['tconst'] ?? '',
                'title' => ($item['primaryTitle'] ?? 'Untitled').' trailer',
                'poster' => $item['primaryImage'] ?? $item['image']['url'] ?? 'https://placehold.co/480x270?text=Trailer',
            ];
        }, array_slice($trending, 0, 6)));

        $featuredCollections = [
            ['title' => 'Award season spotlight', 'filters' => ['collection' => 'award-season']],
            ['title' => 'Editorial picks', 'filters' => ['collection' => 'editorial-picks']],
            ['title' => 'Featured lists', 'filters' => ['collection' => 'featured-lists']],
            ['title' => 'Latest reviews', 'filters' => ['collection' => 'latest-reviews']],
        ];

        return view('home', [
            'hero' => $trending[0] ?? null,
            'sections' => $sections,
            'people' => $people,
            'trailers' => $trailers,
            'featuredCollections' => $featuredCollections,
            'quickLinks' => [
                'genre' => ['Action', 'Comedy', 'Drama', 'Sci-Fi', 'Thriller', 'Animation'],
                'mood' => ['Feel-good', 'Dark', 'Mind-bending', 'Romantic', 'Intense'],
                'language' => ['English', 'Spanish', 'French', 'Japanese', 'Korean'],
                'country' => ['USA', 'UK', 'India', 'South Korea', 'France'],
                'year' => ['2026', '2025', '2024', '2020s', '2010s'],
                'platform' => ['Netflix', 'Prime Video', 'Max', 'Hulu', 'Disney+'],
            ],
        ]);
    }
}

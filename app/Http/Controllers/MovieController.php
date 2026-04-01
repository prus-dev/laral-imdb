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

    public function show(string $id, ImdbApiService $imdb): View
    {
        $movie = $imdb->findById($id);

        return view('movies.show', [
            'movie' => $movie,
            'similar' => $imdb->similar($id),
        ]);
    }

    public function browse(Request $request, ImdbApiService $imdb): View
    {
        $filters = array_filter([
            'collection' => (string) $request->string('collection'),
            'genre' => (string) $request->string('genre'),
            'mood' => (string) $request->string('mood'),
            'language' => (string) $request->string('language'),
            'country' => (string) $request->string('country'),
            'year' => (string) $request->string('year'),
            'platform' => (string) $request->string('platform'),
            'type' => (string) $request->string('type'),
        ]);

        $sort = (string) $request->string('sort', 'popularity');

        return view('movies.browse', [
            'filters' => $filters,
            'sort' => $sort,
            'results' => $imdb->trending(limit: 48),
        ]);
    }
}

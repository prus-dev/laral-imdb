<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use App\Models\Review;
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
            'reviews' => Review::query()->where('movie_imdb_id', $id)->where('status', 'approved')->latest()->get(),
        ]);
    }
}

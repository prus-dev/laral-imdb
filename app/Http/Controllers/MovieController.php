<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class MovieController extends Controller
{
    public function search(): View
    {
        return view('movies.search');
    }

    public function show(string $id, ImdbApiService $imdb): View
    {
        $movie = $imdb->findById($id);

        return view('movies.show', [
            'movie' => $movie,
            'similar' => $imdb->similar($id),
        ]);
    }
}

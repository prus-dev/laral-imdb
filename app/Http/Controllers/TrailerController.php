<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class TrailerController extends Controller
{
    public function show(string $id, ImdbApiService $imdb): View
    {
        return view('trailers.show', [
            'movie' => $imdb->findById($id),
        ]);
    }
}

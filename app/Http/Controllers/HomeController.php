<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(ImdbApiService $imdb): View
    {
        return view('home', [
            'trending' => $imdb->trending(),
        ]);
    }
}

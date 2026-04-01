<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function show(string $id, ImdbApiService $imdb): View
    {
        $person = $imdb->search($id, 1)[0] ?? ['primaryTitle' => $id];

        return view('people.show', [
            'id' => $id,
            'person' => $person,
        ]);
    }
}

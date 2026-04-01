<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use Illuminate\View\View;

class PersonController extends Controller
{
    public function show(string $id, ImdbApiService $imdb): View
    {
        return view('people.show', [
            'person' => $imdb->safePerson($id),
        ]);
    }
}

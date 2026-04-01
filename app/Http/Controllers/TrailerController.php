<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class TrailerController extends Controller
{
    public function show(string $id): View
    {
        return view('trailers.show', [
            'id' => $id,
            'title' => request('title', 'Trailer'),
        ]);
    }
}

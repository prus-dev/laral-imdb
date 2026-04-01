<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PersonController extends Controller
{
    public function show(string $id): View
    {
        return view('people.show', [
            'id' => $id,
            'name' => request('name', 'Celebrity'),
        ]);
    }
}

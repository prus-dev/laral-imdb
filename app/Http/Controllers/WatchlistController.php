<?php

namespace App\Http\Controllers;

use App\Models\WatchlistItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function index(): View
    {
        return view('watchlist.index', [
            'items' => WatchlistItem::query()->latest()->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'imdb_id' => ['required', 'string', 'max:50'],
            'title' => ['required', 'string', 'max:255'],
            'year' => ['nullable', 'integer'],
            'poster_url' => ['nullable', 'url'],
            'rating' => ['nullable', 'numeric', 'between:0,10'],
            'runtime_seconds' => ['nullable', 'integer'],
            'type' => ['nullable', 'string', 'max:40'],
        ]);

        WatchlistItem::query()->updateOrCreate(
            ['imdb_id' => $data['imdb_id']],
            $data,
        );

        return back()->with('status', 'Added to watchlist.');
    }

    public function destroy(WatchlistItem $watchlistItem): RedirectResponse
    {
        $watchlistItem->delete();

        return back()->with('status', 'Removed from watchlist.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Services\ImdbApiService;
use App\Services\RatingInsightsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
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

    public function show(string $id, ImdbApiService $imdb, Request $request, RatingInsightsService $ratings): View
    {
        $movie = $imdb->findById($id);
        $accountKey = $this->accountKey($request);

        return view('movies.show', [
            'movie' => $movie,
            'similar' => $imdb->similar($id),
            'ratingInsights' => $ratings->insightsForTitle($id, isset($movie['startYear']) ? (int) $movie['startYear'] : null, $accountKey),
        ]);
    }

    public function rate(string $id, Request $request, RatingInsightsService $ratings): RedirectResponse
    {
        $validated = $request->validate([
            'score' => ['required', 'integer', 'min:1', 'max:10'],
        ]);

        $accountKey = $this->accountKey($request);

        $ratings->recordVote($id, (int) $validated['score'], [
            'country' => strtoupper(substr((string) $request->header('CF-IPCountry', 'US'), 0, 2)),
            'device_hash' => hash('sha256', (string) $request->userAgent()),
            'account_hash' => hash('sha256', $accountKey),
            'ip_hash' => hash('sha256', (string) $request->ip()),
        ]);

        return back()->with('status', 'Rating submitted.');
    }

    private function accountKey(Request $request): string
    {
        if (! $request->session()->has('rating_account_key')) {
            $request->session()->put('rating_account_key', (string) Str::uuid());
        }

        return (string) $request->session()->get('rating_account_key');
    }
}

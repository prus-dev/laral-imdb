<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Services\ReviewIntegrityService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, ReviewIntegrityService $integrity): RedirectResponse
    {
        $data = $request->validate([
            'movie_imdb_id' => ['required', 'string', 'max:50'],
            'movie_title' => ['nullable', 'string', 'max:255'],
            'author_name' => ['nullable', 'string', 'max:120'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $fingerprint = hash('sha256', sprintf('%s|%s', (string) $request->ip(), (string) $request->userAgent()));

        $moderation = $integrity->moderate($data['movie_imdb_id'], $data['content'], $fingerprint);

        Review::query()->create([
            ...$data,
            'user_fingerprint' => $fingerprint,
            ...$moderation,
        ]);

        return back()->with('status', $moderation['status'] === 'approved'
            ? 'Review published.'
            : 'Review sent to moderator queue.');
    }
}

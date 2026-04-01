<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function store(Request $request, string $imdbId): RedirectResponse
    {
        $data = $request->validate([
            'user_name' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'between:1,10'],
            'headline' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'pros' => ['nullable', 'string', 'max:2000'],
            'cons' => ['nullable', 'string', 'max:2000'],
            'is_spoiler' => ['nullable', 'boolean'],
            'action' => ['required', 'in:draft,publish'],
        ]);

        Review::create([
            'imdb_id' => $imdbId,
            'user_name' => $data['user_name'] ?: 'Anonymous',
            'rating' => $data['rating'] ?? null,
            'headline' => $data['headline'],
            'body' => $data['body'],
            'pros' => $data['pros'] ?? null,
            'cons' => $data['cons'] ?? null,
            'is_spoiler' => (bool) ($data['is_spoiler'] ?? false),
            'status' => $data['action'] === 'publish' ? 'published' : 'draft',
            'published_at' => $data['action'] === 'publish' ? now() : null,
        ]);

        return back()->with('status', $data['action'] === 'publish' ? 'Review published.' : 'Draft saved.');
    }

    public function update(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'user_name' => ['nullable', 'string', 'max:255'],
            'rating' => ['nullable', 'integer', 'between:1,10'],
            'headline' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:5000'],
            'pros' => ['nullable', 'string', 'max:2000'],
            'cons' => ['nullable', 'string', 'max:2000'],
            'is_spoiler' => ['nullable', 'boolean'],
            'action' => ['required', 'in:draft,publish'],
        ]);

        $review->update([
            'user_name' => $data['user_name'] ?: 'Anonymous',
            'rating' => $data['rating'] ?? null,
            'headline' => $data['headline'],
            'body' => $data['body'],
            'pros' => $data['pros'] ?? null,
            'cons' => $data['cons'] ?? null,
            'is_spoiler' => (bool) ($data['is_spoiler'] ?? false),
            'status' => $data['action'] === 'publish' ? 'published' : 'draft',
            'published_at' => $data['action'] === 'publish' ? now() : null,
        ]);

        return back()->with('status', 'Review updated.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('status', 'Review deleted.');
    }
}

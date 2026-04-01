<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReviewInteractionController extends Controller
{
    public function voteHelpful(Request $request, Review $review): RedirectResponse
    {
        $review->helpfulVotes()->firstOrCreate([
            'session_key' => $request->session()->getId(),
        ]);

        return back()->with('status', 'Marked as helpful.');
    }

    public function reportAbuse(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->abuseReports()->firstOrCreate([
            'session_key' => $request->session()->getId(),
        ], [
            'reason' => $data['reason'] ?? null,
        ]);

        return back()->with('status', 'Review reported. Thank you.');
    }

    public function comment(Request $request, Review $review): RedirectResponse
    {
        $data = $request->validate([
            'user_name' => ['nullable', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:2000'],
        ]);

        $review->comments()->create([
            'user_name' => $data['user_name'] ?: 'Anonymous',
            'body' => $data['body'],
        ]);

        return back()->with('status', 'Comment added.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ModeratorReviewController extends Controller
{
    public function index(): View
    {
        return view('moderation.reviews', [
            'reviews' => Review::query()->where('status', 'pending')->latest()->get(),
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['status' => 'approved']);

        return back()->with('status', 'Review approved.');
    }

    public function reject(Review $review): RedirectResponse
    {
        $review->update(['status' => 'rejected']);

        return back()->with('status', 'Review rejected.');
    }
}

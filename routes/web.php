<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ModeratorReviewController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/title/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
Route::delete('/watchlist/{watchlistItem}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
Route::get('/moderation/reviews', [ModeratorReviewController::class, 'index'])->name('moderation.reviews.index');
Route::patch('/moderation/reviews/{review}/approve', [ModeratorReviewController::class, 'approve'])->name('moderation.reviews.approve');
Route::patch('/moderation/reviews/{review}/reject', [ModeratorReviewController::class, 'reject'])->name('moderation.reviews.reject');

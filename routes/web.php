<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/title/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
Route::delete('/watchlist/{watchlistItem}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

Route::post('/ratings', [RatingController::class, 'store'])->name('ratings.store');
Route::delete('/ratings', [RatingController::class, 'destroy'])->name('ratings.destroy');

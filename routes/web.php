<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/title/{id}', [MovieController::class, 'show'])->name('movies.show');

Route::get('/series/{id}', [SeriesController::class, 'show'])->name('series.show');
Route::get('/series/{id}/season/{season}', [SeriesController::class, 'season'])->name('series.season');
Route::get('/series/{id}/season/{season}/episode/{episodeId}', [SeriesController::class, 'episode'])->name('series.episode');

Route::get('/person/{id}', [PersonController::class, 'show'])->name('people.show');

Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
Route::delete('/watchlist/{watchlistItem}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

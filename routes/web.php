<?php

use App\Http\Controllers\BrowseController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\PersonController;
use App\Http\Controllers\TrailerController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/search', [MovieController::class, 'search'])->name('movies.search');
Route::get('/title/{id}', [MovieController::class, 'show'])->name('movies.show');
Route::get('/person/{id}', [PersonController::class, 'show'])->name('people.show');
Route::get('/trailers/{id}', [TrailerController::class, 'show'])->name('trailers.show');
Route::get('/browse/{slug}', [BrowseController::class, 'collection'])->name('browse.collection');
Route::get('/discover', [BrowseController::class, 'discover'])->name('browse.discover');

Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist.index');
Route::post('/watchlist', [WatchlistController::class, 'store'])->name('watchlist.store');
Route::delete('/watchlist/{watchlistItem}', [WatchlistController::class, 'destroy'])->name('watchlist.destroy');

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WatchlistItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id',
        'title',
        'year',
        'poster_url',
        'rating',
        'runtime_seconds',
        'type',
    ];
}

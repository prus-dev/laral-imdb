<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_key',
        'imdb_id',
        'context_type',
        'score',
        'watched_at',
    ];

    protected $casts = [
        'watched_at' => 'datetime',
    ];
}

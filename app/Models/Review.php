<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'movie_imdb_id',
        'movie_title',
        'author_name',
        'user_fingerprint',
        'content',
        'status',
        'flags',
        'quality_score',
        'is_ai_spam',
    ];

    protected $casts = [
        'flags' => 'array',
        'is_ai_spam' => 'boolean',
    ];
}

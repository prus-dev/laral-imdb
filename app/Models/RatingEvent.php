<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RatingEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_key',
        'imdb_id',
        'context_type',
        'action',
        'old_score',
        'new_score',
    ];
}

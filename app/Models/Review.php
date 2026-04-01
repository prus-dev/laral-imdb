<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_id',
        'user_name',
        'rating',
        'headline',
        'body',
        'is_spoiler',
        'pros',
        'cons',
        'status',
        'published_at',
    ];

    protected $casts = [
        'is_spoiler' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function comments(): HasMany
    {
        return $this->hasMany(ReviewComment::class);
    }

    public function helpfulVotes(): HasMany
    {
        return $this->hasMany(ReviewHelpfulVote::class);
    }

    public function abuseReports(): HasMany
    {
        return $this->hasMany(ReviewAbuseReport::class);
    }
}

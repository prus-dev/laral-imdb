<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReviewAbuseReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'review_id',
        'session_key',
        'reason',
    ];
}

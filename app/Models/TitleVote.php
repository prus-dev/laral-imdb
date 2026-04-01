<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TitleVote extends Model
{
    use HasFactory;

    protected $fillable = [
        'title_id',
        'score',
        'country_code',
        'device_hash',
        'account_hash',
        'ip_hash',
        'is_suspicious',
        'suspicious_reasons',
        'voted_at',
    ];

    protected $casts = [
        'is_suspicious' => 'boolean',
        'suspicious_reasons' => 'array',
        'voted_at' => 'datetime',
    ];
}

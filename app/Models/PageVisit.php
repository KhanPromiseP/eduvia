<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PageVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'url',
        'referrer',
        'ip_address',
        'user_agent',
        'visited_at',
        'session_id',
    ];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    protected $dates = [
        'visited_at',
        'created_at',
        'updated_at',
    ];
}

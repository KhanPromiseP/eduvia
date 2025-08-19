<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AdTimeSpent extends Model
{
    use HasFactory;

    protected $table = 'ad_time_spent';

    protected $fillable = [
        'ad_id',
        'session_id',
        'ip_address',
        'user_agent',
        'time_spent',
        'last_tracked_at',
        'placement'
    ];

    protected $casts = [
        'time_spent' => 'float',
        'last_tracked_at' => 'datetime:Y-m-d H:i:s',
    ];

    protected $dateFormat = 'Y-m-d H:i:s';

}
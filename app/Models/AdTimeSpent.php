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
    ];

    protected $casts = [
        'time_spent' => 'float',
        'last_tracked_at' => 'datetime',
    ];

    /**
     * Link back to the ad
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}

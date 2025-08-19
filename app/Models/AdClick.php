<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'ip_address',
        'user_agent',
        'referrer',
        'target_url',
        'clicked_at',
        'session_id',
        'country',
        'city',
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
    ];

  
    /**
     * Get the ad that was clicked.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Scope for today's clicks.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('clicked_at', today());
    }

    /**
     * Scope for clicks within date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('clicked_at', [$startDate, $endDate]);
    }
}

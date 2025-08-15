<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdView extends Model
{
    use HasFactory;

    protected $fillable = [
        'ad_id',
        'ip_address',
        'user_agent',
        'referrer',
        'url',
        'viewport_width',
        'viewport_height',
        'viewed_at',
        'session_id',
        'country',
        'city',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'viewport_width' => 'integer',
        'viewport_height' => 'integer',
    ];

    protected $dates = [
        'viewed_at',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the ad that was viewed.
     */
    public function ad(): BelongsTo
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Get device type based on viewport width.
     */
    public function getDeviceTypeAttribute(): string
    {
        if ($this->viewport_width <= 768) {
            return 'mobile';
        } elseif ($this->viewport_width <= 1024) {
            return 'tablet';
        } else {
            return 'desktop';
        }
    }

    /**
     * Scope for today's views.
     */
    public function scopeToday($query)
    {
        return $query->whereDate('viewed_at', today());
    }

    /**
     * Scope for views within date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('viewed_at', [$startDate, $endDate]);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Analytics extends Model
{
    use HasFactory;

    protected $table = 'analytics';

    // Allow mass assignment on these fields
    protected $fillable = [
        'ad_id',
        'event_type',
        'duration',
        'ip_address',
        'country',
        'device_type',
        'user_agent',
        'referrer',
        'value',
    ];

    /**
     * Casts for attributes.
     */
    protected $casts = [
        'duration' => 'integer',
        'value' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: Analytics belongs to an Ad.
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * Scope to filter by event type.
     */
    public function scopeEventType($query, $type)
    {
        return $query->where('event_type', $type);
    }

    /**
     * Scope to filter by country.
     */
    public function scopeCountry($query, $country)
    {
        return $query->where('country', $country);
    }

    /**
     * Scope to filter by device type.
     */
    public function scopeDeviceType($query, $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }
    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]); 
    }
}

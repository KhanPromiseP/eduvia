<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Ad extends Model
{
    use HasFactory;

    // Allow mass assignment on these fields
    protected $fillable = [
        'user_id',
        'product_id',
        'title',
        'type',
        'content',
        'link',
        'start_at',
        'end_at',
        'is_active',
        'placement',
        'targeting',
        'is_random',
    ];

    // Cast types (targeting is json, timestamps are Carbon instances)
    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'targeting' => 'array',
        'is_random' => 'boolean',
    ];

    /*
     * Relationships
     */

    // Creator user of the ad
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Optional related product (nullable)
    public function product()
    {
        return $this->belongsTo(Product::class)->withDefault();
    }

    /*
     * Scopes for query filtering
     */

    // Only ads marked as active
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Ads that are currently within the start and end date range
    public function scopeCurrentlyRunning($query)
    {
        $now = Carbon::now();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('start_at')->orWhere('start_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('end_at')->orWhere('end_at', '>=', $now);
        });
    }

    // Scope for filtering by placement string (exact or partial match)
    public function scopePlacement($query, string $placement)
    {
        return $query->where('placement', $placement);
    }

    /*
     * Helper methods
     */

    // Check if ad is currently active based on is_active and date range
    public function isCurrentlyActive(): bool
    {
        if (! $this->is_active) {
            return false;
        }

        $now = Carbon::now();

        if ($this->start_at && $this->start_at->isFuture()) {
            return false;
        }

        if ($this->end_at && $this->end_at->isPast()) {
            return false;
        }

        return true;
    }

    // Get targeting info with safe defaults
    public function getTargetingDevices(): array
    {
        return $this->targeting['devices'] ?? [];
    }

    public function getTargetingCountries(): array
    {
        return $this->targeting['countries'] ?? [];
    }

    public function getTargetingLocations(): array
    {
        return $this->targeting['locations'] ?? [];
    }


//     public function analytics()
// {
//     return $this->hasMany(Analytics::class);
// }

// public function views()
// {
//     return $this->analytics()->where('event_type', 'view');
// }

// public function clicks()
// {
//     return $this->analytics()->where('event_type', 'click');
// }

// public function impressions()
// {
//     return $this->analytics()->where('event_type', 'impression');
// }

//     public function getTotalViewsAttribute()
//     {
//         return $this->views()->count();
//     }

//     public function getTotalClicksAttribute()
//     {
//         return $this->clicks()->count();
//     }

//     public function getTotalImpressionsAttribute()
//     {
//         return $this->impressions()->count();
//     }
//     public function getAverageViewDurationAttribute()
//     {
//         return $this->views()->avg('duration') ?? 0;
//     }

//     public function getClickThroughRateAttribute()
//     {
//         $impressions = $this->getTotalImpressionsAttribute();
//         $clicks = $this->getTotalClicksAttribute();
//         return $impressions > 0 ? ($clicks / $impressions) * 100 : 0;
//     }
    

}

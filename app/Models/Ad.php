<?php

// Update your App\Models\Ad.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ad extends Model
{
    use HasFactory;

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
        'weight',
        'budget',
        'max_impressions',
        'max_clicks',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'is_random' => 'boolean',
        'targeting' => 'json',
        'weight' => 'integer',
        'budget' => 'decimal:2',
        'max_impressions' => 'integer',
        'max_clicks' => 'integer',
    ];

    /**
     * Scope for active ads
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for currently running ads (within date range)
     */
 public function scopeCurrentlyRunning($query)
{
    return $query->where(function($q) {
        $q->whereNull('start_at')
          ->orWhere('start_at', '<=', now()->toDateTimeString());
    })->where(function($q) {
        $q->whereNull('end_at')
          ->orWhere('end_at', '>=', now()->toDateTimeString());
    });
}

    /**
     * Scope for ads by placement
     */
    public function scopeForPlacement($query, $placement)
    {
        return $query->where(function($q) use ($placement) {
            $q->where('placement', $placement)
              ->orWhere('placement', 'any')
              ->orWhereNull('placement');
        });
    }

    /**
     * Get ads that should be displayed (active and within date range)
     */
    public function scopeDisplayable($query)
    {
        return $query->active()->currentlyRunning();
    }

    /**
     * Relationships
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(AdView::class);
    }

    public function clicks(): HasMany
    {
        return $this->hasMany(AdClick::class);
    }

    public function timeSpent(): HasMany
    {
        return $this->hasMany(AdTimeSpent::class);
    }

    /**
     * Scope for admin view — show all ads, regardless of status/date
     */
public function scopeForAdmin($query)
    {
        return $query->with(['user']); // Only load user, not product
    }



    /**
     * Get formatted targeting rules
     */
    public function getFormattedTargetingAttribute(): string
    {
        if (!$this->targeting) {
            return 'No targeting';
        }

        $rules = [];
        foreach ($this->targeting as $key => $value) {
            if (is_array($value)) {
                $rules[] = ucfirst($key) . ': ' . implode(', ', $value);
            } else {
                $rules[] = ucfirst($key) . ': ' . $value;
            }
        }

        return implode(' | ', $rules);
    }

    /**
     * Check if ad should be displayed based on targeting
     */
    public function shouldDisplay($userAgent = null, $currentHour = null, $referrer = null): bool
    {
        if (!$this->targeting) {
            return true;
        }

        // Device targeting
        if (isset($this->targeting['device'])) {
            $isMobile = $userAgent ? $this->isMobileDevice($userAgent) : false;
            $targetDevice = $this->targeting['device'];
            
            if (($targetDevice === 'mobile' && !$isMobile) || 
                ($targetDevice === 'desktop' && $isMobile)) {
                return false;
            }
        }

        // Time targeting
        if (isset($this->targeting['hours']) && $currentHour !== null) {
            if (!in_array($currentHour, $this->targeting['hours'])) {
                return false;
            }
        }

        // Add more targeting logic as needed

        return true;
    }

    /**
     * Check if device is mobile
     */
    private function isMobileDevice(string $userAgent): bool
    {
        return preg_match('/Mobile|Android|iPhone|iPad/', $userAgent) === 1;
    }

    /**
     * Get status for display
     */
public function getStatusAttribute(): string
{
    if (!$this->is_active) {
        return 'inactive';
    }

    $now = now();
    
    if ($this->start_at && $this->start_at->gt($now)) {
        return 'scheduled';
    }

    if ($this->end_at && $this->end_at->lt($now)) {
        return 'expired';
    }

    return 'active';
}
}

?>
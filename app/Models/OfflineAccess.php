<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class OfflineAccess extends Model
{
    use HasFactory;

    protected $table = 'offline_access';

    protected $fillable = [
        'user_id',
        'attachment_id',
        'device_id',
        'device_name',
        'expires_at',
        'access_token',
        'is_active'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the offline access.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachment that owns the offline access.
     */
    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    /**
     * Scope a query to only include active offline access.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('expires_at', '>', now());
    }

    /**
     * Scope a query to only include expired offline access.
     */
    public function scopeExpired($query)
    {
        return $query->where('expires_at', '<=', now())
                    ->orWhere('is_active', false);
    }

    /**
     * Scope a query to only include offline access for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include offline access for a specific device.
     */
    public function scopeForDevice($query, $deviceId)
    {
        return $query->where('device_id', $deviceId);
    }

    /**
     * Check if offline access is expired.
     */
    public function getIsExpiredAttribute()
    {
        return $this->expires_at->isPast() || !$this->is_active;
    }

    /**
     * Check if offline access is valid (active and not expired).
     */
    public function getIsValidAttribute()
    {
        return $this->is_active && $this->expires_at->isFuture();
    }

    /**
     * Get days until expiration.
     */
    public function getDaysUntilExpirationAttribute()
    {
        return now()->diffInDays($this->expires_at, false);
    }

    /**
     * Get formatted expiration date.
     */
    public function getFormattedExpiresAtAttribute()
    {
        return $this->expires_at->format('M j, Y \a\t g:i A');
    }

    /**
     * Get device information.
     */
    public function getDeviceInfoAttribute()
    {
        return [
            'device_id' => $this->device_id,
            'device_name' => $this->device_name,
            'last_used' => $this->updated_at->format('M j, Y'),
            'status' => $this->is_valid ? 'active' : ($this->is_expired ? 'expired' : 'inactive')
        ];
    }

    /**
     * Revoke offline access.
     */
    public function revoke()
    {
        $this->update([
            'is_active' => false,
            'expires_at' => now() // Set to now to immediately expire
        ]);

        return $this;
    }

    /**
     * Extend offline access.
     */
    public function extend($days = 30)
    {
        $this->update([
            'expires_at' => now()->addDays($days),
            'is_active' => true
        ]);

        return $this;
    }

    /**
     * Check if device limit is reached for user.
     */
    public static function isDeviceLimitReached($userId, $maxDevices = 3)
    {
        return self::active()
                    ->forUser($userId)
                    ->count() >= $maxDevices;
    }

    /**
     * Get active devices for user.
     */
    public static function getActiveDevices($userId)
    {
        return self::active()
                    ->forUser($userId)
                    ->with('attachment')
                    ->get()
                    ->map(function($access) {
                        return $access->device_info;
                    });
    }
}
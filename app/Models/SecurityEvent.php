<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityEvent extends Model
{
    use HasFactory;

    protected $table = 'security_events';

    protected $fillable = [
        'user_id',
        'event_type',
        'ip_address',
        'user_agent',
        'event_data',
        'occurred_at'
    ];

    protected $casts = [
        'event_data' => 'array',
        'occurred_at' => 'datetime',
    ];

    // Event types constants
    const EVENT_DOWNLOAD_ATTEMPT = 'download_attempt';
    const EVENT_MULTIPLE_STREAMS = 'multiple_streams';
    const EVENT_UNAUTHORIZED_ACCESS = 'unauthorized_access';
    const EVENT_GEO_BLOCK = 'geo_block';
    const EVENT_RATE_LIMIT_EXCEEDED = 'rate_limit_exceeded';
    const EVENT_SUSPICIOUS_ACTIVITY = 'suspicious_activity';
    const EVENT_DEVICE_LIMIT_EXCEEDED = 'device_limit_exceeded';
    const EVENT_TOKEN_EXPIRED = 'token_expired';
    const EVENT_IP_MISMATCH = 'ip_mismatch';
    const EVENT_CONTENT_SCRAPING = 'content_scraping';

    /**
     * Get the user that owns the security event.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include events of a specific type.
     */
    public function scopeOfType($query, $eventType)
    {
        return $query->where('event_type', $eventType);
    }

    /**
     * Scope a query to only include events for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include events from a specific IP address.
     */
    public function scopeFromIp($query, $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope a query to only include events within a date range.
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('occurred_at', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include recent events (last 24 hours).
     */
    public function scopeRecent($query, $hours = 24)
    {
        return $query->where('occurred_at', '>=', now()->subHours($hours));
    }

    /**
     * Get event severity level.
     */
    public function getSeverityAttribute()
    {
        $severityMap = [
            self::EVENT_DOWNLOAD_ATTEMPT => 'high',
            self::EVENT_UNAUTHORIZED_ACCESS => 'high',
            self::EVENT_CONTENT_SCRAPING => 'high',
            self::EVENT_MULTIPLE_STREAMS => 'medium',
            self::EVENT_GEO_BLOCK => 'medium',
            self::EVENT_DEVICE_LIMIT_EXCEEDED => 'medium',
            self::EVENT_RATE_LIMIT_EXCEEDED => 'low',
            self::EVENT_SUSPICIOUS_ACTIVITY => 'low',
            self::EVENT_TOKEN_EXPIRED => 'low',
            self::EVENT_IP_MISMATCH => 'low',
        ];

        return $severityMap[$this->event_type] ?? 'low';
    }

    /**
     * Get formatted event description.
     */
    public function getDescriptionAttribute()
    {
        $descriptions = [
            self::EVENT_DOWNLOAD_ATTEMPT => 'Attempted to download protected content',
            self::EVENT_MULTIPLE_STREAMS => 'Multiple simultaneous streams detected',
            self::EVENT_UNAUTHORIZED_ACCESS => 'Unauthorized access attempt',
            self::EVENT_GEO_BLOCK => 'Access blocked due to geographic restrictions',
            self::EVENT_RATE_LIMIT_EXCEEDED => 'Rate limit exceeded for streaming',
            self::EVENT_SUSPICIOUS_ACTIVITY => 'Suspicious activity detected',
            self::EVENT_DEVICE_LIMIT_EXCEEDED => 'Device limit exceeded for offline access',
            self::EVENT_TOKEN_EXPIRED => 'Access token expired',
            self::EVENT_IP_MISMATCH => 'IP address mismatch detected',
            self::EVENT_CONTENT_SCRAPING => 'Potential content scraping detected',
        ];

        return $descriptions[$this->event_type] ?? 'Security event occurred';
    }

    /**
     * Get event icon based on type.
     */
    public function getIconAttribute()
    {
        $icons = [
            self::EVENT_DOWNLOAD_ATTEMPT => 'fa-download',
            self::EVENT_MULTIPLE_STREAMS => 'fa-stream',
            self::EVENT_UNAUTHORIZED_ACCESS => 'fa-user-lock',
            self::EVENT_GEO_BLOCK => 'fa-globe',
            self::EVENT_RATE_LIMIT_EXCEEDED => 'fa-tachometer-alt',
            self::EVENT_SUSPICIOUS_ACTIVITY => 'fa-exclamation-triangle',
            self::EVENT_DEVICE_LIMIT_EXCEEDED => 'fa-mobile-alt',
            self::EVENT_TOKEN_EXPIRED => 'fa-key',
            self::EVENT_IP_MISMATCH => 'fa-network-wired',
            self::EVENT_CONTENT_SCRAPING => 'fa-code',
        ];

        return $icons[$this->event_type] ?? 'fa-shield-alt';
    }

    /**
     * Get color class based on severity.
     */
    public function getColorClassAttribute()
    {
        $colors = [
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'info'
        ];

        return $colors[$this->severity] ?? 'secondary';
    }

    /**
     * Check if event requires immediate attention.
     */
    public function getRequiresAttentionAttribute()
    {
        return $this->severity === 'high' || 
               ($this->severity === 'medium' && $this->occurred_at->diffInHours(now()) < 1);
    }

    /**
     * Log a new security event.
     */
    public static function log($eventType, $userId = null, $eventData = [])
    {
        return self::create([
            'user_id' => $userId,
            'event_type' => $eventType,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'event_data' => $eventData,
            'occurred_at' => now()
        ]);
    }

    /**
     * Get security events summary for dashboard.
     */
    public static function getSecuritySummary($days = 7)
    {
        $startDate = now()->subDays($days);

        return [
            'total_events' => self::where('occurred_at', '>=', $startDate)->count(),
            'high_severity' => self::where('occurred_at', '>=', $startDate)
                                ->whereIn('event_type', [
                                    self::EVENT_DOWNLOAD_ATTEMPT,
                                    self::EVENT_UNAUTHORIZED_ACCESS,
                                    self::EVENT_CONTENT_SCRAPING
                                ])->count(),
            'suspicious_ips' => self::where('occurred_at', '>=', $startDate)
                                ->distinct('ip_address')
                                ->count('ip_address'),
            'blocked_users' => self::where('occurred_at', '>=', $startDate)
                                ->whereIn('event_type', [
                                    self::EVENT_UNAUTHORIZED_ACCESS,
                                    self::EVENT_GEO_BLOCK
                                ])
                                ->distinct('user_id')
                                ->count('user_id')
        ];
    }

    /**
     * Check if IP address is suspicious.
     */
    public static function isSuspiciousIp($ipAddress, $threshold = 5)
    {
        return self::fromIp($ipAddress)
                    ->recent(24) // Last 24 hours
                    ->count() >= $threshold;
    }

    /**
     * Get events grouped by type for charting.
     */
    public static function getEventsByType($days = 30)
    {
        $startDate = now()->subDays($days);

        return self::where('occurred_at', '>=', $startDate)
                    ->selectRaw('event_type, COUNT(*) as count')
                    ->groupBy('event_type')
                    ->orderBy('count', 'desc')
                    ->get()
                    ->mapWithKeys(function($item) {
                        return [$item->event_type => $item->count];
                    })
                    ->toArray();
    }
}
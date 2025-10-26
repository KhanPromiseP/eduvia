<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoProgress extends Model
{
    use HasFactory;

    protected $table = 'video_progress';

    protected $fillable = [
        'user_id',
        'attachment_id',
        'current_time',
        'total_duration',
        'progress_percentage',
        'total_watched_time',
        'completed',
        'completed_at',
        'last_watched_at',
        'session_id',
        'quality'
    ];

    protected $casts = [
        'current_time' => 'decimal:3',
        'total_duration' => 'decimal:3',
        'progress_percentage' => 'decimal:2',
        'total_watched_time' => 'decimal:2',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_watched_at' => 'datetime',
    ];

    /**
     * Get the user that owns the video progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the attachment that owns the video progress.
     */
    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }

    /**
     * Scope a query to only include completed progress.
     */
    public function scopeCompleted($query)
    {
        return $query->where('completed', true);
    }

    /**
     * Scope a query to only include progress for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include progress for a specific attachment.
     */
    public function scopeForAttachment($query, $attachmentId)
    {
        return $query->where('attachment_id', $attachmentId);
    }

    /**
     * Get formatted current time (MM:SS)
     */
    public function getFormattedCurrentTimeAttribute()
    {
        $minutes = floor($this->current_time / 60);
        $seconds = floor($this->current_time % 60);
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get formatted total duration (MM:SS)
     */
    public function getFormattedTotalDurationAttribute()
    {
        $minutes = floor($this->total_duration / 60);
        $seconds = floor($this->total_duration % 60);
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Check if video is fully watched (95% or more)
     */
    public function getIsFullyWatchedAttribute()
    {
        return $this->progress_percentage >= 95;
    }

    /**
     * Get estimated time remaining
     */
    public function getTimeRemainingAttribute()
    {
        return max(0, $this->total_duration - $this->current_time);
    }

    /**
     * Get formatted time remaining (MM:SS)
     */
    public function getFormattedTimeRemainingAttribute()
    {
        $minutes = floor($this->time_remaining / 60);
        $seconds = floor($this->time_remaining % 60);
        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
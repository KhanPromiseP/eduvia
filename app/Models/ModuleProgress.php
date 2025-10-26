<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleProgress extends Model
{
    use HasFactory;

    protected $table = 'module_progress';

    protected $fillable = [
        'user_id',
        'course_module_id',
        'completed_attachments',
        'total_attachments',
        'progress_percentage',
        'completed',
        'completed_at',
        'last_accessed_at'
    ];

    protected $casts = [
        'progress_percentage' => 'decimal:2',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_accessed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the module progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module that owns the progress.
     */
    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'course_module_id');
    }

    /**
     * Get the course through the module.
     */
    public function course()
    {
        return $this->hasOneThrough(Course::class, CourseModule::class, 'id', 'id', 'course_module_id', 'course_id');
    }

    /**
     * Scope a query to only include completed modules.
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
     * Scope a query to only include progress for a specific module.
     */
    public function scopeForModule($query, $moduleId)
    {
        return $query->where('course_module_id', $moduleId);
    }

    /**
     * Get remaining attachments count
     */
    public function getRemainingAttachmentsAttribute()
    {
        return $this->total_attachments - $this->completed_attachments;
    }

    /**
     * Check if module has any attachments
     */
    public function getHasAttachmentsAttribute()
    {
        return $this->total_attachments > 0;
    }

    /**
     * Get progress as a fraction (e.g., 3/5)
     */
    public function getProgressFractionAttribute()
    {
        return $this->completed_attachments . '/' . $this->total_attachments;
    }

    /**
     * Update progress based on completed attachments
     */
    public function updateProgress()
    {
        $this->completed_attachments = $this->module->attachments()
            ->whereHas('videoProgress', function($query) {
                $query->where('user_id', $this->user_id)
                      ->where('completed', true);
            })
            ->count();

        $this->total_attachments = $this->module->attachments()->count();
        
        $this->progress_percentage = $this->total_attachments > 0 
            ? ($this->completed_attachments / $this->total_attachments) * 100 
            : 0;

        $this->completed = $this->completed_attachments >= $this->total_attachments && $this->total_attachments > 0;
        
        if ($this->completed && !$this->completed_at) {
            $this->completed_at = now();
        }

        $this->last_accessed_at = now();
        $this->save();

        return $this;
    }

    /**
     * Check if module is started (any progress)
     */
    public function getIsStartedAttribute()
    {
        return $this->completed_attachments > 0 || $this->progress_percentage > 0;
    }
}
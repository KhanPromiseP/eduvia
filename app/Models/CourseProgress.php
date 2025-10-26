<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseProgress extends Model
{
    use HasFactory;

    protected $table = 'course_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'completed_modules',
        'total_modules',
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
     * Get the user that owns the course progress.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the course that owns the progress.
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Get the module progress records for this course.
     */
    public function moduleProgress()
    {
        return $this->hasMany(ModuleProgress::class, 'course_module_id')
                    ->whereHas('module', function($query) {
                        $query->where('course_id', $this->course_id);
                    });
    }

    /**
     * Scope a query to only include completed courses.
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
     * Scope a query to only include progress for a specific course.
     */
    public function scopeForCourse($query, $courseId)
    {
        return $query->where('course_id', $courseId);
    }

    /**
     * Get remaining modules count
     */
    public function getRemainingModulesAttribute()
    {
        return $this->total_modules - $this->completed_modules;
    }

    /**
     * Check if course has any modules
     */
    public function getHasModulesAttribute()
    {
        return $this->total_modules > 0;
    }

    /**
     * Get progress as a fraction (e.g., 3/5)
     */
    public function getProgressFractionAttribute()
    {
        return $this->completed_modules . '/' . $this->total_modules;
    }

    /**
     * Update progress based on completed modules
     */
    public function updateProgress()
    {
        $this->completed_modules = $this->course->modules()
            ->whereHas('moduleProgress', function($query) {
                $query->where('user_id', $this->user_id)
                      ->where('completed', true);
            })
            ->count();

        $this->total_modules = $this->course->modules()->count();
        
        $this->progress_percentage = $this->total_modules > 0 
            ? ($this->completed_modules / $this->total_modules) * 100 
            : 0;

        $this->completed = $this->completed_modules >= $this->total_modules && $this->total_modules > 0;
        
        if ($this->completed && !$this->completed_at) {
            $this->completed_at = now();
        }

        $this->last_accessed_at = now();
        $this->save();

        return $this;
    }

    /**
     * Check if course is started (any progress)
     */
    public function getIsStartedAttribute()
    {
        return $this->completed_modules > 0 || $this->progress_percentage > 0;
    }

    /**
     * Get estimated time to complete based on average progress
     */
    public function getEstimatedTimeToCompleteAttribute()
    {
        if ($this->progress_percentage <= 0) {
            return null;
        }

        $timeSpent = $this->last_accessed_at->diffInHours($this->created_at);
        $completionRate = $this->progress_percentage / 100;
        
        if ($completionRate > 0) {
            $totalEstimatedTime = $timeSpent / $completionRate;
            $remainingTime = $totalEstimatedTime - $timeSpent;
            return max(0, $remainingTime);
        }

        return null;
    }

    /**
     * Get progress level (beginner, intermediate, advanced, expert)
     */
    public function getProgressLevelAttribute()
    {
        if ($this->progress_percentage >= 90) return 'expert';
        if ($this->progress_percentage >= 70) return 'advanced';
        if ($this->progress_percentage >= 40) return 'intermediate';
        if ($this->progress_percentage >= 10) return 'beginner';
        return 'not_started';
    }
}
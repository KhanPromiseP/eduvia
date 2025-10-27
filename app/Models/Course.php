<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id', 'title', 'description', 'objectives', 'target_audience', 
        'requirements', 'price', 'image', 'duration', 'level', 'is_published', 
        'is_premium', 'user_id', 'status', 'reviewed_by', 'review_notes', 'reviewed_at'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'is_premium' => 'boolean',
        'reviewed_at' => 'datetime',
        'price' => 'decimal:2',
    ];

    // Course statuses
    const STATUS_DRAFT = 'draft';
    const STATUS_PENDING_REVIEW = 'pending_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    // Relationships
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function instructor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     /**
     * Relationship with user courses (enrollments)
     */
    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    /**
     * Relationship with payments through user courses
     */
    public function payments()
    {
        return $this->hasManyThrough(Payment::class, UserCourse::class, 'course_id', 'id', 'id', 'payment_id');
    }

     /**
     * Relationship with instructor earnings
     */
    public function instructorEarnings()
    {
        return $this->hasMany(InstructorEarning::class, 'course_id');
    }

    /**
     * Scope for courses created by a specific user
     */
    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function attachments()
    {
        return $this->hasManyThrough(Attachment::class, CourseModule::class);
    }

    public function freeModules()
    {
        return $this->hasMany(CourseModule::class)
                    ->where('is_free', true)
                    ->orderBy('order');
    }

    // Scopes for filtering
    public function scopePublished($query)
    {
        return $query->where('is_published', true)
                    ->where('status', self::STATUS_APPROVED);
    }

    public function scopePendingReview($query)
    {
        return $query->where('status', self::STATUS_PENDING_REVIEW);
    }

    public function scopeDraft($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', self::STATUS_APPROVED);
    }

    public function scopeRejected($query)
    {
        return $query->where('status', self::STATUS_REJECTED);
    }

    public function scopeByInstructor($query, $instructorId)
    {
        return $query->where('user_id', $instructorId);
    }

    public function scopeWithModulesCount($query)
    {
        return $query->withCount('modules');
    }

    // Helper methods
    public function isDraft()
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isPendingReview()
    {
        return $this->status === self::STATUS_PENDING_REVIEW;
    }

    public function isApproved()
    {
        return $this->status === self::STATUS_APPROVED;
    }

    public function isRejected()
    {
        return $this->status === self::STATUS_REJECTED;
    }

    public function canBePublished()
    {
        return $this->isApproved() && $this->is_published;
    }

    public function isUnderReview()
    {
        return $this->isPendingReview();
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            self::STATUS_DRAFT => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Draft</span>',
            self::STATUS_PENDING_REVIEW => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Under Review</span>',
            self::STATUS_APPROVED => $this->is_published 
                ? '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Published</span>'
                : '<span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">Approved</span>',
            self::STATUS_REJECTED => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Rejected</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Unknown</span>'
        };
    }

    public function getStatusTextAttribute()
    {
        if ($this->isApproved() && $this->is_published) {
            return 'Published';
        } elseif ($this->isApproved()) {
            return 'Approved';
        }

        return match($this->status) {
            self::STATUS_DRAFT => 'Draft',
            self::STATUS_PENDING_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected',
            default => 'Unknown'
        };
    }

    // New methods for review system
    public function canBeSubmittedForReview()
    {
        return $this->isDraft() || $this->isRejected() && $this->modules()->count() > 0;
    }

    // public function canBeEditedByInstructor()
    // {
    //     return $this->isDraft() || $this->isRejected();
    // }

    public function requiresReview()
    {
        return $this->isPendingReview();
    }

    public function isVisibleToStudents()
    {
        return $this->is_published && $this->isApproved();
    }

    /**
     * Get formatted review notes with line breaks
     */
    public function getFormattedReviewNotesAttribute()
    {
        return $this->review_notes ? nl2br(e($this->review_notes)) : null;
    }

    /**
     * Check if course has been reviewed
     */
    public function hasBeenReviewed()
    {
        return !is_null($this->reviewed_at);
    }


    /**
     * Get the users enrolled in this course
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')
                    ->withPivot('amount_paid', 'purchased_at')
                    ->withTimestamps();
    }

    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'user_courses')
                    ->withTimestamps()
                    ->distinct();
    }

    public function enrollments()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function totalEnrollments()
    {
        return $this->enrollments()->count();
    }

    public function totalRevenue()
    {
        return $this->enrollments()->sum('amount_paid');
    }


    public function isPurchasedBy($user)
    {
        if (!$user) {
            return false;
        }

        // Check if the user is enrolled in this course
        return $this->users()->where('user_id', $user->id)->exists();
    }

    public function userProgress()
{
    return $this->hasManyThrough(
        \App\Models\UserProgress::class,
        \App\Models\CourseModule::class,
        'course_id',     // Foreign key on course_modules table
        'module_id',     // Foreign key on user_progress table
        'id',            // Local key on courses table
        'id'             // Local key on course_modules table
    );
}


public function progressPercentage($userId)
{
    // Get total modules in this course
    $totalModules = $this->modules()->count();

    if ($totalModules === 0) {
        return 0;
    }

    // Count modules completed by this user
    $completedModules = $this->modules()
        ->whereHas('progress', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('completed', true);
        })
        ->count();

    // Calculate percentage
    return round(($completedModules / $totalModules) * 100);
}


public function reviews()
{
    return $this->hasMany(Review::class);
}

public function approvedReviews()
{
    return $this->hasMany(Review::class)->approved();
}

// In Course model
public function updateRatingStats()
{
    // Use fresh database query to get approved reviews
    $reviews = $this->reviews()->where('is_approved', true);
    
    $totalReviews = $reviews->count();
    $averageRating = $totalReviews > 0 ? $reviews->avg('rating') : 0;

    // Calculate rating breakdown
    $breakdown = [];
    for ($i = 1; $i <= 5; $i++) {
        $count = $reviews->where('rating', $i)->count();
        $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        
        $breakdown[$i] = [
            'count' => $count,
            'percentage' => $percentage
        ];
    }

    // Update the course directly using DB query to avoid model events
    \DB::table('courses')
        ->where('id', $this->id)
        ->update([
            'average_rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews,
            'rating_breakdown' => json_encode($breakdown),
            'updated_at' => now(),
        ]);

    // Refresh this model instance
    $this->refresh();
}

// Accessor for rating breakdown
public function getRatingBreakdownAttribute($value)
{
    if ($value) {
        $breakdown = json_decode($value, true);
        
        foreach ($breakdown as $rating => $data) {
            if (!isset($data['count']) || !isset($data['percentage'])) {
               
                $breakdown = $this->calculateFreshBreakdown();
                break;
            }
        }
        
        return $breakdown;
    }
    
    return $this->calculateFreshBreakdown();
}


protected function calculateFreshBreakdown()
{
    $reviews = $this->approvedReviews();
    $totalReviews = $reviews->count();
    
    $breakdown = [];
    for ($i = 1; $i <= 5; $i++) {
        $count = $reviews->where('rating', $i)->count();
        $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
        
        $breakdown[$i] = [
            'count' => $count,
            'percentage' => $percentage
        ];
    }
    
    return $breakdown;
}

public function getTotalEnrollmentsAttribute()
{
    return $this->enrollments()->count();
}

}
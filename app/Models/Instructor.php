<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id','headline','bio','languages','skills',
        'rating','total_students','total_reviews','is_verified', 
        'suspended_at', 'suspension_reason'
    ];

    protected $casts = [
        'languages' => 'array',
        'skills' => 'array',
        'suspended_at' => 'datetime',
        'rating' => 'decimal:2',
        'total_students' => 'integer',
        'total_reviews' => 'integer'
    ];

    protected $appends = ['status', 'total_earnings', 'pending_earnings', 'processed_earnings'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payouts()
    {
        return $this->hasOne(InstructorPayout::class);
    }

    public function documents()
    {
        return $this->hasMany(InstructorDocument::class);
    }

    public function isSuspended()
    {
        return !is_null($this->suspended_at);
    }

    public function getStatusAttribute()
    {
        if ($this->isSuspended()) {
            return 'suspended';
        }
        return $this->is_verified ? 'active' : 'pending';
    }

    // Check if instructor is active (verified and not suspended)
    public function isActive()
    {
        return $this->is_verified && !$this->isSuspended();
    }

    // Scope for active instructors
    public function scopeActive($query)
    {
        return $query->where('is_verified', true)->whereNull('suspended_at');
    }

    // Scope for suspended instructors
    public function scopeSuspended($query)
    {
        return $query->whereNotNull('suspended_at');
    }

    // FIXED: Correct relationship for courses
 public function courses()
{
    return $this->hasMany(Course::class, 'user_id', 'user_id');
}

    // Get all enrollments for instructor's courses
    // public function enrollments()
    // {
    //     return $this->hasManyThrough(UserCourse::class, Course::class, 'instructor_id', 'course_id');
    // }

    public function enrollments()
{
    return $this->hasManyThrough(
        \App\Models\UserCourse::class, 
        \App\Models\Course::class,
        'user_id', // Foreign key on courses table (instructor's user_id)
        'course_id', // Foreign key on user_courses table
        'user_id', // Local key on instructors table
        'id' // Local key on courses table
    );
}

    // Get unique students across all courses
    public function students()
    {
        $courseIds = $this->courses()->pluck('id');
        return User::whereHas('enrollments', function($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds);
        })->distinct();
    }

    // Calculate total students count
public function getTotalStudentsAttribute()
{
    if ($this->attributes['total_students'] !== null) {
        return $this->attributes['total_students'];
    }

    // Calculate and cache if not set - use user_id to find courses
    $courseIds = Course::where('user_id', $this->user_id)->pluck('id');
    $total = \App\Models\UserCourse::whereIn('course_id', $courseIds)
        ->distinct('user_id')
        ->count('user_id');
    
    $this->update(['total_students' => $total]);
    
    return $total;
}

// Calculate total reviews count 
public function getTotalReviewsAttribute()
{
    if ($this->attributes['total_reviews'] !== null) {
        return $this->attributes['total_reviews'];
    }

    $courseIds = Course::where('user_id', $this->user_id)->pluck('id');
    $total = \App\Models\Review::whereIn('course_id', $courseIds)->count();
    
    $this->update(['total_reviews' => $total]);
    
    return $total;
}

// Calculate average rating
public function getRatingAttribute()
{
    if ($this->attributes['rating'] !== null) {
        return $this->attributes['rating'];
    }

    $courseIds = Course::where('user_id', $this->user_id)->pluck('id');
    $avgRating = \App\Models\Review::whereIn('course_id', $courseIds)->avg('rating') ?? 0;
    
    $this->update(['rating' => $avgRating]);
    
    return $avgRating;
}

// Update instructor statistics (call this periodically) 
public function updateStatistics()
{
    $courseIds = Course::where('user_id', $this->user_id)->pluck('id');
    
    $totalStudents = \App\Models\UserCourse::whereIn('course_id', $courseIds)
        ->distinct('user_id')
        ->count('user_id');
        
    $totalReviews = \App\Models\Review::whereIn('course_id', $courseIds)->count();
    $avgRating = \App\Models\Review::whereIn('course_id', $courseIds)->avg('rating') ?? 0;

    $this->update([
        'total_students' => $totalStudents,
        'total_reviews' => $totalReviews,
        'rating' => $avgRating,
    ]);

    return $this;
}

    public function earnings()
    {
        return $this->hasMany(InstructorEarning::class);
    }

    public function refundRequests()
    {
        return $this->hasManyThrough(RefundRequest::class, Course::class, 'instructor_id', 'course_id');
    }


    public function getTotalEarningsAttribute()
    {
        return $this->earnings()->where('status', InstructorEarning::STATUS_PAID_OUT)->sum('amount');
    }

  
    public function getPendingEarningsAttribute()
    {
        return $this->earnings()->forPayout()->sum('amount');
    }

    // get processed earnings (ready for payout)
    public function getProcessedEarningsAttribute()
    {
        return $this->earnings()->where('status', InstructorEarning::STATUS_PROCESSED)->sum('amount');
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'instructor_followers', 'instructor_id', 'user_id')
                    ->withTimestamps()
                    ->whereNotNull('users.id'); // Ensure user exists
    }

    // Get recent enrollments
    public function recentEnrollments($limit = 10)
    {
        return $this->enrollments()->with('user', 'course')->latest()->take($limit)->get();
    }

    // Get top performing courses
    public function topPerformingCourses($limit = 5)
    {
        return $this->courses()
            ->withCount(['enrollments', 'reviews'])
            ->withAvg('reviews', 'rating')
            ->orderBy('enrollments_count', 'desc')
            ->take($limit)
            ->get();
    }

 
    // Get monthly earnings
    public function monthlyEarnings($months = 12)
    {
        return $this->earnings()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(amount) as total')
            ->where('status', InstructorEarning::STATUS_PAID_OUT)
            ->groupBy('year', 'month')
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->take($months)
            ->get();
    }
}
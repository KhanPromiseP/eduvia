<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
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
    ];

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

    public function courses()
    {
        return $this->hasMany(Course::class, 'user_id', 'user_id');
    }

    // FIXED: Get enrolled students through courses
    public function enrolledStudents()
    {
        $courseIds = $this->courses()->pluck('id');
        
        return User::whereHas('userCourses', function($query) use ($courseIds) {
            $query->whereIn('course_id', $courseIds);
        })->distinct();
    }

    // Calculate total students count
    public function getTotalStudentsAttribute()
    {
        $courseIds = $this->courses()->pluck('id');
        return UserCourse::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');
    }
}
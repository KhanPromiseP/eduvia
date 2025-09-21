<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            // REMOVED: 'purchased_at' => 'datetime', // This field is in user_courses table, not users table
        ];
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
                    ->using(UserCourse::class) // Add this to use the UserCourse model for pivot
                    ->withPivot('amount_paid', 'purchased_at', 'payment_id')
                    ->withTimestamps();
    }

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function hasPurchased($course)
    {
        if (!$this->id) {
            return false;
        }
        
        // Handle both course object and course ID
        $courseId = is_object($course) ? $course->id : $course;
        
        // If we already loaded the courses relationship, use it
        if ($this->relationLoaded('courses')) {
            return $this->courses->contains('id', $courseId);
        }
        
        // Otherwise, query the database
        return $this->courses()->where('course_id', $courseId)->exists();
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function hasPaid($productId)
    {
        return $this->payments()
                    ->where('product_id', $productId)
                    ->where('status', 'completed')
                    ->exists();
    }
}
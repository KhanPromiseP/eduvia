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
        'country',
        'city',
        'preferred_language',
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
        ];
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class);
    }


    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses')
                    ->using(UserCourse::class)
                    ->withPivot('amount_paid', 'purchased_at', 'payment_id')
                    ->withTimestamps();
    }

    public function userCourses()
    {
        return $this->hasMany(UserCourse::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    public function hasPurchased($course)
    {
        if (!$this->id) {
            return false;
        }
        
        $courseId = is_object($course) ? $course->id : $course;
        
        if ($this->relationLoaded('courses')) {
            return $this->courses->contains('id', $courseId);
        }
        
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

    public function modules()
    {
        return $this->belongsToMany(CourseModule::class, 'course_module_user', 'user_id', 'module_id')
                    ->withPivot('completed', 'viewed_at')
                    ->withTimestamps();
    }


}
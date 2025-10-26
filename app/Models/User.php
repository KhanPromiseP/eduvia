<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    use HasRoles;

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
        'role',
        'profile_path',
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


    
    /**
     * A user can have many roles.
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
                    ->withTimestamps();
    }



    /**
     * Assign a role to the user.
     */
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }

        return $this->roles()->syncWithoutDetaching([$role->id]);
    }

    /**
     * Remove a role from the user.
     */
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->first();
        }

        return $this->roles()->detach($role);
    }

    

     public function instructor()
    {
        return $this->hasOne(Instructor::class);
    }

    public function applications()
    {
        return $this->hasMany(InstructorApplication::class);
    }

    /**
     * Check if user is admin (both old and new methods)
     */
    public function isAdmin()
    {
        // Check both old is_admin column and new role system
        return $this->is_admin || $this->hasRole('admin');
    }

/**
 * Check if user has a specific role.
 */
public function hasRole($role)
{
    if (!$this->relationLoaded('roles')) {
        return $this->roles()->where('name', $role)->exists();
    }
    
    return $this->roles->contains('name', $role);
}

/**
 * Check if user has any of the given roles.
 */
public function hasAnyRole($roles)
{
    if (!is_array($roles)) {
        $roles = [$roles];
    }
    
    if (!$this->relationLoaded('roles')) {
        return $this->roles()->whereIn('name', $roles)->exists();
    }
    
    return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
}


public function isInstructor()
{
    return $this->role === 'instructor';
}

public function isSupervisor()
{
    return $this->role === 'supervisor';
}


public function isActiveInstructor()
{
    if (!$this->hasRole('instructor')) {
        return false;
    }

    $instructor = $this->instructor;
    return $instructor && $instructor->isActive();
}




public function instructorReviews()
{
    return $this->hasMany(InstructorReview::class);
}

public function sentMessages()
{
    return $this->hasMany(InstructorMessage::class);
}


public function isFollowingInstructor($instructorId)
{
    return \Illuminate\Support\Facades\DB::table('instructor_followers')
        ->where('instructor_id', $instructorId)
        ->where('user_id', $this->id)
        ->exists();
}


public function hasEnrolledInInstructorCourse($instructorId)
{
    // Get the instructor to find their user_id
    $instructor = \App\Models\Instructor::find($instructorId);
    if (!$instructor) {
        return false;
    }

    // Use user_id since courses are linked to users
    $instructorCourseIds = \App\Models\Course::where('user_id', $instructor->user_id)
        ->where('is_published', true)
        ->pluck('id');
    
    if ($instructorCourseIds->isEmpty()) {
        return false;
    }
    
    return \DB::table('user_courses')
        ->where('user_id', $this->id)
        ->whereIn('course_id', $instructorCourseIds)
        ->exists();
}

public function getCourseProgress($course)
{
    // Example: assuming you have a pivot table "course_user" with a 'progress' column
    $record = $this->courses()->where('course_id', $course->id)->first();

    if ($record) {
        // Adjust this depending on how you store progress
        return $record->pivot->progress ?? 0;
    }

    return 0;
}



public function reviews()
{
    return $this->hasMany(Review::class);
}

public function hasReviewed($courseId)
{
    return $this->reviews()->where('course_id', $courseId)->exists();
}




/**
 * Define relationship with courses
 */
// public function courses()
// {
//     return $this->belongsToMany(Course::class)->withPivot('progress')->withTimestamps();
// }

}
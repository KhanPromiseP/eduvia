<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

     protected $fillable = [
        'category_id', 'title', 'description', 'objectives', 'target_audience', 
        'requirements', 'price', 'image', 'duration', 'level', 'is_published', 'is_premium'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // Scopes for filtering
    public function scopePremium($query)
    {
        return $query->where('is_premium', true);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function modules()
    {
        return $this->hasMany(CourseModule::class)->orderBy('order');
    }

    public function freeModules()
    {
        return $this->hasMany(CourseModule::class)
                    ->where('is_free', true)
                    ->orderBy('order');
    }

    public function firstModule()
    {
        return $this->hasOne(CourseModule::class)->orderBy('order');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_courses')
                    ->withPivot('amount_paid', 'purchased_at')
                    ->withTimestamps();
    }

    public function isPurchasedBy(User $user = null)
    {
        if (!$user) {
            return false;
        }
        
        return $this->users()->where('user_id', $user->id)->exists();
    }


    public function userProgress()
    {
        return $this->hasManyThrough(
            UserProgress::class,
            CourseModule::class,
            'course_id',   // FK on course_modules
            'module_id',   // FK on user_progress
            'id',          // PK on courses
            'id'           // PK on course_modules
        );
    }

    public function completedModulesForUser($userId)
    {
        return $this->userProgress()
            ->where('user_id', $userId)
            ->where('completed', true)
            ->count();
    }

    // public function progressPercentage($userId)
    // {
    //     return cache()->remember(
    //         "course_{$this->id}_progress_{$userId}",
    //         now()->addMinutes(5),
    //         function () use ($userId) {
    //             $totalModules = $this->modules->count();
    //             if ($totalModules === 0) return 0;

    //             $completedModules = $this->completedModulesForUser($userId);
    //             return round(($completedModules / $totalModules) * 100, 2);
    //         }
    //     );
    // }

public function progressPercentage($userId)
{
    $totalModules = $this->modules()->count();
    if ($totalModules === 0) return 0;

    $completedModules = $this->modules()
        ->whereHas('progress', function ($q) use ($userId) {
            $q->where('user_id', $userId)->where('completed', true);
        })
        ->count();

    return round(($completedModules / $totalModules) * 100);
}



    
}
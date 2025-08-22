<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'objectives',
        'target_audience',
        'requirements',
        'price',
        'image',
        'duration',
        'level',
        'is_published'
    ];

    protected $casts = [
        'is_published' => 'boolean',
    ];

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



    
}
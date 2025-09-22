<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseModule extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_free'
    ];

    protected $casts = [
        'is_free' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attachments()
    {
        return $this->hasMany(Attachment::class, 'module_id')->orderBy('order');
    }

    public function progress()
    {
        return $this->hasMany(UserProgress::class, 'module_id');
    }
    

    public function users()
    {
        return $this->belongsToMany(User::class, 'course_module_user', 'module_id', 'user_id')
                    ->withPivot('completed', 'viewed_at')
                    ->withTimestamps();
    }




}
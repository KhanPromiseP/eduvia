<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProgress extends Model
{
    use HasFactory;
    protected $table = 'user_progress';
    protected $fillable = [
        'user_id',
        'module_id',
        'viewed_at',
        'completed',
        'completed_at',
    ];

    protected $casts = [
        'viewed_at' => 'datetime',
        'completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }
}

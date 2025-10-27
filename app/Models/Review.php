<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_id',
        'rating',
        'comment',
        'instructor_response',
        'response_date',
        'is_verified',
        'is_approved',
        'is_helpful'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_approved' => 'boolean',
        'is_helpful' => 'boolean',
        'response_date' => 'datetime',
        'created_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Scopes
    public function scopeApproved($query)
    {
        return $query->where('is_approved', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeWithHighRating($query, $minRating = 4)
    {
        return $query->where('rating', '>=', $minRating);
    }

    public function scopeWithResponse($query)
    {
        return $query->whereNotNull('instructor_response');
    }

    public function scopeWithoutResponse($query)
    {
        return $query->whereNull('instructor_response');
    }

    // Accessors
    public function getHasResponseAttribute()
    {
        return !is_null($this->instructor_response);
    }

    public function getResponseAgeAttribute()
    {
        return $this->response_date ? $this->response_date->diffForHumans() : null;
    }
}
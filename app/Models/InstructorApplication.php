<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;


class InstructorApplication extends Model
{
    use HasFactory;


    const STATUS_PENDING = 'pending';
    const STATUS_DOCUMENTS_UPLOADED = 'uploaded';
    const STATUS_UNDER_REVIEW = 'under_review';
    const STATUS_APPROVED = 'approved';
    const STATUS_REJECTED = 'rejected';

    
    protected $fillable = [
        'user_id','bio','expertise','linkedin_url','website_url',
        'video_intro','status','reviewed_by','review_notes'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class,'reviewed_by');
    }
}

<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Model;



class InstructorDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'instructor_id',
        'document_type',
        'file_path', 
        'status',
        'verified_by'
    ];

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    // Relationships
    public function instructor()
    {
        return $this->belongsTo(Instructor::class);
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    // Helpers
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function getDocumentTypeNameAttribute()
    {
        return match($this->document_type) {
            'id_card' => 'ID Card',
            'passport' => 'Passport',
            'certificate' => 'Certificate',
            default => ucfirst($this->document_type)
        };
    }
}
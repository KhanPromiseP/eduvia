<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'module_id',
        'title',
        'file_path',
        'file_type',
        'file_size',
        'order'
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class);
    }
}
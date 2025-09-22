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
        'description', // Added
        'file_path',
        'file_type',
        'file_size',
        'video_url', // Added
        'thumbnail_url', // Added
        'order'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'order' => 'integer',
    ];

    public function module()
    {
        return $this->belongsTo(CourseModule::class);
    }

    // Helper method to check if attachment is an external video
    public function isExternalVideo()
    {
        return $this->file_type === 'external_video' && !empty($this->video_url);
    }

    // Helper method to check if attachment is a file upload
    public function isFileUpload()
    {
        return !empty($this->file_path) && $this->file_type !== 'external_video';
    }

    // Helper method to get YouTube video ID
    public function getYoutubeVideoId()
    {
        if (!$this->isExternalVideo()) {
            return null;
        }

        $url = $this->video_url;
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);
        
        return $matches[1] ?? null;
    }

    // Helper method to get video thumbnail
    public function getThumbnailUrl()
    {
        if ($this->thumbnail_url) {
            return $this->thumbnail_url;
        }

        if ($this->isExternalVideo() && $youtubeId = $this->getYoutubeVideoId()) {
            return "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg";
        }

        return null;
    }

    // Helper method to get display file type
    public function getDisplayFileType()
    {
        if ($this->isExternalVideo()) {
            return 'External Video';
        }
        
        return strtoupper($this->file_type);
    }
}
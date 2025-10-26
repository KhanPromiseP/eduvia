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
        'description',
        'file_path',
        'file_type',
        'file_size',
        'video_url',
        'thumbnail_url',
        'video_id',
        'order',
        'is_secure',
        'allow_download',
        'external_provider',
        'external_video_id',
        'encryption_data',
    ];

    protected $casts = [
        'is_secure' => 'boolean',
        'allow_download' => 'boolean',
        'file_size' => 'integer',
        'order' => 'integer',
        'encryption_data' => 'array',
    ];

    /**
     * Get display file type for UI
     */
    public function getDisplayFileType(): string
    {
        if ($this->file_type === 'secure_video') {
            return 'Secure Video';
        }

        if ($this->file_type === 'external_video') {
            return ucfirst($this->external_provider) . ' Video';
        }

        $typeMap = [
            'pdf' => 'PDF Document',
            'doc' => 'Word Document', 'docx' => 'Word Document',
            'ppt' => 'PowerPoint', 'pptx' => 'PowerPoint',
            'xls' => 'Excel Spreadsheet', 'xlsx' => 'Excel Spreadsheet',
            'jpg' => 'Image', 'jpeg' => 'Image', 'png' => 'Image', 'gif' => 'Image',
            'mp4' => 'Video', 'mov' => 'Video', 'avi' => 'Video',
            'mp3' => 'Audio', 'wav' => 'Audio',
            'zip' => 'Archive',
        ];

        return $typeMap[$this->file_type] ?? ucfirst($this->file_type);
    }

    /**
     * Relationship with module
     */
    public function module()
    {
        return $this->belongsTo(CourseModule::class, 'module_id');
    }

    /**
     * Check if attachment is a video
     */
    public function isVideo(): bool
    {
        return in_array($this->file_type, ['secure_video', 'external_video']) || 
               in_array($this->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm']);
    }

    /**
     * Check if attachment is an image
     */
    public function isImage(): bool
    {
        return in_array($this->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']);
    }

    /**
     * Check if attachment is a document
     */
    public function isDocument(): bool
    {
        return in_array($this->file_type, ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt']);
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


    /**
     * Get the video progress records for the attachment.
     */
    public function videoProgress()
    {
        return $this->hasMany(VideoProgress::class);
    }

    /**
     * Get the video view sessions for the attachment.
     */
    public function videoViewSessions()
    {
        return $this->hasMany(VideoViewSession::class);
    }

    /**
     * Get the video events for the attachment.
     */
    public function videoEvents()
    {
        return $this->hasMany(VideoEvent::class);
    }

    /**
     * Get the offline access records for the attachment.
     */
    public function offlineAccess()
    {
        return $this->hasMany(OfflineAccess::class);
    }

    /**
     * Check if attachment is a secure video.
     */
    public function getIsSecureVideoAttribute()
    {
        return $this->file_type === 'secure_video' && $this->is_secure;
    }

    /**
     * Get user's progress for this attachment.
     */
    public function getUserProgress($userId)
    {
        return $this->videoProgress()
                    ->where('user_id', $userId)
                    ->first();
    }

    /**
     * Check if user has completed this attachment.
     */
    public function isCompletedByUser($userId)
    {
        $progress = $this->getUserProgress($userId);
        return $progress && $progress->completed;
    }

    /**
     * Get completion percentage for this attachment.
     */
    public function getCompletionPercentage($userId)
    {
        $progress = $this->getUserProgress($userId);
        return $progress ? $progress->progress_percentage : 0;
    }


}


<?php

use App\Models\Course;
use App\Models\UserProgress;
use App\Models\CourseModule;
use App\Models\ModuleAttachment;

if (!function_exists('calculateCourseProgress')) {
    /**
     * Calculate course progress for a student
     */
    function calculateCourseProgress($courseId, $userId)
    {
        try {
            $course = Course::with('modules')->find($courseId);
            
            if (!$course) {
                return [
                    'completion_percentage' => 0,
                    'total_time_spent' => 0,
                    'completed_modules' => 0,
                    'total_modules' => 0
                ];
            }

            $totalModules = $course->modules->count();
            
            if ($totalModules == 0) {
                return [
                    'completion_percentage' => 0,
                    'total_time_spent' => 0,
                    'completed_modules' => 0,
                    'total_modules' => 0
                ];
            }
            
            // Get all progress records for this course
            $progressRecords = UserProgress::where('user_id', $userId)
                ->whereHas('module', function($query) use ($courseId) {
                    $query->where('course_id', $courseId);
                })
                ->get();
            
            $completedModules = $progressRecords->where('completed', true)->count();
            
            // Calculate total time spent
            $totalTimeSpent = calculateActualTimeSpent($progressRecords);
            
            $completionPercentage = ($completedModules / $totalModules) * 100;
            
            return [
                'completion_percentage' => round($completionPercentage, 2),
                'total_time_spent' => $totalTimeSpent,
                'completed_modules' => $completedModules,
                'total_modules' => $totalModules
            ];
            
        } catch (\Exception $e) {
            \Log::error('Error calculating course progress: ' . $e->getMessage());
            return [
                'completion_percentage' => 0,
                'total_time_spent' => 0,
                'completed_modules' => 0,
                'total_modules' => 0
            ];
        }
    }
}

if (!function_exists('calculateActualTimeSpent')) {
    /**
     * Calculate actual time spent based on progress records
     */
    function calculateActualTimeSpent($progressRecords)
    {
        $totalTimeSpent = 0;
        
        foreach ($progressRecords as $progress) {
            if ($progress->viewed_at && $progress->completed_at) {
                // Calculate time between viewing and completing (in seconds)
                $timeSpent = $progress->completed_at->diffInSeconds($progress->viewed_at);
                $totalTimeSpent += $timeSpent;
            } elseif ($progress->viewed_at) {
                // If not completed, count time from viewing to now (capped at estimated duration)
                $estimatedDuration = getEstimatedModuleDuration($progress->module_id);
                $timeSpent = now()->diffInSeconds($progress->viewed_at);
                
                // Cap at estimated duration to prevent unrealistic times
                $totalTimeSpent += min($timeSpent, $estimatedDuration * 60); // Convert to seconds
            }
        }
        
        return $totalTimeSpent;
    }
}

if (!function_exists('getEstimatedModuleDuration')) {
    /**
     * Get estimated module duration in minutes
     */
    function getEstimatedModuleDuration($moduleId)
    {
        try {
            $module = CourseModule::with('attachments')->find($moduleId);
            
            if (!$module) {
                return 10; // Default fallback duration
            }
            
            return calculateModuleDuration($module);
            
        } catch (\Exception $e) {
            return 10; // Default fallback duration
        }
    }
}

if (!function_exists('calculateModuleDuration')) {
    /**
     * Calculate module duration based on attachments
     */
    function calculateModuleDuration($module)
    {
        $totalMinutes = 0;
        
        foreach ($module->attachments as $attachment) {
            $totalMinutes += getAttachmentDurationInMinutes($attachment);
        }
        
        return max(1, $totalMinutes); // Ensure at least 1 minute
    }
}

if (!function_exists('getFileDuration')) {
    /**
     * Get file duration for display
     */
    function getFileDuration($attachment)
    {
        if (!$attachment) {
            return '5:00';
        }
        
        if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            $duration = getAttachmentDurationInMinutes($attachment);
            $minutes = floor($duration);
            $seconds = round(($duration - $minutes) * 60);
            return sprintf('%d:%02d', $minutes, $seconds);
        } elseif ($attachment->file_type === 'pdf') {
            $pageCount = $attachment->metadata['page_count'] ?? 1;
            $readingTime = calculateReadingTime($pageCount);
            return $readingTime . ' min read';
        } else {
            return '5:00'; // Default duration for other file types
        }
    }
}

if (!function_exists('getAttachmentDurationInMinutes')) {
    /**
     * Get attachment duration in minutes
     */
    function getAttachmentDurationInMinutes($attachment)
    {
        if (!$attachment) {
            return 5; // Default fallback
        }
        
        // Check if duration is stored in metadata
        if (isset($attachment->metadata['duration_minutes'])) {
            return max(1, $attachment->metadata['duration_minutes']);
        }
        
        // Estimate based on file type
        if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm'])) {
            // For videos, you might want to implement actual duration detection
            // For now, use reasonable estimates based on file size or type
            $fileSize = $attachment->file_size ?? 0;
            if ($fileSize > 100 * 1024 * 1024) { // > 100MB
                return 30;
            } elseif ($fileSize > 50 * 1024 * 1024) { // > 50MB
                return 15;
            } else {
                return 10;
            }
        } elseif ($attachment->file_type === 'pdf') {
            $pageCount = $attachment->metadata['page_count'] ?? 1;
            return calculateReadingTime($pageCount);
        } else {
            return 5; // Default for other files
        }
    }
}

if (!function_exists('calculateReadingTime')) {
    /**
     * Calculate reading time for documents
     */
    function calculateReadingTime($pageCount)
    {
        // Average reading time: 2 minutes per page
        return max(1, $pageCount * 2);
    }
}

if (!function_exists('getYoutubeId')) {
    /**
     * Extract YouTube ID from URL
     */
    function getYoutubeId($url)
    {
        $pattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($pattern, $url, $matches);
        return $matches[1] ?? null;
    }
}

if (!function_exists('getYoutubeThumbnail')) {
    /**
     * Get YouTube thumbnail URL
     */
    function getYoutubeThumbnail($url)
    {
        $youtubeId = getYoutubeId($url);
        return $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg" : null;
    }
}

if (!function_exists('formatTimeSpent')) {
    /**
     * Format time spent in seconds to human readable format
     */
    function formatTimeSpent($seconds)
    {
        if ($seconds <= 0) {
            return '0h 0m';
        }
        
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;
        
        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $minutes);
        } elseif ($minutes > 0) {
            return sprintf('%dm %02ds', $minutes, $remainingSeconds);
        } else {
            return sprintf('%ds', $remainingSeconds);
        }
    }
}

if (!function_exists('formatTimeSpentDetailed')) {
    /**
     * Format time spent in detailed format (HH:MM:SS)
     */
    function formatTimeSpentDetailed($seconds)
    {
        if ($seconds <= 0) {
            return '00:00:00';
        }
        
        return gmdate('H:i:s', $seconds);
    }
}


if (!function_exists('getFileTypeInfo')) {
    function getFileTypeInfo($fileType, $videoUrl = null) {
        $isExternalVideo = $videoUrl != null;
        
        if ($isExternalVideo) {
            return [
                'icon' => 'fab fa-youtube',
                'iconColor' => 'text-red-600',
                'bgColor' => 'bg-red-100',
                'hoverBgColor' => 'bg-red-200',
                'typeName' => 'YouTube Video',
                'duration' => 'Video'
            ];
        }
        
        switch ($fileType) {
            case 'pdf':
                return [
                    'icon' => 'fas fa-file-pdf',
                    'iconColor' => 'text-red-600',
                    'bgColor' => 'bg-red-100',
                    'hoverBgColor' => 'bg-red-200',
                    'typeName' => 'PDF Document',
                    'duration' => 'Document'
                ];
                
            case 'mp4':
            case 'mov':
            case 'avi':
            case 'mkv':
            case 'webm':
                return [
                    'icon' => 'fas fa-video',
                    'iconColor' => 'text-purple-600',
                    'bgColor' => 'bg-purple-100',
                    'hoverBgColor' => 'bg-purple-200',
                    'typeName' => 'Video',
                    'duration' => 'Video'
                ];
                
            case 'mp3':
            case 'wav':
            case 'ogg':
                return [
                    'icon' => 'fas fa-music',
                    'iconColor' => 'text-green-600',
                    'bgColor' => 'bg-green-100',
                    'hoverBgColor' => 'bg-green-200',
                    'typeName' => 'Audio',
                    'duration' => 'Audio'
                ];
                
            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'webp':
            case 'bmp':
                return [
                    'icon' => 'fas fa-image',
                    'iconColor' => 'text-green-600',
                    'bgColor' => 'bg-green-100',
                    'hoverBgColor' => 'bg-green-200',
                    'typeName' => 'Image',
                    'duration' => 'Image'
                ];
                
            case 'doc':
            case 'docx':
                return [
                    'icon' => 'fas fa-file-word',
                    'iconColor' => 'text-blue-600',
                    'bgColor' => 'bg-blue-100',
                    'hoverBgColor' => 'bg-blue-200',
                    'typeName' => 'Word Document',
                    'duration' => 'Document'
                ];
                
            case 'xls':
            case 'xlsx':
                return [
                    'icon' => 'fas fa-file-excel',
                    'iconColor' => 'text-green-600',
                    'bgColor' => 'bg-green-100',
                    'hoverBgColor' => 'bg-green-200',
                    'typeName' => 'Excel Spreadsheet',
                    'duration' => 'Spreadsheet'
                ];
                
            case 'ppt':
            case 'pptx':
                return [
                    'icon' => 'fas fa-file-powerpoint',
                    'iconColor' => 'text-orange-600',
                    'bgColor' => 'bg-orange-100',
                    'hoverBgColor' => 'bg-orange-200',
                    'typeName' => 'PowerPoint',
                    'duration' => 'Presentation'
                ];
                
            case 'zip':
            case 'rar':
            case '7z':
                return [
                    'icon' => 'fas fa-file-archive',
                    'iconColor' => 'text-yellow-600',
                    'bgColor' => 'bg-yellow-100',
                    'hoverBgColor' => 'bg-yellow-200',
                    'typeName' => 'Archive',
                    'duration' => 'Archive'
                ];
                
            default:
                return [
                    'icon' => 'fas fa-file',
                    'iconColor' => 'text-gray-600',
                    'bgColor' => 'bg-gray-100',
                    'hoverBgColor' => 'bg-gray-200',
                    'typeName' => 'File',
                    'duration' => 'File'
                ];
        }
    }
}

if (!function_exists('formatFileSize')) {
    function formatFileSize($bytes) {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}
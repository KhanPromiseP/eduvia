<?php

// Calculate module duration based on attachments
function calculateModuleDuration($module) {
    $totalMinutes = 0;
    
    foreach ($module->attachments as $attachment) {
        $totalMinutes += getAttachmentDurationInMinutes($attachment);
    }
    
    return $totalMinutes;
}

// Get file duration for display
function getFileDuration($attachment) {
    if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv'])) {
        $duration = getAttachmentDurationInMinutes($attachment);
        $minutes = floor($duration);
        $seconds = round(($duration - $minutes) * 60);
        return sprintf('%d:%02d', $minutes, $seconds);
    } elseif ($attachment->file_type === 'pdf') {
        $pageCount = $attachment->metadata['page_count'] ?? 1;
        return $pageCount . ' page' . ($pageCount != 1 ? 's' : '');
    } else {
        return '5:00'; // Default duration for other file types
    }
}

// Get attachment duration in minutes (pseudo-implementation)
function getAttachmentDurationInMinutes($attachment) {
    // This would typically read metadata from the file
    // For now, return a random value for demonstration
    if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv'])) {
        return rand(5, 30); // Random between 5-30 minutes for videos
    } else {
        return rand(1, 10); // Random between 1-10 minutes for other files
    }
}
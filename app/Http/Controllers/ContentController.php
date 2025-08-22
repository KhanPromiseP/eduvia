<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class SecureContentController extends Controller
{
    public function stream(Attachment $attachment)
    {
        // Check if user has access to this content
        $user = Auth::user();
        
        if (!$user || !$user->purchasedCourses->contains($attachment->module->course_id)) {
            abort(403, 'You do not have access to this resource.');
        }

        $filePath = 'public/' . $attachment->file_path;
        
        if (!Storage::exists($filePath)) {
            abort(404, 'File not found.');
        }

        $file = Storage::path($filePath);
        $fileSize = Storage::size($filePath);
        $mimeType = $this->getMimeType($attachment->file_type);

        Log::info('Streaming file', [
            'attachment_id' => $attachment->id,
            'file_type' => $attachment->file_type,
            'mime_type' => $mimeType,
            'user_id' => $user->id
        ]);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Content-Disposition' => 'inline', // Show in browser instead of downloading
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ];

        // For videos, enable range requests for seeking
        if (strpos($mimeType, 'video/') === 0) {
            return Response::stream(function() use ($file) {
                readfile($file);
            }, 200, $headers);
        }

        return response()->file($file, $headers);
    }

    private function getMimeType($fileExtension)
    {
        $mimeTypes = [
            // Video formats
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            'webm' => 'video/webm',
            'wmv' => 'video/x-ms-wmv',
            'flv' => 'video/x-flv',
            'm4v' => 'video/x-m4v',
            
            // Document formats
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'rtf' => 'application/rtf',
            
            // Image formats
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'tiff' => 'image/tiff',
            
            // Audio formats
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'm4a' => 'audio/mp4',
            
            // Archive formats
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
            '7z' => 'application/x-7z-compressed',
        ];

        $extension = strtolower($fileExtension);
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }

    // public function download(Attachment $attachment)
    // {
    //     // Optional: If you want to allow downloads in some cases
    //     $user = Auth::user();
        
    //     if (!$user || !$user->purchasedCourses->contains($attachment->module->course_id)) {
    //         abort(403, 'You do not have access to this resource.');
    //     }

    //     $filePath = 'public/' . $attachment->file_path;
        
    //     if (!Storage::exists($filePath)) {
    //         abort(404, 'File not found.');
    //     }

    //     return Storage::download($filePath, $attachment->title . '.' . $attachment->file_type);
    // }
}
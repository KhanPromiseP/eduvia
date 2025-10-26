<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Services\EnterpriseStreamingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VideoStreamController extends Controller
{
    protected $streamingService;

    public function __construct(EnterpriseStreamingService $streamingService)
    {
        $this->streamingService = $streamingService;
    }

    /**
     * Get secure stream URL for video (returns JSON with direct URL)
     */
    public function getSecureStream(Attachment $attachment)
    {
        try {
            if ($attachment->file_type !== 'secure_video') {
                return response()->json([
                    'success' => false,
                    'message' => 'Video not found'
                ], 404);
            }

            // Generate direct signed URL
            $streamUrl = $this->streamingService->generateSecureStreamUrl(
                $attachment->video_id,
                '720p',
                Auth::id(),
                request()->ip()
            );

            return response()->json([
                'success' => true,
                'stream_url' => $streamUrl,
                'type' => 'video',
                'title' => $attachment->title,
                'expires_in' => 3600 // 1 hour
            ]);

        } catch (\Exception $e) {
            Log::error('Secure stream error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to load video: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Direct video streaming endpoint (no redirects)
     */
    public function streamVideoDirect(Attachment $attachment)
    {
        try {
            // Check access
            if (!Auth::check()) {
                abort(403, 'Access denied');
            }

            if (!Storage::disk('r2')->exists($attachment->file_path)) {
                abort(404, 'Video file not found');
            }

            $filePath = $attachment->file_path;
            $fileSize = Storage::disk('r2')->size($filePath);
            $mimeType = $this->getVideoMimeType(pathinfo($filePath, PATHINFO_EXTENSION));

            // Handle range requests for video streaming
            $range = request()->header('Range');
            
            if ($range) {
                return $this->serveVideoRange($filePath, $fileSize, $mimeType, $range);
            }

            // Serve full video
            $headers = [
                'Content-Type' => $mimeType,
                'Content-Length' => $fileSize,
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'private, max-age=3600',
            ];

            $stream = Storage::disk('r2')->readStream($filePath);
            
            return response()->stream(function() use ($stream) {
                fpassthru($stream);
            }, 200, $headers);

        } catch (\Exception $e) {
            Log::error('Direct video streaming error: ' . $e->getMessage());
            abort(500, 'Unable to stream video');
        }
    }

    /**
     * Serve video with range support
     */
    private function serveVideoRange($filePath, $fileSize, $mimeType, $range)
    {
        list($sizeUnit, $range) = explode('=', $range);
        list($start, $end) = explode('-', $range);

        $start = (int) $start;
        $end = $end ? (int) $end : $fileSize - 1;
        $length = $end - $start + 1;

        $stream = Storage::disk('r2')->readStream($filePath);
        fseek($stream, $start);

        $headers = [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=3600',
        ];

        return response()->stream(function() use ($stream, $length) {
            $chunkSize = 1024 * 1024; // 1MB chunks
            $bytesRead = 0;
            
            while (!feof($stream) && $bytesRead < $length) {
                $bytesToRead = min($chunkSize, $length - $bytesRead);
                echo fread($stream, $bytesToRead);
                $bytesRead += $bytesToRead;
                flush();
            }
            fclose($stream);
        }, 206, $headers);
    }

    /**
     * Get MIME type for video files
     */
    private function getVideoMimeType(string $extension): string
    {
        $mimeTypes = [
            'mp4' => 'video/mp4',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'mkv' => 'video/x-matroska',
            'webm' => 'video/webm',
            'wmv' => 'video/x-ms-wmv',
            'm4v' => 'video/x-m4v',
            '3gp' => 'video/3gpp',
            'flv' => 'video/x-flv',
        ];
        
        return $mimeTypes[strtolower($extension)] ?? 'video/mp4';
    }
}
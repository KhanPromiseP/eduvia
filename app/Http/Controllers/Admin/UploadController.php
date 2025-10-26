<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function getS3Params(Request $request)
    {
        $request->validate([
            'filename' => 'required|string',
            'contentType' => 'required|string',
        ]);

        $filename = $request->filename;
        $contentType = $request->contentType;
        
        // Generate unique file path
        $fileId = Str::uuid();
        $extension = pathinfo($filename, PATHINFO_EXTENSION) ?: 'mp4';
        $safeFilename = Str::slug(pathinfo($filename, PATHINFO_FILENAME));
        $filePath = "videos/original/{$fileId}/{$safeFilename}_{$fileId}.{$extension}";

        try {
            \Log::info('Generating S3 params', [
                'filename' => $filename,
                'contentType' => $contentType,
                'filePath' => $filePath
            ]);

            // Generate pre-signed URL for direct upload to Storj
            $signedUrl = Storage::disk('r2')->temporaryUrl(
                $filePath,
                now()->addMinutes(30),
                [
                    'ContentType' => $contentType,
                ]
            );

            \Log::info('Generated signed URL', ['url' => $signedUrl]);

            return response()->json([
                'method' => 'PUT',
                'url' => $signedUrl,
                'fields' => [],
                'headers' => [
                    'Content-Type' => $contentType,
                ],
                'filePath' => $filePath,
                'fileId' => $fileId,
            ]);

        } catch (\Exception $e) {
            \Log::error('S3 params generation failed: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to generate upload URL: ' . $e->getMessage()
            ], 500);
        }
    }
}
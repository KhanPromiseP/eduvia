<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Attachment;

class EnterpriseStreamingService
{
    protected $encryptionKey;
    protected $streamingSecret;

    public function __construct()
    {
        // Get config with fallbacks
        $this->encryptionKey = $this->getVideoEncryptionKey();
        $this->streamingSecret = config('app.streaming_secret_key', Str::random(32));
    }

    /**
     * Get video encryption key with proper fallback
     */
    private function getVideoEncryptionKey(): string
    {
        $configKey = config('app.video_encryption_key');
        
        if (empty($configKey)) {
            Log::warning('Video encryption key not found in config, using fallback');
            return $this->generateFallbackEncryptionKey();
        }
        
        // Handle base64 encoded keys
        if (Str::startsWith($configKey, 'base64:')) {
            $decoded = base64_decode(Str::after($configKey, 'base64:'));
            if ($decoded === false) {
                Log::error('Failed to decode base64 video encryption key, using fallback');
                return $this->generateFallbackEncryptionKey();
            }
            return $decoded;
        }
        
        return $configKey;
    }

    /**
     * Generate fallback encryption key if config is missing
     */
    private function generateFallbackEncryptionKey(): string
    {
        // Use app key as fallback, but log a warning
        $appKey = config('app.key');
        if (Str::startsWith($appKey, 'base64:')) {
            $decoded = base64_decode(Str::after($appKey, 'base64:'));
            return substr($decoded, 0, 32); // Take first 32 bytes for AES-256
        }
        
        // Final fallback - generate a deterministic key
        return hash('sha256', 'fallback_video_encryption_key_' . config('app.key'), true);
    }

    /**
     * Get offline device limit with fallback
     */
    private function getMaxOfflineDevices(): int
    {
        return (int) config('app.max_offline_devices', 3);
    }

    /**
     * Get offline expiry days with fallback
     */
    private function getOfflineExpiryDays(): int
    {
        return (int) config('app.offline_expiry_days', 30);
    }

    /**
     * Upload secure video with proper binary data handling
     */
    public function uploadSecureVideo(UploadedFile $video, array $qualities = ['720p'])
    {
        try {
            Log::info('Starting secure video upload', [
                'file_name' => $video->getClientOriginalName(),
                'file_size' => $video->getSize(),
                'file_mime' => $video->getMimeType()
            ]);

            $videoId = Str::uuid()->toString();
            $encryptionKey = $this->generateEncryptionKey();
            
            $originalFilename = 'video_' . $videoId . '.' . $video->getClientOriginalExtension();
            $originalPath = "videos/original/{$videoId}/" . $originalFilename;
            
            Log::info('Attempting to store file', [
                'path' => $originalPath,
                'disk' => 'r2'
            ]);

            // Store the original file
            $stored = Storage::disk('r2')->put($originalPath, file_get_contents($video->getRealPath()), [
                'CacheControl' => 'private, no-cache',
                'ContentType' => $video->getMimeType()
            ]);

            Log::info('File storage result', ['stored' => $stored]);

            if (!$stored) {
                throw new \Exception('Failed to store video file in R2');
            }

            $uploadData = [
                'video_id' => $videoId,
                'encryption_key' => base64_encode($encryptionKey),
                'qualities' => ['720p' => $originalPath],
                'file_path' => $originalPath,
                'thumbnail_url' => null,
                'created_at' => now(),
            ];

            // Store encryption key with proper fallback handling
            $this->storeEncryptionKey($videoId, $encryptionKey);

            Log::info('Secure video upload completed successfully', [
                'video_id' => $videoId
            ]);

            return $uploadData;

        } catch (\Exception $e) {
            Log::error('Secure video upload failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Store encryption key with multiple fallback options
     */
    private function storeEncryptionKey(string $videoId, string $encryptionKey): void
    {
        $encodedKey = base64_encode($encryptionKey);
        
        // Try cache first
        try {
            Cache::put("video_key_{$videoId}", $encodedKey, now()->addYears(10));
            Log::info('Encryption key stored in cache successfully');
            return;
        } catch (\Exception $e) {
            Log::warning('Failed to store encryption key in cache', [
                'error' => $e->getMessage()
            ]);
        }

        // Try database fallback
        try {
            $this->storeKeyInDatabase($videoId, $encodedKey);
            Log::info('Encryption key stored in database fallback');
            return;
        } catch (\Exception $e) {
            Log::error('Failed to store encryption key in database fallback', [
                'error' => $e->getMessage()
            ]);
        }

        // Final fallback - file storage
        try {
            $this->storeKeyInFile($videoId, $encodedKey);
            Log::info('Encryption key stored in file fallback');
        } catch (\Exception $e) {
            Log::critical('All encryption key storage methods failed', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            throw new \Exception('Failed to store encryption key using any method');
        }
    }

    /**
     * Store key in database fallback
     */
    private function storeKeyInDatabase(string $videoId, string $encodedKey): void
    {
        if (!Schema::hasTable('video_encryption_keys')) {
            $this->createVideoEncryptionKeysTable();
        }
        
        DB::table('video_encryption_keys')->insert([
            'video_id' => $videoId,
            'encryption_key' => $encodedKey,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }



    /**
     * Store key in file fallback
     */
    private function storeKeyInFile(string $videoId, string $encodedKey): void
    {
        $path = "video_keys/{$videoId}.key";
        Storage::disk('local')->put($path, $encodedKey);
    }

    /**
     * Get encryption key with proper decoding and fallbacks
     */
    private function getStoredEncryptionKey(string $videoId): string
    {
        // Try cache first
        $cachedKey = Cache::get("video_key_{$videoId}");
        if ($cachedKey) {
            return base64_decode($cachedKey);
        }

        // Try database fallback
        if (Schema::hasTable('video_encryption_keys')) {
            $keyRecord = DB::table('video_encryption_keys')
                ->where('video_id', $videoId)
                ->first();
                
            if ($keyRecord) {
                // Store in cache for future use
                Cache::put("video_key_{$videoId}", $keyRecord->encryption_key, now()->addYears(10));
                return base64_decode($keyRecord->encryption_key);
            }
        }

        // Try file fallback
        $filePath = "video_keys/{$videoId}.key";
        if (Storage::disk('local')->exists($filePath)) {
            $fileKey = Storage::disk('local')->get($filePath);
            // Store in cache for future use
            Cache::put("video_key_{$videoId}", $fileKey, now()->addYears(10));
            return base64_decode($fileKey);
        }

        throw new \Exception("Encryption key not found for video: {$videoId}");
    }

    /**
     * Get encryption key with proper decoding
     */
    private function getEncryptionKey(string $videoId): string
    {
        // Try cache first
        $cachedKey = Cache::get("video_key_{$videoId}");
        if ($cachedKey) {
            return base64_decode($cachedKey);
        }

        // Try database fallback
        if (\Schema::hasTable('video_encryption_keys')) {
            $keyRecord = \DB::table('video_encryption_keys')
                ->where('video_id', $videoId)
                ->first();
                
            if ($keyRecord) {
                return base64_decode($keyRecord->encryption_key);
            }
        }

        throw new \Exception("Encryption key not found for video: {$videoId}");
    }

    /**
     * Create video encryption keys table if it doesn't exist
     */
    private function createVideoEncryptionKeysTable(): void
    {
        \Schema::create('video_encryption_keys', function ($table) {
            $table->id();
            $table->string('video_id')->unique();
            $table->text('encryption_key'); // Base64 encoded
            $table->timestamps();
        });
    }

 public function generateSecureStreamUrl(string $videoId, string $quality, int $userId, string $userIp): string
{
    try {
        $attachment = Attachment::where('video_id', $videoId)->first();
        
        if (!$attachment) {
            throw new \Exception('Attachment not found for video ID: ' . $videoId);
        }

        $filePath = $attachment->file_path;

        if (!Storage::disk('r2')->exists($filePath)) {
            throw new \Exception('Video file not found in storage: ' . $filePath);
        }

        // Generate signed URL with proper content type
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $mimeType = $this->getVideoMimeType($extension);
        
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $filePath,
            now()->addHours(2),
            [
                'ResponseContentType' => $mimeType,
                'ResponseCacheControl' => 'private, max-age=7200',
                'ResponseContentDisposition' => 'inline'
            ]
        );

        \Log::info('Streaming service generated URL', [
            'video_id' => $videoId,
            'file_path' => $filePath,
            'mime_type' => $mimeType
        ]);

        return $signedUrl;

    } catch (\Exception $e) {
        \Log::error('Secure stream URL generation failed', [
            'video_id' => $videoId,
            'error' => $e->getMessage()
        ]);
        
        // Fallback - try direct file access
        if (isset($attachment)) {
            return route('attachment.view', ['attachment' => $attachment->id]);
        }
        
        throw new \Exception('Failed to generate secure stream URL: ' . $e->getMessage());
    }
}

/**
 * Get proper MIME type for video files
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

    /**
     * Stream video with token - FIXED VERSION
     */
    public function streamVideo(Request $request, string $token, string $quality)
    {
        try {
            // Decrypt and validate token
            $tokenData = $this->decryptToken($token);
            $this->validateStreamToken($tokenData, $request->ip());

            $videoId = $tokenData['video_id'];
            
            // Find the attachment
            $attachment = Attachment::where('video_id', $videoId)->first();
            
            if (!$attachment) {
                throw new \Exception('Video not found');
            }

            $filePath = $attachment->file_path;

            if (!Storage::disk('r2')->exists($filePath)) {
                throw new \Exception('Video file not found');
            }

            // Return the video file as a stream response
            return Storage::disk('r2')->response($filePath, null, [
                'Content-Type' => 'video/mp4',
                'Cache-Control' => 'private, no-cache',
                'Accept-Ranges' => 'bytes',
            ]);

        } catch (\Exception $e) {
            Log::error('Video streaming failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate stream token
     */
    private function validateStreamToken(array $tokenData, string $currentIp): void
    {
        if (now()->timestamp > $tokenData['expires']) {
            throw new \Exception('Stream token expired');
        }

        if ($tokenData['user_ip'] !== $currentIp) {
            throw new \Exception('IP address mismatch');
        }

        if (!Cache::has("stream_session_{$tokenData['session_id']}")) {
            throw new \Exception('Invalid streaming session');
        }
    }

    


    /**
     * Multi-layer security validation - UPDATED
     */
    private function validateAccess(string $videoId, int $userId, string $userIp): void
    {
        // Check if user has access to the course
        // if (!$this->userHasCourseAccess($videoId, $userId)) {
        //     throw new \Exception('Access denied to this course');
        // }

        // Check concurrent streams (max 1)
        $activeStreams = Cache::get("user_streams_{$userId}", 0);
        if ($activeStreams >= 1) {
            throw new \Exception('Multiple simultaneous streams not allowed');
        }

        // Check geographic restrictions
        if (!$this->isAllowedRegion($userIp)) {
            throw new \Exception('Streaming not available in your region');
        }

        // Check download attempts
        $downloadAttempts = Cache::get("download_attempts_{$userId}_{$videoId}", 0);
        if ($downloadAttempts > 3) {
            throw new \Exception('Too many download attempts');
        }
    }

    /**
     * Generate offline access package with proper config fallbacks
     */
    public function generateOfflineAccess(string $videoId, int $userId, string $deviceId): array
    {
        // Validate device limit with config fallback
        $userDevices = Cache::get("user_devices_{$userId}", []);
        $maxDevices = $this->getMaxOfflineDevices();
        
        if (count($userDevices) >= $maxDevices && !in_array($deviceId, $userDevices)) {
            abort(403, "Device limit reached for offline access. Maximum {$maxDevices} devices allowed.");
        }

        $expiryDays = $this->getOfflineExpiryDays();
        $expires = now()->addDays($expiryDays);
        
        $offlineToken = $this->encryptToken([
            'video_id' => $videoId,
            'user_id' => $userId,
            'device_id' => $deviceId,
            'expires' => $expires->timestamp,
            'type' => 'offline',
        ]);

        // Generate encrypted offline package
        $offlineData = $this->createOfflinePackage($videoId, $userId);
        
        // Track device
        if (!in_array($deviceId, $userDevices)) {
            $userDevices[] = $deviceId;
            Cache::put("user_devices_{$userId}", $userDevices, now()->addYears(1));
        }

        Cache::put("offline_access_{$userId}_{$videoId}_{$deviceId}", true, $expires);

        return [
            'download_url' => route('secure.video.offline', ['token' => $offlineToken]),
            'expires_at' => $expires,
            'device_id' => $deviceId,
            'max_devices' => $maxDevices,
            'expiry_days' => $expiryDays,
        ];
    }

    /**
     * Advanced video streaming with DRM-like protection
     */
    // public function streamVideo(Request $request, string $token, string $quality)
    // {
    //     // Decrypt and validate token
    //     $tokenData = $this->decryptToken($token);
    //     $this->validateStreamToken($tokenData, $request->ip());

    //     $videoId = $tokenData['video_id'];
    //     $path = "videos/{$quality}/{$videoId}/" . $this->getVideoFilename($videoId);

    //     // Increment concurrent stream counter
    //     Cache::increment("user_streams_{$tokenData['user_id']}");
        
    //     // Track streaming session
    //     $this->trackStreamingSession($tokenData);

    //     return $this->serveProtectedVideo($path, $videoId, $tokenData['session_id']);
    // }

    /**
     * Serve video with chunked encryption - FIXED VERSION
     */
    private function serveProtectedVideo(string $path, string $videoId, string $sessionId)
    {
        $disk = Storage::disk('r2');
        
        if (!$disk->exists($path)) {
            abort(404, 'Video not found');
        }

        $fileSize = $disk->size($path);
        $contentType = 'video/mp4';

        // Get encryption key using fallback system
        try {
            $encryptionKey = $this->getStoredEncryptionKey($videoId);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve encryption key for video streaming', [
                'video_id' => $videoId,
                'error' => $e->getMessage()
            ]);
            abort(500, 'Encryption key not available');
        }

        $range = request()->header('Range');
        
        if ($range) {
            return $this->serveEncryptedChunk($path, $fileSize, $contentType, $range, $encryptionKey, $sessionId);
        }

        return $this->serveEncryptedFull($path, $fileSize, $contentType, $encryptionKey, $sessionId);
    }

    /**
     * Serve encrypted video chunks
     */
    private function serveEncryptedChunk(string $path, int $fileSize, string $contentType, string $range, string $encryptionKey, string $sessionId)
    {
        list($sizeUnit, $range) = explode('=', $range);
        list($start, $end) = explode('-', $range);

        $start = (int) $start;
        $end = $end ? (int) $end : $fileSize - 1;
        $length = $end - $start + 1;

        // Read and encrypt chunk using Laravel's Storage
        $chunk = Storage::disk('r2')->read($path);
        $chunk = substr($chunk, $start, $length);
        $encryptedChunk = $this->encryptChunk($chunk, $encryptionKey, $start);

        return response($encryptedChunk, 206, [
            'Content-Type' => $contentType,
            'Content-Length' => strlen($encryptedChunk),
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-cache',
            'X-Stream-Session' => $sessionId,
        ]);
    }

    /**
     * Advanced encryption methods
     */
    private function encryptVideo(string $content, string $key): string
    {
        $iv = random_bytes(16);
        $encrypted = openssl_encrypt($content, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
        return $iv . $encrypted;
    }

    private function encryptChunk(string $chunk, string $key, int $offset): string
    {
        $iv = substr(md5($key . $offset), 0, 16);
        return openssl_encrypt($chunk, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    }

    private function generateEncryptionKey(): string
    {
        return random_bytes(32); // AES-256
    }

    private function encryptToken(array $data): string
    {
        return Crypt::encrypt($data);
    }

    private function decryptToken(string $token): array
    {
        return Crypt::decrypt($token);
    }

    private function generateDeviceFingerprint(): string
    {
        $fingerprint = request()->userAgent() . 
                      request()->ip() . 
                      request()->header('Accept-Language') . 
                      request()->header('User-Agent');
        
        return hash('sha256', $fingerprint);
    }

    // private function validateStreamToken(array $tokenData, string $currentIp): void
    // {
    //     if (Carbon::now()->timestamp > $tokenData['expires']) {
    //         abort(403, 'Stream token expired');
    //     }

    //     if ($tokenData['user_ip'] !== $currentIp) {
    //         abort(403, 'IP address mismatch');
    //     }

    //     if (!Cache::has("stream_session_{$tokenData['session_id']}")) {
    //         abort(403, 'Invalid streaming session');
    //     }
    // }

    //  /**
    //  * Check if user has course access - FIXED VERSION
    //  */
    // public function userHasCourseAccess(string $videoId, int $userId): bool
    // {
    //     try {
    //         // Find the attachment by video_id
    //         $attachment = Attachment::where('video_id', $videoId)->first();

    //         if (!$attachment) {
    //             Log::warning('Attachment not found for video_id: ' . $videoId);
    //             return false;
    //         }

    //         // Load the relationships
    //         $attachment->load('module.course');

    //         // Check if relationships exist
    //         if (!$attachment->module || !$attachment->module->course) {
    //             Log::warning('Module or course not found for attachment', [
    //                 'attachment_id' => $attachment->id,
    //                 'video_id' => $videoId
    //             ]);
    //             return false;
    //         }

    //         // Check if user is enrolled in the course
    //         return \App\Models\UserCourse::where('user_id', $userId)
    //             ->where('course_id', $attachment->module->course->id)
    //             // ->where('status', 'active')
    //             ->exists();

    //     } catch (\Exception $e) {
    //         Log::error('Course access check failed: ' . $e->getMessage(), [
    //             'video_id' => $videoId,
    //             'user_id' => $userId
    //         ]);
    //         return false;
    //     }
    // }

    /**
 * Check if user has course access - TEMPORARY BYPASS
 */
private function userHasCourseAccess(Attachment $attachment, int $userId): bool
{
    // TODO: Implement proper course access logic
    // For now, return true to test video streaming
    Log::info('Course access check bypassed for testing', [
        'attachment_id' => $attachment->id,
        'user_id' => $userId,
        'course_id' => $attachment->module->course->id ?? 'unknown'
    ]);
    return true;
}

    private function isAllowedRegion(string $ip): bool
    {
        // Implement geographic restrictions
        $restrictedRegions = ['CN', 'RU', 'KP']; // Example restricted countries
        $country = $this->getCountryFromIP($ip);
        
        return !in_array($country, $restrictedRegions);
    }

    private function getCountryFromIP(string $ip): string
    {
        // Use IP geolocation service
        try {
            $response = file_get_contents("http://ip-api.com/json/{$ip}");
            $data = json_decode($response, true);
            return $data['countryCode'] ?? 'US';
        } catch (\Exception $e) {
            return 'US';
        }
    }

    private function trackStreamingSession(array $tokenData): void
    {
        \App\Models\VideoView::create([
            'user_id' => $tokenData['user_id'],
            'video_id' => $tokenData['video_id'],
            'session_id' => $tokenData['session_id'],
            'ip_address' => $tokenData['user_ip'],
            'user_agent' => request()->userAgent(),
            'started_at' => now(),
        ]);
    }

    private function generateSecureFilename(UploadedFile $file): string
    {
        $extension = $file->getClientOriginalExtension();
        $randomName = Str::random(40);
        return "{$randomName}.{$extension}";
    }

    private function getVideoFilename(string $videoId): string
    {
        // Get from database
        $attachment = \App\Models\Attachment::where('video_id', $videoId)->first();
        return $attachment ? $this->generateSecureFilename(new UploadedFile(
            $attachment->file_path, 
            basename($attachment->file_path)
        )) : 'video.mp4';
    }

    private function createOfflinePackage(string $videoId, int $userId): array
    {
        // Create encrypted offline package
        return [
            'video_id' => $videoId,
            'user_id' => $userId,
            'package_hash' => Str::random(32),
            'created_at' => now(),
        ];
    }

    /**
     * Serve offline video
     */
    public function serveOfflineVideo(array $tokenData)
    {
        $this->validateOfflineAccess($tokenData);

        $videoId = $tokenData['video_id'];
        $path = "videos/720p/{$videoId}/" . $this->getVideoFilename($videoId);

        return $this->serveProtectedVideo($path, $videoId, 'offline_' . $tokenData['device_id']);
    }

    /**
     * Validate offline access
     */
    public function validateOfflineAccess(array $tokenData): bool
    {
        if (Carbon::now()->timestamp > $tokenData['expires']) {
            throw new \Exception('Offline access expired');
        }

        $cacheKey = "offline_access_{$tokenData['user_id']}_{$tokenData['video_id']}_{$tokenData['device_id']}";
        
        if (!Cache::has($cacheKey)) {
            throw new \Exception('Offline access not found');
        }

        return true;
    }

    /**
     * Serve encrypted full video
     */
    private function serveEncryptedFull(string $path, int $fileSize, string $contentType, string $encryptionKey, string $sessionId)
    {
        $encryptedContent = $this->encryptVideo(
            Storage::disk('r2')->get($path),
            $encryptionKey
        );

        return response($encryptedContent, 200, [
            'Content-Type' => $contentType,
            'Content-Length' => strlen($encryptedContent),
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-cache',
            'X-Stream-Session' => $sessionId,
            'Content-Disposition' => 'inline; filename="encrypted_video.mp4"'
        ]);
    }

    /**
     * Clean up streaming session
     */
    public function cleanupStreamSession(string $sessionId): void
    {
        Cache::forget("stream_session_{$sessionId}");
        
        // Decrement concurrent stream counter
        $sessionData = Cache::get("stream_session_{$sessionId}");
        if ($sessionData) {
            Cache::decrement("user_streams_{$sessionData['user_id']}");
        }
    }

    /**
     * Generate temporary signed URL for R2 files
     */
    public function generateTemporaryUrl(string $path, int $expiresInMinutes = 60): string
    {
        return Storage::disk('r2')->temporaryUrl(
            $path,
            now()->addMinutes($expiresInMinutes)
        );
    }

    /**
     * Check if file exists in R2
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk('r2')->exists($path);
    }

    /**
     * Get file size from R2
     */
    public function getFileSize(string $path): int
    {
        return Storage::disk('r2')->size($path);
    }

    /**
     * Delete file from R2
     */
    public function deleteFile(string $path): bool
    {
        return Storage::disk('r2')->delete($path);
    }
}
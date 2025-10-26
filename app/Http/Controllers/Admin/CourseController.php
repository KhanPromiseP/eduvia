<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\User;
use App\Models\CourseModule;
use App\Models\Attachment;
use App\Services\EnterpriseStreamingService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{

    protected $streamingService;

    public function __construct(EnterpriseStreamingService $streamingService)
    {
        $this->streamingService = $streamingService;
    }



    /**
     * Get the appropriate route based on user role
     */
    private function getModuleRoute(Course $course, string $action = 'modules'): string
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return "admin.courses.{$action}";
        } elseif ($user->hasRole('instructor')) {
            return "instructor.courses.{$action}";
        }
        
        // Fallback to admin route
        return "instructor.courses.{$action}";
    }

    /**
     * Redirect to modules page with role-based routing
     */
    private function redirectToModules(Course $course, string $message = null, string $type = 'success')
    {
        $route = $this->getModuleRoute($course);
        
        $redirect = redirect()->route($route, $course);
        
        if ($message) {
            return $redirect->with($type, $message);
        }
        
        return $redirect;
    }

    /**
     * Redirect to attachments with role-based routing
     */
    private function redirectToAttachments(Course $course, CourseModule $module, string $message = null, string $type = 'success')
    {
        $route = $this->getModuleRoute($course);
        
        $redirect = redirect()->route($route, $course);
        
        if ($message) {
            return $redirect->with($type, $message);
        }
        
        return $redirect;
    }
    
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = Course::with(['user', 'category'])
            ->withCount('modules')
            ->latest()
            ->paginate(10);

        return view('admin.courses.index', compact('courses'));
    }

    /**
 * Display courses pending review
 */
public function pendingReview()
{
    // Get pending review courses with proper relationships
    $courses = Course::with(['category', 'instructor', 'modules'])
        ->withCount('modules')
        ->where('status', Course::STATUS_PENDING_REVIEW)
        ->latest()
        ->paginate(10);

    // Get accurate stats
    $instructorsCount = User::whereHas('courses', function($query) {
        $query->where('status', Course::STATUS_PENDING_REVIEW);
    })->count();

    $approvedThisWeek = Course::where('status', Course::STATUS_APPROVED)
        ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->count();

    $rejectedThisWeek = Course::where('status', Course::STATUS_REJECTED)
        ->whereBetween('updated_at', [now()->startOfWeek(), now()->endOfWeek()])
        ->count();

    return view('admin.courses.pending-review', compact(
        'courses', 
        'instructorsCount',
        'approvedThisWeek',
        'rejectedThisWeek'
    ));
}


/**
 * Show detailed review page for a course
 */
public function showReview(Course $course)
{
    if (!$course->isPendingReview()) {
        return redirect()->route('admin.courses.pending-review')
            ->with('error', 'This course is not pending review.');
    }

    $course->load(['instructor', 'category', 'modules.attachments']);

    return view('admin.courses.review', compact('course'));
}

/**
 * Get review statistics for dashboard
 */
public function reviewStats()
{
    return [
        'pending_count' => Course::pendingReview()->count(),
        'approved_today' => Course::approved()
            ->whereDate('reviewed_at', today())
            ->count(),
        'rejected_today' => Course::rejected()
            ->whereDate('reviewed_at', today())
            ->count(),
        'average_review_time' => $this->getAverageReviewTime(),
    ];
}

private function getAverageReviewTime()
{
    $recentlyReviewed = Course::whereNotNull('reviewed_at')
        ->where('reviewed_at', '>=', now()->subDays(30))
        ->get();

    if ($recentlyReviewed->isEmpty()) {
        return 0;
    }

    return $recentlyReviewed->avg(function($course) {
        return $course->created_at->diffInHours($course->reviewed_at);
    });
}
    
    /**
     * Approve a course.
     */
    public function approveCourse(Request $request, Course $course)
    {
        $request->validate([
            'review_notes' => 'nullable|string|max:1000',
        ]);

        $course->update([
            'status' => Course::STATUS_APPROVED,
            'reviewed_by' => Auth::id(), // Now Auth is imported
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.courses.pending-review')
            ->with('success', 'Course approved successfully.');
    }

    /**
     * Reject a course.
     */
    public function rejectCourse(Request $request, Course $course)
    {
        $request->validate([
            'review_notes' => 'required|string|min:10|max:1000',
        ]);

        $course->update([
            'status' => Course::STATUS_REJECTED,
            'reviewed_by' => Auth::id(), // Now Auth is imported
            'review_notes' => $request->review_notes,
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.courses.pending-review')
            ->with('success', 'Course rejected successfully.');
    }

    /**
     * Toggle course publish status (Admin only).
     */
    public function togglePublish(Course $course)
    {
        // Only allow publishing of approved courses
        if (!$course->isApproved()) {
            return redirect()->back()
                ->with('error', 'Only approved courses can be published.');
        }

        $course->update([
            'is_published' => !$course->is_published
        ]);

        $status = $course->is_published ? 'published' : 'unpublished';

        return redirect()->route('admin.courses.index')
            ->with('success', "Course {$status} successfully.");
    }

    /**
     * Show the form for creating a new course.
     */
   public function create()
    {
        $levels = [
            1 => 'Beginner',
            2 => 'Intermediate',
            3 => 'Advanced',
            4 => 'Expert',
            5 => 'Beginner to Advanced',
        ];

        $categories = Category::all(); // Fetch all categories

        return view('admin.courses.create', compact('levels', 'categories'));
    }


    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'requirements' => 'nullable|string',
            'price' => $request->has('is_premium') ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|integer|min:0',
            'level' => 'required|integer|in:1,2,3,4,5',
            'category_id' => 'required|exists:categories,id',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        $data['is_published'] = $request->has('is_published');
        $data['is_premium'] = $request->has('is_premium');

        // Set price for non-premium courses
        if (!$data['is_premium']) {
            $data['price'] = 0;
        }

        // Handle image
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        Course::create($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course created successfully.');
    }


    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        $course->load(['modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);

        return view('admin.courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
         $levels = [
            1 => 'Beginner',
            2 => 'Intermediate',
            3 => 'Advanced',
            4 => 'Expert',
            5 => 'Beginner to Advanced',
        ];

        $categories = Category::all(); // Fetch all categories

        return view('admin.courses.edit', compact('course', 'levels', 'categories'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'objectives' => 'nullable|string',
            'target_audience' => 'nullable|string',
            'requirements' => 'nullable|string',
            'price' => $request->has('is_premium') ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'duration' => 'nullable|integer|min:0',
            'level' => 'required|integer|in:1,2,3,4,5',
            'is_published' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            
            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        $data['is_published'] = $request->has('is_published');

        $data['is_premium'] = $request->has('is_premium');

        if (!$data['is_premium']) {
            $data['price'] = 0;
        }

        $course->update($data);

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        // Delete course image if exists
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        $course->delete();

        return redirect()->route('admin.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Show the form for managing course modules.
     */
    public function modules(Course $course)
    {
        $course->load(['modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);

        return view('admin.courses.modules', compact('course'));
    }

    /**
     * Enhanced module success messages
     */
    public function storeModule(Request $request, Course $course)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_free' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', '❌ Please check the module details and try again.')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_free'] = $request->has('is_free');

        $module = $course->modules()->create($data);

        $freeBadge = $data['is_free'] ? ' (Free Preview)' : '';
        
        $message = "✅ Module '{$module->title}'{$freeBadge} created successfully!";
    
        return $this->redirectToModules($course, $message);
    }

    public function updateModule(Request $request, Course $course, CourseModule $module)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_free' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->with('error', '❌ Unable to update module. Please check the details.')
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_free'] = $request->has('is_free');

        $module->update($data);

        $freeStatus = $data['is_free'] ? 'marked as Free Preview' : 'updated';
        
        $message = "✅ Module '{$module->title}' {$freeStatus} successfully!";
    
        return $this->redirectToModules($course, $message);
    }

    public function destroyModule(Course $course, CourseModule $module)
    {
        $moduleTitle = $module->title;
        $attachmentsCount = $module->attachments->count();

        // Delete all attachments for this module
        foreach ($module->attachments as $attachment) {
            if ($attachment->file_path && Storage::disk('r2')->exists($attachment->file_path)) {
                Storage::disk('r2')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $module->delete();

        $attachmentMessage = $attachmentsCount > 0 ? " and {$attachmentsCount} learning materials" : "";
        
        $message = "🗑️ Module '{$moduleTitle}'{$attachmentMessage} deleted successfully.";
    
        return $this->redirectToModules($course, $message);
    }




    /**
     * Get secure streaming URL for video
     */
    public function getSecureStreamUrl(Request $request, Attachment $attachment)
    {
        if ($attachment->file_type !== 'secure_video') {
            abort(404, 'Video not found');
        }

        $streamUrl = $this->streamingService->generateSecureStreamUrl(
            $attachment->video_id,
            '720p', // Default quality
            Auth::id(),
            $request->ip()
        );

        return response()->json([
            'stream_url' => $streamUrl,
            'expires_in' => 1800, // 30 minutes
        ]);
    }

    /**
     * Store attachment with enhanced error handling and messages
     */

  public function storeAttachment(Request $request, Course $course, CourseModule $module)
    {
        \Log::info('Store attachment called', [
            'course_id' => $course->id,
            'module_id' => $module->id,
            'content_type' => $request->content_type
        ]);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file_upload' => 'nullable|file|mimes:pdf,mp4,mov,avi,mkv,webm,jpg,jpeg,png,doc,docx,ppt,pptx,xls,xlsx,txt,zip,mp3,wav|max:512000',
            'secure_video_file' => 'nullable|file|mimes:mp4,mov,avi,mkv,webm|max:512000',
            'video_url' => 'nullable|url',
            'order' => 'required|integer|min:0',
            'is_secure' => 'sometimes|boolean',
            'allow_download' => 'sometimes|boolean',
            'content_type' => 'required|in:file,secure_video,external_video',
        ]);

        if ($validator->fails()) {
            $errorMessages = $validator->errors()->all();
            \Log::error('Attachment validation failed', ['errors' => $errorMessages]);
            
            return redirect()->back()
                ->with('error', $this->getValidationErrorMessage($errorMessages))
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();

        try {
            $attachmentData = [
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'order' => $data['order'],
                'allow_download' => $request->boolean('allow_download', true),
            ];

            \Log::info('Processing content type: ' . $data['content_type']);

            switch ($data['content_type']) {
                case 'secure_video':
                    if ($request->hasFile('secure_video_file')) {
                        $attachmentData = $this->handleSecureVideoUpload($request->file('secure_video_file'), $attachmentData);
                    } else {
                        throw new \Exception('Secure video file is required for secure video uploads.');
                    }
                    break;

                case 'external_video':
                    $attachmentData = $this->handleExternalVideo($request, $attachmentData);
                    break;

                case 'file':
                default:
                    if ($request->hasFile('file_upload')) {
                        $attachmentData = $this->handleFileUpload($request->file('file_upload'), $attachmentData);
                    } else {
                        throw new \Exception('Please select a file to upload.');
                    }
                    break;
            }

            \Log::info('Creating attachment with data', $attachmentData);
            $attachment = $module->attachments()->create($attachmentData);
            \Log::info('Attachment created successfully', ['attachment_id' => $attachment->id]);

            $successMessage = $this->getAttachmentSuccessMessage($data['content_type'], $attachment);
            
            return $this->redirectToModules($course, $successMessage);

        } catch (\Exception $e) {
            \Log::error('Attachment upload failed: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $errorMessage = $this->getAttachmentErrorMessage($data['content_type'] ?? 'file', $e->getMessage());
            
            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }

    /**
     * Get comprehensive success messages for attachments
     */
    private function getAttachmentSuccessMessage(string $contentType, Attachment $attachment): string
    {
        $messages = [
            'secure_video' => [
                'title' => '🎬 Secure Video Uploaded Successfully!',
                'message' => "Your video '{$attachment->title}' has been encrypted and securely stored."
            ],
            'external_video' => [
                'title' => '📺 External Video Added!',
                'message' => "External video '{$attachment->title}' has been linked successfully."
            ],
            'file' => [
                'title' => '📄 File Uploaded Successfully!',
                'message' => "File '{$attachment->title}' has been uploaded successfully."
            ]
        ];

        $typeConfig = $messages[$contentType] ?? $messages['file'];
        return "{$typeConfig['title']} {$typeConfig['message']}";
    }

    /**
     * Get comprehensive error messages for attachments
     */
    private function getAttachmentErrorMessage(string $contentType, string $error): string
    {
        $baseMessages = [
            'secure_video' => [
                'title' => '❌ Secure Video Upload Failed',
                'generic' => 'Unable to process secure video upload.',
                'specific' => 'Secure video processing failed: '
            ],
            'external_video' => [
                'title' => '❌ External Video Setup Failed',
                'generic' => 'Unable to process external video link.',
                'specific' => 'External video setup failed: '
            ],
            'file' => [
                'title' => '❌ File Upload Failed',
                'generic' => 'Unable to upload file.',
                'specific' => 'File upload failed: '
            ]
        ];

        $typeConfig = $baseMessages[$contentType] ?? $baseMessages['file'];
        
        // Check if it's a specific error or generic
        if (strlen($error) > 50) {
            return "{$typeConfig['title']} {$typeConfig['specific']}{$error}";
        }
        
        return "{$typeConfig['title']} {$typeConfig['generic']}";
    }

    /**
     * Get validation error messages
     */
    private function getValidationErrorMessage(array $errors): string
    {
        $errorCount = count($errors);
        
        if ($errorCount === 1) {
            return "❌ Validation Error: " . $errors[0];
        }
        
        return "❌ There are {$errorCount} validation errors. Please check the form and try again.";
    }

  private function handleSecureVideoUpload(UploadedFile $file, array $attachmentData): array
{
    \Log::info('Starting handleSecureVideoUpload', [
        'file_name' => $file->getClientOriginalName(),
        'file_size' => $file->getSize(),
        'file_mime' => $file->getMimeType(),
        'extension' => $file->getClientOriginalExtension()
    ]);

    // More lenient video file check
    $extension = strtolower($file->getClientOriginalExtension());
    $allowedExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'm4v', '3gp', 'flv'];
    
    if (!in_array($extension, $allowedExtensions)) {
        throw new \Exception("File extension '{$extension}' is not allowed. Please upload a valid video file.");
    }

    try {
        // Use consistent storage path
        $videoId = Str::uuid()->toString();
        $filename = 'secure_video_' . $videoId . '_' . Str::random(8) . '.' . $extension;
        $filePath = 'videos/' . $filename;

        \Log::info('Storing secure video to path: ' . $filePath);

        // Store the file directly in R2
        Storage::disk('r2')->put($filePath, file_get_contents($file->getRealPath()), [
            'ContentType' => $file->getMimeType(),
            'CacheControl' => 'private, max-age=31536000'
        ]);

        // Verify the file was stored
        if (!Storage::disk('r2')->exists($filePath)) {
            throw new \Exception('Failed to store secure video file in R2');
        }

        $fileSize = Storage::disk('r2')->size($filePath);
        \Log::info('Secure video stored successfully', [
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'video_id' => $videoId
        ]);

        return array_merge($attachmentData, [
            'video_id' => $videoId,
            'file_path' => $filePath,
            'file_type' => 'secure_video',
            'file_size' => $fileSize,
            'is_secure' => true,
            'allow_download' => false,
            'thumbnail_url' => null,
        ]);

    } catch (\Exception $e) {
        \Log::error('handleSecureVideoUpload failed', [
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        throw $e;
    }
}

private function storeFileDirectly(UploadedFile $file, string $directory): string
{
    $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $extension = $file->getClientOriginalExtension();
    
    $filename = Str::slug($originalName) . '_' . Str::random(8) . '.' . $extension;
    $filePath = $directory . '/' . $filename;

    // Store in R2 directly
    Storage::disk('r2')->put($filePath, file_get_contents($file), [
        'ContentType' => $file->getMimeType(),
    ]);

    return $filePath;
}

    /**
     * Handle external video URLs (YouTube/Vimeo)
     */
    private function handleExternalVideo(Request $request, array $attachmentData): array
    {
        $videoUrl = $request->input('video_url');
        
        if (!$videoUrl) {
            throw new \Exception('Video URL is required for external videos.');
        }

        $videoData = $this->processExternalVideoUrl($videoUrl);
        
        return array_merge($attachmentData, [
            'video_url' => $videoData['url'],
            'file_type' => 'external_video',
            'thumbnail_url' => $videoData['thumbnail_url'],
            'is_secure' => false,
            'allow_download' => false, // External videos cannot be downloaded
            'external_provider' => $videoData['provider'],
            'external_video_id' => $videoData['video_id'],
        ]);
    }

    /**
     * Process and validate external video URLs
     */
    private function processExternalVideoUrl(string $videoUrl): array
    {
        $youtubePattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        $vimeoPattern = '/vimeo\.com\/(?:.*\/)?(\d+)/';

        if (preg_match($youtubePattern, $videoUrl, $matches)) {
            $videoId = $matches[1];
            return [
                'provider' => 'youtube',
                'video_id' => $videoId,
                'thumbnail_url' => "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg",
                'url' => $videoUrl,
            ];
        }

        if (preg_match($vimeoPattern, $videoUrl, $matches)) {
            $videoId = $matches[1];
            $thumbnailUrl = $this->getVimeoThumbnail($videoId);
            
            return [
                'provider' => 'vimeo',
                'video_id' => $videoId,
                'thumbnail_url' => $thumbnailUrl,
                'url' => $videoUrl,
            ];
        }

        throw new \Exception('Please provide a valid YouTube or Vimeo URL.');
    }

    /**
     * Get Vimeo thumbnail
     */
    private function getVimeoThumbnail(string $videoId): ?string
    {
        try {
            $vimeoData = file_get_contents("https://vimeo.com/api/v2/video/{$videoId}.json");
            $vimeoData = json_decode($vimeoData, true);
            return $vimeoData[0]['thumbnail_large'] ?? $vimeoData[0]['thumbnail_medium'] ?? null;
        } catch (\Exception $e) {
            Log::warning('Failed to fetch Vimeo thumbnail: ' . $e->getMessage());
            return null;
        }
    }

    private function handleFileUpload(UploadedFile $file, array $attachmentData): array
{   
    $fileType = $file->getClientOriginalExtension();
    
    // For videos, use consistent naming in 'videos/' directory
    if ($this->isVideoFile($fileType)) {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = 'video_' . Str::uuid()->toString() . '_' . Str::random(8) . '.' . $fileType;
        $filePath = 'videos/' . $filename; // Consistent videos directory
    } else {
        // For non-video files, use appropriate directories
        $storagePath = $this->getStoragePathForFileType($fileType);
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $filename = Str::slug($originalName) . '_' . Str::random(8) . '.' . $fileType;
        $filePath = $storagePath . '/' . $filename;
    }

    \Log::info('Storing file to path: ' . $filePath);

    // Store in R2
    Storage::disk('r2')->put($filePath, file_get_contents($file), [
        'ContentType' => $file->getMimeType(),
    ]);

    // Verify file was stored
    if (!Storage::disk('r2')->exists($filePath)) {
        throw new \Exception('Failed to store file in R2');
    }

    $fileSize = Storage::disk('r2')->size($filePath);

    $result = array_merge($attachmentData, [
        'file_path' => $filePath,
        'file_type' => $fileType,
        'file_size' => $fileSize,
        'is_secure' => false,
        'allow_download' => true,
    ]);

    \Log::info('File stored successfully', [
        'file_path' => $filePath,
        'file_size' => $fileSize,
        'file_type' => $fileType
    ]);

    return $result;
}

    /**
     * Store file in Storj DCS
     */
    private function storeFileInStorj($file, string $directory): string
    {
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        
        $filename = Str::slug($originalName) . '_' . Str::random(8) . '.' . $extension;
        $filePath = $directory . '/' . $filename;

        // Store in Storj
        Storage::disk('r2')->put($filePath, file_get_contents($file), [
            'ContentType' => $file->getMimeType(),
        ]);

        return $filePath;
    }

    /**
     * Get storage path based on file type
     */
    private function getStoragePathForFileType(string $fileType): string
    {
        $typeMap = [
            'pdf' => 'documents',
            'doc' => 'documents', 'docx' => 'documents',
            'ppt' => 'documents', 'pptx' => 'documents',
            'xls' => 'documents', 'xlsx' => 'documents',
            'txt' => 'documents',
            'jpg' => 'images', 'jpeg' => 'images', 'png' => 'images', 
            'gif' => 'images', 'webp' => 'images', 'bmp' => 'images',
            'mp4' => 'videos', 'mov' => 'videos', 'avi' => 'videos', 
            'mkv' => 'videos', 'webm' => 'videos', 'wmv' => 'videos',
            'mp3' => 'audio', 'wav' => 'audio', 'ogg' => 'audio', 'm4a' => 'audio',
            'zip' => 'archives', 'rar' => 'archives', '7z' => 'archives',
        ];

        return $typeMap[strtolower($fileType)] ?? 'misc';
    }

    /**
 * Check if file is a video - FIXED VERSION
 */
private function isVideoFile($file): bool
{
    // If it's a string (file extension), check directly
    if (is_string($file)) {
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'm4v', '3gp'];
        return in_array(strtolower($file), $videoExtensions);
    }
    
    // If it's an UploadedFile object, check extension and MIME type
    if ($file instanceof UploadedFile) {
        $extension = strtolower($file->getClientOriginalExtension());
        $mimeType = strtolower($file->getMimeType());
        
        $videoExtensions = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'm4v', '3gp'];
        $videoMimeTypes = [
            'video/mp4', 'video/quicktime', 'video/x-msvideo', 'video/x-matroska',
            'video/webm', 'video/x-ms-wmv', 'video/mp4', 'video/3gpp'
        ];
        
        $isValidExtension = in_array($extension, $videoExtensions);
        $isValidMimeType = in_array($mimeType, $videoMimeTypes);
        
        \Log::info('Video file validation', [
            'filename' => $file->getClientOriginalName(),
            'extension' => $extension,
            'mime_type' => $mimeType,
            'is_valid_extension' => $isValidExtension,
            'is_valid_mime_type' => $isValidMimeType
        ]);
        
        return $isValidExtension || $isValidMimeType;
    }
    
    return false;
}

    /**
     * Check if file is an image
     */
    private function isImageFile(string $fileType): bool
    {
        $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'];
        return in_array(strtolower($fileType), $imageExtensions);
    }

    /**
     * Generate thumbnail for images
     */
    private function generateThumbnail($file, string $originalPath): ?string
    {
        try {
            // For now, just use the original image as thumbnail
            // In production, you might want to use Intervention Image to create actual thumbnails
            return $originalPath;
        } catch (\Exception $e) {
            Log::warning('Thumbnail generation failed: ' . $e->getMessage());
            return null;
        }
    }



    /**
 * Debug method to see what's happening with video serving
 */
public function debugVideoServe(Attachment $attachment)
{
    \Log::info('DEBUG VIDEO SERVE', [
        'attachment_id' => $attachment->id,
        'file_path' => $attachment->file_path,
        'file_type' => $attachment->file_type,
        'video_id' => $attachment->video_id,
        'title' => $attachment->title
    ]);

    try {
        // Check file existence
        $exists = Storage::disk('r2')->exists($attachment->file_path);
        $fileSize = $exists ? Storage::disk('r2')->size($attachment->file_path) : 0;
        
        // Get MIME type
        $extension = pathinfo($attachment->file_path, PATHINFO_EXTENSION);
        $mimeType = $this->getVideoMimeType($extension);

        // Try to generate signed URL
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $attachment->file_path,
            now()->addHours(1),
            [
                'ResponseContentType' => $mimeType,
                'ResponseCacheControl' => 'private, max-age=3600'
            ]
        );

        return response()->json([
            'success' => true,
            'attachment' => [
                'id' => $attachment->id,
                'title' => $attachment->title,
                'file_path' => $attachment->file_path,
                'file_type' => $attachment->file_type,
                'file_exists' => $exists,
                'file_size' => $fileSize,
                'mime_type' => $mimeType,
                'is_video' => $this->isVideoFile($attachment->file_type),
                'is_secure_video' => $attachment->file_type === 'secure_video'
            ],
            'signed_url' => $signedUrl,
            'signed_url_length' => strlen($signedUrl)
        ]);

    } catch (\Exception $e) {
        \Log::error('DEBUG VIDEO SERVE ERROR', [
            'attachment_id' => $attachment->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'error' => $e->getMessage(),
            'attachment_id' => $attachment->id
        ], 500);
    }
}

public function serveFile(Attachment $attachment)
{
    \Log::info('Serving file request', [
        'attachment_id' => $attachment->id,
        'file_path' => $attachment->file_path,
        'file_type' => $attachment->file_type,
        'user_id' => auth()->id()
    ]);

    try {
        // Check if file exists in R2
        if (!Storage::disk('r2')->exists($attachment->file_path)) {
            \Log::error('File not found in R2 storage', [
                'file_path' => $attachment->file_path,
                'attachment_id' => $attachment->id
            ]);
            return response()->json([
                'error' => 'File not found in storage',
                'file_path' => $attachment->file_path
            ], 404);
        }

        // For ALL file types, generate a signed URL
        $signedUrl = $this->generateSignedUrl($attachment);
        
        \Log::info('Generated signed URL', [
            'attachment_id' => $attachment->id,
            'signed_url' => $signedUrl,
            'file_type' => $attachment->file_type
        ]);

        // Return JSON with the signed URL
        return response()->json([
            'type' => $attachment->file_type,
            'url' => $signedUrl,
            'stream_url' => $signedUrl, // For backward compatibility
            'title' => $attachment->title,
            'file_type' => $attachment->file_type,
            'mime_type' => $this->getVideoMimeType($attachment->file_type),
            'allow_download' => $attachment->allow_download ?? true,
            'expires_in' => 7200, // 2 hours
            'is_secure' => $attachment->file_type === 'secure_video'
        ]);

    } catch (\Exception $e) {
        \Log::error('File serving failed', [
            'attachment_id' => $attachment->id,
            'error' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);
        
        return response()->json([
            'error' => 'Failed to generate access URL: ' . $e->getMessage(),
            'attachment_id' => $attachment->id
        ], 500);
    }
}

/**
 * Generate signed URL for any file type
 */
private function generateSignedUrl(Attachment $attachment): string
{
    $filePath = $attachment->file_path;
    $mimeType = $this->getVideoMimeType($attachment->file_type);

    try {
        // Generate signed URL with proper content type
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $filePath,
            now()->addHours(2),
            [
                'ResponseContentType' => $mimeType,
                'ResponseCacheControl' => 'private, max-age=7200',
                'ResponseContentDisposition' => 'inline' // Show in browser instead of download
            ]
        );

        return $signedUrl;

    } catch (\Exception $e) {
        \Log::error('Failed to generate signed URL', [
            'file_path' => $filePath,
            'error' => $e->getMessage()
        ]);
        
        // Fallback: generate without additional parameters
        return Storage::disk('r2')->temporaryUrl($filePath, now()->addHours(2));
    }
}

/**
 * Serve regular files as JSON with signed URLs
 */
private function serveRegularFileJson(Attachment $attachment)
{
    try {
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $attachment->file_path,
            now()->addHours(2)
        );

        return response()->json([
            'type' => 'file',
            'url' => $signedUrl,
            'title' => $attachment->title,
            'file_type' => $attachment->file_type,
            'allow_download' => $attachment->allow_download,
            'expires_in' => 7200
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to generate signed URL for file', [
            'attachment_id' => $attachment->id,
            'error' => $e->getMessage()
        ]);
        
        throw new \Exception('Failed to generate file URL: ' . $e->getMessage());
    }
}

/**
 * Direct video streaming endpoint as fallback
 */
public function streamVideoDirect(Attachment $attachment)
{
    if (!auth()->check()) {
        abort(403, 'Authentication required');
    }

    \Log::info('Direct video streaming', [
        'attachment_id' => $attachment->id,
        'file_path' => $attachment->file_path
    ]);

    try {
        if (!Storage::disk('r2')->exists($attachment->file_path)) {
            abort(404, 'Video file not found');
        }

        $filePath = $attachment->file_path;
        $fileSize = Storage::disk('r2')->size($filePath);
        $mimeType = $this->getVideoMimeType(pathinfo($filePath, PATHINFO_EXTENSION));

        // Get the file stream
        $stream = Storage::disk('r2')->readStream($filePath);

        // Handle range requests for video seeking
        $range = request()->header('Range');
        if ($range) {
            return $this->serveVideoChunk($filePath, $fileSize, $mimeType, $range);
        }

        // Return the full video stream
        return response()->stream(function() use ($stream) {
            fpassthru($stream);
        }, 200, [
            'Content-Type' => $mimeType,
            'Content-Length' => $fileSize,
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, max-age=3600',
        ]);

    } catch (\Exception $e) {
        \Log::error('Direct video streaming failed', [
            'attachment_id' => $attachment->id,
            'error' => $e->getMessage()
        ]);
        
        abort(500, 'Unable to stream video');
    }
}

/**
 * Serve secure video as JSON response
 */
private function serveSecureVideoJson(Attachment $attachment)
{
    try {
        $streamUrl = $this->streamingService->generateSecureStreamUrl(
            $attachment->video_id,
            '720p',
            auth()->id(),
            request()->ip()
        );

        \Log::info('Secure video URL generated', [
            'attachment_id' => $attachment->id,
            'stream_url' => $streamUrl
        ]);

        return response()->json([
            'type' => 'secure_video',
            'stream_url' => $streamUrl,
            'title' => $attachment->title,
            'file_path' => $attachment->file_path,
            'is_secure' => true,
            'expires_in' => 7200
        ]);

    } catch (\Exception $e) {
        \Log::error('Secure video serving failed', [
            'attachment_id' => $attachment->id,
            'error' => $e->getMessage()
        ]);
        
        // Fallback to direct serving
        return $this->serveVideoDirectJson($attachment, true);
    }
}

/**
 * Serve regular video as JSON response
 */
private function serveRegularVideoJson(Attachment $attachment)
{
    return $this->serveVideoDirectJson($attachment, false);
}

/**
 * Universal video serving with signed URL
 */
private function serveVideoDirectJson(Attachment $attachment, $isSecure = false)
{
    $filePath = $attachment->file_path;
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeType = $this->getVideoMimeType($extension);

    \Log::info('Generating signed URL for video', [
        'attachment_id' => $attachment->id,
        'file_path' => $filePath,
        'mime_type' => $mimeType,
        'is_secure' => $isSecure
    ]);

    try {
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $filePath,
            now()->addHours(2),
            [
                'ResponseContentType' => $mimeType,
                'ResponseCacheControl' => 'private, max-age=7200'
            ]
        );

        \Log::info('Signed URL generated successfully', [
            'attachment_id' => $attachment->id,
            'signed_url_length' => strlen($signedUrl)
        ]);

        return response()->json([
            'type' => $isSecure ? 'secure_video' : 'regular_video',
            'stream_url' => $signedUrl,
            'title' => $attachment->title,
            'file_path' => $filePath,
            'mime_type' => $mimeType,
            'is_secure' => $isSecure,
            'expires_in' => 7200
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to generate signed URL', [
            'attachment_id' => $attachment->id,
            'error' => $e->getMessage()
        ]);

        // Ultimate fallback - use direct streaming endpoint
        $directUrl = route('stream.video.direct', ['attachment' => $attachment->id]);
        
        return response()->json([
            'type' => $isSecure ? 'secure_video' : 'regular_video',
            'stream_url' => $directUrl,
            'title' => $attachment->title,
            'file_path' => $filePath,
            'is_secure' => $isSecure,
            'note' => 'Using direct streaming fallback'
        ]);
    }
}

/**
 * Serve external video as JSON response
 */
private function serveExternalVideoJson(Attachment $attachment)
{
    return response()->json([
        'type' => 'external_video',
        'embed_url' => $this->getEmbedUrl($attachment->video_url),
        'original_url' => $attachment->video_url,
        'title' => $attachment->title,
        'provider' => $this->getVideoProvider($attachment->video_url)
    ]);
}

/**
 * Universal video file serving for both secure and regular videos
 */
private function serveVideoFile(Attachment $attachment, bool $isSecure = false)
{
    $filePath = $attachment->file_path;
    
    \Log::info('Serving video file', [
        'attachment_id' => $attachment->id,
        'file_path' => $filePath,
        'is_secure' => $isSecure,
        'file_type' => $attachment->file_type
    ]);

    // Check if file exists
    if (!Storage::disk('r2')->exists($filePath)) {
        \Log::error('Video file not found in R2', [
            'attachment_id' => $attachment->id,
            'file_path' => $filePath,
            'storage_disk' => 'r2'
        ]);
        throw new \Exception('Video file not found in storage');
    }

    // Get file info
    $fileSize = Storage::disk('r2')->size($filePath);
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);
    $mimeType = $this->getVideoMimeType($extension);

    \Log::info('Video file info', [
        'file_size' => $fileSize,
        'extension' => $extension,
        'mime_type' => $mimeType,
        'file_exists' => true
    ]);

    try {
        // Generate signed URL with proper headers
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $filePath,
            now()->addHours(2),
            [
                'ResponseContentType' => $mimeType,
                'ResponseCacheControl' => 'private, max-age=7200',
                'ResponseContentDisposition' => 'inline'
            ]
        );

        \Log::info('Generated signed URL for video', [
            'attachment_id' => $attachment->id,
            'signed_url_length' => strlen($signedUrl),
            'expires_at' => now()->addHours(2)->toISOString()
        ]);

        return response()->json([
            'type' => $isSecure ? 'secure_video' : 'regular_video',
            'stream_url' => $signedUrl,
            'title' => $attachment->title,
            'file_path' => $filePath,
            'file_size' => $fileSize,
            'mime_type' => $mimeType,
            'is_secure' => $isSecure,
            'expires_in' => 7200
        ]);

    } catch (\Exception $e) {
        \Log::error('Failed to generate signed URL', [
            'attachment_id' => $attachment->id,
            'file_path' => $filePath,
            'error' => $e->getMessage()
        ]);
        
        // Fallback: try without additional parameters
        try {
            $signedUrl = Storage::disk('r2')->temporaryUrl($filePath, now()->addHours(2));
            
            \Log::info('Generated fallback signed URL', [
                'attachment_id' => $attachment->id,
                'fallback_url' => $signedUrl
            ]);
            
            return response()->json([
                'type' => $isSecure ? 'secure_video' : 'regular_video',
                'stream_url' => $signedUrl,
                'title' => $attachment->title,
                'file_path' => $filePath,
                'file_size' => $fileSize,
                'is_secure' => $isSecure,
                'expires_in' => 7200,
                'note' => 'Using fallback URL'
            ]);
            
        } catch (\Exception $fallbackError) {
            \Log::error('Fallback URL generation also failed', [
                'attachment_id' => $attachment->id,
                'error' => $fallbackError->getMessage()
            ]);
            
            throw new \Exception('Failed to generate video URL: ' . $fallbackError->getMessage());
        }
    }
}


/**
 * Get video provider for external videos
 */
private function getVideoProvider($videoUrl): string
{
    if (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
        return 'youtube';
    }
    if (str_contains($videoUrl, 'vimeo.com')) {
        return 'vimeo';
    }
    return 'external';
}
/**
 * Serve regular video files (non-secure)
 */
private function serveRegularVideo(Attachment $attachment)
{
    try {
        if (!Storage::disk('r2')->exists($attachment->file_path)) {
            abort(404, 'Video file not found');
        }

        $filePath = $attachment->file_path;
        $fileSize = Storage::disk('r2')->size($filePath);
        $mimeType = $this->getVideoMimeType(pathinfo($filePath, PATHINFO_EXTENSION));

        // Generate a temporary signed URL for the video
        $signedUrl = Storage::disk('r2')->temporaryUrl(
            $filePath,
            now()->addHours(1),
            [
                'ResponseContentType' => $mimeType,
                'ResponseCacheControl' => 'private, max-age=3600'
            ]
        );

        // Redirect to the signed URL
        return redirect($signedUrl);

    } catch (\Exception $e) {
        Log::error('Regular video serving error: ' . $e->getMessage());
        abort(500, 'Unable to serve video');
    }
}

/**
 * Serve secure video with proper streaming
 */
private function serveSecureVideo(Attachment $attachment)
{
    try {
        if (!Storage::disk('r2')->exists($attachment->file_path)) {
            abort(404, 'Secure video file not found');
        }

        $filePath = $attachment->file_path;
        $fileSize = Storage::disk('r2')->size($filePath);
        $mimeType = $this->getVideoMimeType(pathinfo($filePath, PATHINFO_EXTENSION));

        // For secure videos, use the streaming service
        $streamUrl = $this->streamingService->generateSecureStreamUrl(
            $attachment->video_id,
            '720p',
            auth()->id(),
            request()->ip()
        );

        return redirect($streamUrl);

    } catch (\Exception $e) {
        Log::error('Secure video serving error: ' . $e->getMessage());
        
        // Fallback: try to serve directly
        return $this->serveRegularVideo($attachment);
    }
}

/**
 * Get embed URL for external videos
 */
private function getEmbedUrl($videoUrl)
{
    $youtubePattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
    $vimeoPattern = '/vimeo\.com\/(?:.*\/)?(\d+)/';

    if (preg_match($youtubePattern, $videoUrl, $matches)) {
        return "https://www.youtube.com/embed/{$matches[1]}?rel=0&modestbranding=1";
    }

    if (preg_match($vimeoPattern, $videoUrl, $matches)) {
        return "https://player.vimeo.com/video/{$matches[1]}";
    }

    return $videoUrl;
}



/**
 * Serve video chunk for range requests
 */
private function serveVideoChunk($filePath, $fileSize, $mimeType, $range)
{
    try {
        list($sizeUnit, $range) = explode('=', $range);
        list($start, $end) = explode('-', $range);

        $start = (int) $start;
        $end = $end ? (int) $end : $fileSize - 1;
        
        // Ensure end doesn't exceed file size
        if ($end >= $fileSize) {
            $end = $fileSize - 1;
        }
        
        $length = $end - $start + 1;

        $stream = Storage::disk('r2')->readStream($filePath);
        if ($stream === false) {
            throw new \Exception('Failed to open file stream');
        }

        fseek($stream, $start);

        $chunk = fread($stream, $length);
        fclose($stream);

        return response($chunk, 206, [
            'Content-Type' => $mimeType,
            'Content-Length' => $length,
            'Content-Range' => "bytes {$start}-{$end}/{$fileSize}",
            'Accept-Ranges' => 'bytes',
            'Cache-Control' => 'private, no-cache',
        ]);

    } catch (\Exception $e) {
        \Log::error('Video chunk serving failed', [
            'file_path' => $filePath,
            'error' => $e->getMessage()
        ]);
        
        abort(500, 'Unable to serve video chunk');
    }
}

/**
 * Enhanced MIME type detection
 */
private function getVideoMimeType(string $fileType): string
{
    $mimeMap = [
        // Video types
        'mp4' => 'video/mp4',
        'm4v' => 'video/mp4',
        'mov' => 'video/quicktime',
        'qt' => 'video/quicktime',
        'avi' => 'video/x-msvideo',
        'mkv' => 'video/x-matroska',
        'webm' => 'video/webm',
        'wmv' => 'video/x-ms-wmv',
        'flv' => 'video/x-flv',
        '3gp' => 'video/3gpp',
        '3g2' => 'video/3gpp2',
        
        // Audio types
        'mp3' => 'audio/mpeg',
        'wav' => 'audio/wav',
        'ogg' => 'audio/ogg',
        'm4a' => 'audio/mp4',
        
        // Document types
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        
        // Image types
        'jpg' => 'image/jpeg', 
        'jpeg' => 'image/jpeg',
        'png' => 'image/png', 
        'gif' => 'image/gif', 
        'webp' => 'image/webp',
    ];

    return $mimeMap[strtolower($fileType)] ?? 'application/octet-stream';
}


  
    /**
     * Serve regular file with temporary access
     */
    private function serveRegularFile(Attachment $attachment)
    {
        try {
            // Generate temporary signed URL (valid for 1 hour)
            $signedUrl = Storage::disk('r2')->temporaryUrl(
                $attachment->file_path,
                now()->addHours(1)
            );

            // For downloadable files, redirect to signed URL
            if ($attachment->allow_download && $this->isDownloadableFile($attachment->file_type)) {
                return redirect($signedUrl);
            }

            // For preview files, proxy the content
            return response()->stream(function() use ($signedUrl) {
                $context = stream_context_create([
                    'http' => [
                        'header' => 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    ],
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ]);
                
                $content = file_get_contents($signedUrl, false, $context);
                echo $content;
            }, 200, [
                'Content-Type' => $this->getMimeType($attachment->file_type),
                'Content-Disposition' => 'inline; filename="' . $attachment->title . '"',
                'Cache-Control' => 'private, max-age=3600',
            ]);
            
        } catch (\Exception $e) {
            Log::error('File serving error: ' . $e->getMessage());
            abort(404, 'File not available');
        }
    }

    /**
     * Download file
     */
    public function downloadFile(Attachment $attachment)
    {
        if (!auth()->check() || !$this->userHasCourseAccess($attachment->module->course, auth()->id())) {
            abort(403, 'Access denied');
        }

        if (!$attachment->allow_download || $attachment->file_type === 'secure_video') {
            abort(403, 'Download not allowed for this file');
        }

        try {
            $signedUrl = Storage::disk('r2')->temporaryUrl(
                $attachment->file_path,
                now()->addMinutes(30)
            );

            return redirect($signedUrl);
        } catch (\Exception $e) {
            Log::error('File download error: ' . $e->getMessage());
            abort(404, 'File not available for download');
        }
    }

   /**
     * Check if user has course access - FIX THIS BASED ON YOUR BUSINESS LOGIC
     */
    private function userHasCourseAccess(Course $course, int $userId): bool
    {
        // TODO: Implement your actual course access logic
        // For now, return true to test the video streaming
        // In production, implement proper access checks
        
        // Example implementation:
        // return \App\Models\UserCourse::where('user_id', $userId)
        //     ->where('course_id', $course->id)
        //     ->exists();
        
        return true; // Temporary for testing
    }

    /**
     * Check if file type is downloadable
     */
    private function isDownloadableFile(string $fileType): bool
    {
        $downloadableTypes = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'mp3', 'wav'];
        return in_array($fileType, $downloadableTypes);
    }

    /**
     * Get MIME type for file
     */
    private function getMimeType(string $fileType): string
    {
        $mimeMap = [
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png', 'gif' => 'image/gif', 'webp' => 'image/webp',
            'mp4' => 'video/mp4', 'mov' => 'video/quicktime', 'avi' => 'video/x-msvideo',
            'mp3' => 'audio/mpeg', 'wav' => 'audio/wav', 'ogg' => 'audio/ogg',
            'zip' => 'application/zip',
        ];

        return $mimeMap[$fileType] ?? 'application/octet-stream';
    }



    /**
     * Extract thumbnail from external video URLs
     */
    private function extractVideoThumbnail($videoUrl)
    {
        try {
            // YouTube
            if (str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be')) {
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $videoUrl, $matches);
                if (isset($matches[1])) {
                    return "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
                }
            }
            
            // Vimeo
            if (str_contains($videoUrl, 'vimeo.com')) {
                preg_match('/vimeo\.com\/(?:.*\/)?(\d+)/', $videoUrl, $matches);
                if (isset($matches[1])) {
                    $vimeoData = file_get_contents("https://vimeo.com/api/v2/video/{$matches[1]}.json");
                    $vimeoData = json_decode($vimeoData, true);
                    return $vimeoData[0]['thumbnail_large'] ?? null;
                }
            }
        } catch (\Exception $e) {
            // Silently fail - thumbnail is optional
        }

        return null;
    }



    /**
     * Generate offline access
     */
    public function generateOfflineAccess(Request $request, Attachment $attachment)
    {
        $request->validate([
            'device_id' => 'required|string|max:255',
        ]);

        if ($attachment->file_type !== 'secure_video') {
            abort(404, 'Video not available for offline access');
        }

        $offlineData = $this->streamingService->generateOfflineAccess(
            $attachment->video_id,
            Auth::id(),
            $request->device_id
        );

        return response()->json([
            'success' => true,
            'offline_access' => $offlineData,
        ]);
    }


/**
 * Update an attachment for a module.
 */
public function updateAttachment(Request $request, Course $course, CourseModule $module, Attachment $attachment)
{
    $validator = Validator::make($request->all(), [
        'title' => 'required|string|max:255',
        'description' => 'nullable|string',
        'file' => 'nullable|file|mimes:pdf,mp4,mov,avi,jpg,jpeg,png,doc,docx,zip|max:512000',
        'video_url' => 'nullable|url',
        'order' => 'required|integer|min:0',
    ]);

    if ($validator->fails()) {
        return redirect()->back()
            ->withErrors($validator)
            ->withInput();
    }

    $data = $validator->validated();

    $updateData = [
        'title' => $data['title'],
        'description' => $data['description'],
        'order' => $data['order'],
    ];

    // Handle file upload
    if ($request->hasFile('file')) {
        // Delete old file if exists
        if ($attachment->file_path) {
            Storage::disk('public')->delete($attachment->file_path);
        }

        $file = $request->file('file');
        $filePath = $file->store('attachments', 'public');
        
        $updateData['file_path'] = $filePath;
        $updateData['file_type'] = $file->getClientOriginalExtension();
        $updateData['file_size'] = $file->getSize();
        $updateData['video_url'] = null;
        $updateData['thumbnail_url'] = null;
        
    } elseif (!empty($data['video_url'])) {
        // Handle video URL update
        $updateData['video_url'] = $data['video_url'];
        $updateData['file_type'] = 'external_video';
        
        // Delete old file if switching from file to video URL
        if ($attachment->file_path) {
            Storage::disk('public')->delete($attachment->file_path);
            $updateData['file_path'] = null;
            $updateData['file_size'] = null;
        }

        // Try to extract YouTube video ID for thumbnail
        $youtubePattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
        preg_match($youtubePattern, $data['video_url'], $matches);
        
        if (isset($matches[1])) {
            $updateData['thumbnail_url'] = "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
        }
    }

    $attachment->update($updateData);

    $message = "✅ Attachment '{$attachment->title}' updated successfully!";
    
    return $this->redirectToModules($course, $message);
}



    public function destroyAttachment(Course $course, CourseModule $module, Attachment $attachment)
    {
        $attachmentTitle = $attachment->title;
        $attachmentType = $attachment->getDisplayFileType();

        // Delete the file from storage
        if ($attachment->file_path && Storage::disk('r2')->exists($attachment->file_path)) {
            Storage::disk('r2')->delete($attachment->file_path);
        }
        
        $attachment->delete();

        $message = "🗑️ {$attachmentType} '{$attachmentTitle}' deleted successfully.";
    
        return $this->redirectToModules($course, $message);
    }



 


}
<?php

namespace App\Http\Controllers\Instructor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseModule;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class InstructorCourseController extends Controller
{
    /**
     * Display a listing of the instructor's courses.
     */
    public function index()
    {
        $userId = Auth::id();
        
        $courses = Course::with(['category'])
            ->withCount('modules')
            ->byInstructor($userId)
            ->latest()
            ->paginate(10);

        // Calculate accurate stats
        $stats = [
            'total' => Course::byInstructor($userId)->count(),
            'draft' => Course::byInstructor($userId)->where('status', Course::STATUS_DRAFT)->count(),
            'pending_review' => Course::byInstructor($userId)->where('status', Course::STATUS_PENDING_REVIEW)->count(),
            'approved' => Course::byInstructor($userId)->where('status', Course::STATUS_APPROVED)->count(),
            'published' => Course::byInstructor($userId)->where('status', Course::STATUS_APPROVED)
                ->where('is_published', true)->count(),
        ];

        return view('instructor.courses.index', compact('courses', 'stats'));
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

        $categories = Category::all();

        return view('instructor.courses.create', compact('levels', 'categories'));
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'objectives' => 'nullable|string|min:50',
            'target_audience' => 'nullable|string|min:50',
            'requirements' => 'nullable|string|min:50',
            'price' => $request->has('is_premium') ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'duration' => 'nullable|integer|min:0',
            'level' => 'required|integer|in:1,2,3,4,5',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['user_id'] = Auth::id();
        $data['is_premium'] = $request->has('is_premium');
        $data['status'] = Course::STATUS_DRAFT;

        // Set price for non-premium courses
        if (!$data['is_premium']) {
            $data['price'] = 0;
        }

        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('courses', 'public');
        }

        $course = Course::create($data);

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Course created successfully! Now add your course modules and content.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course)
    {
        // Ensure instructor can only view their own courses
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $course->load(['category', 'modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);

        // Fix: Use userCourses relationship
        $enrollmentStats = [
            'total_students' => $course->user()->count(),
            'total_revenue' => $course->users()->sum('amount_paid'),
        ];

        return view('instructor.courses.show', compact('course', 'enrollmentStats'));
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $levels = [
            1 => 'Beginner',
            2 => 'Intermediate',
            3 => 'Advanced',
            4 => 'Expert',
            5 => 'Beginner to Advanced',
        ];

        $categories = Category::all();

        return view('instructor.courses.edit', compact('course', 'levels', 'categories'));
    }

    /**
     * Update the specified course in storage.
     */
    public function update(Request $request, Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:100',
            'objectives' => 'nullable|string|min:50',
            'target_audience' => 'nullable|string|min:50',
            'requirements' => 'nullable|string|min:50',
            'price' => $request->has('is_premium') ? 'required|numeric|min:0' : 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'duration' => 'nullable|integer|min:0',
            'level' => 'required|integer|in:1,2,3,4,5',
            'category_id' => 'required|exists:categories,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_premium'] = $request->has('is_premium');

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($course->image) {
                Storage::disk('public')->delete($course->image);
            }
            
            $imagePath = $request->file('image')->store('courses', 'public');
            $data['image'] = $imagePath;
        }

        // Set price for non-premium courses
        if (!$data['is_premium']) {
            $data['price'] = 0;
        }

        $course->update($data);

        return redirect()->route('instructor.courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete course image if exists
        if ($course->image) {
            Storage::disk('public')->delete($course->image);
        }

        // Delete all modules and attachments
        foreach ($course->modules as $module) {
            foreach ($module->attachments as $attachment) {
                if ($attachment->file_path) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }
            $module->delete();
        }

        $course->delete();

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * Submit course for review.
     */
    public function submitForReview(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Check if course can be submitted for review with proper messaging
        if (!$course->canBeSubmittedForReview()) {
            $message = 'Course cannot be submitted for review. ';
            
            if ($course->status == Course::STATUS_PENDING_REVIEW) {
                $message .= 'Course is already submited and under review. ';
            }
            
            if ($course->modules()->count() === 0) {
                $message .= 'You need to add at least one module. ';
            }
            
            if (!$course->title || !$course->description || !$course->category_id) {
                $message .= 'Please complete all required course information.';
            }
            
            return redirect()->back()
                ->with('error', trim($message));
        }

        $course->update([
            'status' => Course::STATUS_PENDING_REVIEW
        ]);

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course submitted for review successfully! It will be reviewed within 2-3 business days.');
    }

    /**
     * Withdraw course from review.
     */
    public function withdrawFromReview(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if (!$course->isPendingReview()) {
            return redirect()->back()
                ->with('error', 'Course is not currently under review. Current status: ' . $course->status);
        }

        $course->update([
            'status' => Course::STATUS_DRAFT
        ]);

        return redirect()->route('instructor.courses.index')
            ->with('success', 'Course withdrawn from review. You can make changes and resubmit.');
    }

    /**
     * Show the form for managing course modules.
     */
    public function modules(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $course->load(['modules' => function($query) {
            $query->orderBy('order');
        }, 'modules.attachments']);

        return view('instructor.courses.modules', compact('course'));
    }

    /**
     * Store a new module for the course.
     */
    public function storeModule(Request $request, Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_free' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_free'] = $request->has('is_free');

        $course->modules()->create($data);

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Module added successfully.');
    }

    /**
     * Update a module for the course.
     */
    public function updateModule(Request $request, Course $course, CourseModule $module)
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order' => 'required|integer|min:0',
            'is_free' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_free'] = $request->has('is_free');

        $module->update($data);

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Delete a module from the course.
     */
    public function destroyModule(Course $course, CourseModule $module)
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        // Delete all attachments for this module
        foreach ($module->attachments as $attachment) {
            if ($attachment->file_path) {
                Storage::disk('public')->delete($attachment->file_path);
            }
            $attachment->delete();
        }

        $module->delete();

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Module deleted successfully.');
    }



    /**
     * Store an attachment for a module.
     */
    public function storeAttachment(Request $request, Course $course, CourseModule $module)
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id) {
            abort(403, 'Unauthorized action.');
        }

        // if (!$course->canBeEditedByInstructor()) {
        //     return redirect()->back()
        //         ->with('error', 'You can only add attachments to courses that are in draft or rejected status.');
        // }

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

        $attachmentData = [
            'title' => $data['title'],
            'description' => $data['description'],
            'order' => $data['order'],
        ];

        // Handle file upload or video URL
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filePath = $file->store('attachments', 'public');
            
            $attachmentData['file_path'] = $filePath;
            $attachmentData['file_type'] = $file->getClientOriginalExtension();
            $attachmentData['file_size'] = $file->getSize();
            
        } elseif (!empty($data['video_url'])) {
            $attachmentData['video_url'] = $data['video_url'];
            $attachmentData['file_type'] = 'external_video';
            
            // Try to extract YouTube video ID for thumbnail
            $youtubePattern = '/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/';
            preg_match($youtubePattern, $data['video_url'], $matches);
            
            if (isset($matches[1])) {
                $attachmentData['thumbnail_url'] = "https://img.youtube.com/vi/{$matches[1]}/hqdefault.jpg";
            }
        } else {
            return redirect()->back()
                ->withErrors(['file' => 'Either a file or video URL is required.'])
                ->withInput();
        }

        $module->attachments()->create($attachmentData);

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Attachment added successfully.');
    }

    /**
     * Update an attachment for a module.
     */
    public function updateAttachment(Request $request, Course $course, CourseModule $module, Attachment $attachment)
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id || $attachment->module_id !== $module->id) {
            abort(403, 'Unauthorized action.');
        }

        // if (!$course->canBeEditedByInstructor()) {
        //     return redirect()->back()
        //         ->with('error', 'You can only edit attachments of courses that are in draft or rejected status.');
        // }

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

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Attachment updated successfully.');
    }

    /**
     * Delete an attachment from a module.
     */
    public function destroyAttachment(Course $course, CourseModule $module, Attachment $attachment)
    {
        if ($course->user_id !== Auth::id() || $module->course_id !== $course->id || $attachment->module_id !== $module->id) {
            abort(403, 'Unauthorized action.');
        }

        // if (!$course->canBeEditedByInstructor()) {
        //     return redirect()->back()
        //         ->with('error', 'You can only delete attachments from courses that are in draft or rejected status.');
        // }

        // Delete the file from storage
        if ($attachment->file_path) {
            Storage::disk('public')->delete($attachment->file_path);
        }
        
        $attachment->delete();

        return redirect()->route('instructor.courses.modules', $course)
            ->with('success', 'Attachment deleted successfully.');
    }

    /**
     * Show course analytics and enrollment data.
     */
    public function analytics(Course $course)
    {
        if ($course->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $enrollments = $course->users()
            ->withPivot('amount_paid', 'purchased_at')
            ->orderBy('purchased_at', 'desc')
            ->paginate(10);

        $stats = [
            'total_enrollments' => $course->users()->count(),
            'total_revenue' => $course->users()->sum('amount_paid'),
            'free_enrollments' => $course->users()->wherePivot('amount_paid', 0)->count(),
            'paid_enrollments' => $course->users()->wherePivot('amount_paid', '>', 0)->count(),
        ];

        return view('instructor.courses.analytics', compact('course', 'enrollments', 'stats'));
    }


    
}
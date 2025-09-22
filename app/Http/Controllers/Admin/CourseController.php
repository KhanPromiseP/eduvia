<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\CourseModule;
use App\Models\Attachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index()
    {
        $courses = Course::withCount('modules')->latest()->paginate(10);
        
        
        return view('admin.courses.index', compact('courses'));
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
     * Store a new module for the course.
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
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_free'] = $request->has('is_free');

        $course->modules()->create($data);

        return redirect()->route('admin.courses.modules', $course)
            ->with('success', 'Module added successfully.');
    }

    /**
     * Update a module for the course.
     */
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
                ->withErrors($validator)
                ->withInput();
        }

        $data = $validator->validated();
        $data['is_free'] = $request->has('is_free');

        $module->update($data);

        return redirect()->route('admin.courses.modules', $course)
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Delete a module from the course.
     */
    public function destroyModule(Course $course, CourseModule $module)
    {
        // Delete all attachments for this module
        foreach ($module->attachments as $attachment) {
            Storage::disk('public')->delete($attachment->file_path);
            $attachment->delete();
        }

        $module->delete();

        return redirect()->route('admin.courses.modules', $course)
            ->with('success', 'Module deleted successfully.');
    }


    /**
 * Store an attachment for a module.
 */
public function storeAttachment(Request $request, Course $course, CourseModule $module)
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

    return redirect()->route('admin.courses.modules', $course)
        ->with('success', 'Attachment added successfully.');
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

    return redirect()->route('admin.courses.modules', $course)
        ->with('success', 'Attachment updated successfully.');
}





    /**
     * Delete an attachment from a module.
     */
    public function destroyAttachment(Course $course, CourseModule $module, Attachment $attachment)
    {
        // Delete the file from storage
        Storage::disk('public')->delete($attachment->file_path);
        
        $attachment->delete();

        return redirect()->route('admin.courses.modules', $course)
            ->with('success', 'Attachment deleted successfully.');
    }

    /**
     * Toggle course publish status.
     */
    public function togglePublish(Course $course)
    {
        $course->update([
            'is_published' => !$course->is_published
        ]);

        $status = $course->is_published ? 'published' : 'unpublished';

        return redirect()->route('admin.courses.index')
            ->with('success', "Course {$status} successfully.");
    }
}
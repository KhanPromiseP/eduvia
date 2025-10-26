@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('instructor.courses.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700 mb-4">
            <i class="fas fa-arrow-left mr-2"></i> Back to Courses
        </a>
        
        <div class="flex flex-col md:flex-row md:items-center md:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $course->title }}</h1>
                <p class="text-gray-600 mt-1">{{ $course->description }}</p>
            </div>
            <div class="mt-4 md:mt-0 flex space-x-3">
                <!-- Edit Button - Always Visible -->
                <a href="{{ route('instructor.courses.edit', $course) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-edit mr-2"></i> Edit Course
                </a>
                
                <a href="{{ route('instructor.courses.modules', $course) }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-layer-group mr-2"></i> Manage Modules
                </a>

                <a href="{{ route('instructor.courses.analytics', $course) }}" 
                   class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-chart-bar mr-2"></i> Analytics
                </a>
            </div>
        </div>
    </div>

    <!-- Status Messages -->
    @if($course->status === 'rejected' && $course->review_notes)
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 mt-0.5"></i>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">Course Rejected</h3>
                <div class="mt-1 text-sm text-red-700">
                    <p><strong>Review Notes:</strong> {{ $course->review_notes }}</p>
                    @if($course->reviewed_at)
                    <p class="mt-1"><strong>Reviewed on:</strong> {{ $course->reviewed_at->format('M d, Y') }}</p>
                    @endif
                </div>
                <div class="mt-2">
                    <a href="{{ route('instructor.courses.edit', $course) }}" 
                       class="inline-flex items-center text-sm font-medium text-red-800 hover:text-red-900">
                        <i class="fas fa-edit mr-1"></i> Make changes and resubmit
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($course->status === 'approved')
    @if($course->is_published)
        <!-- Published and Live Message - More celebratory -->
        <div class="mb-6 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-300 rounded-lg p-4 shadow-sm">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-rocket text-green-600"></i>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-bold text-green-800 flex items-center">
                        Course is Live on Eduvia! 
                        <span class="ml-2 text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">ACTIVE</span>
                    </h3>
                    <div class="mt-2 text-sm text-green-700 space-y-2">
                        <p class="font-semibold">🎉 Congratulations! Your course is now available to students worldwide.</p>
                        <p>Students can enroll and start learning from your content immediately.</p>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3 text-xs">
                            @if($course->review_notes)
                                <div>
                                    <span class="font-medium">Approval Feedback:</span>
                                    <p class="text-green-600">{{ $course->review_notes }}</p>
                                </div>
                            @endif
                            @if($course->reviewed_at)
                                <div>
                                    <span class="font-medium">Approval Date:</span>
                                    <p>{{ $course->reviewed_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                            @if($course->updated_at)
                                <div>
                                    <span class="font-medium">Published Date:</span>
                                    <p>{{ $course->updated_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="font-medium">Current Status:</span>
                                <p class="text-green-600 font-semibold">Live & Accepting Enrollments</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Approved but Not Published Message -->
        <div class="mb-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-yellow-600"></i>
                    </div>
                </div>
                <div class="ml-3 flex-1">
                    <h3 class="text-sm font-bold text-yellow-800 flex items-center">
                        Course Approved - Ready to Publish
                        <span class="ml-2 text-xs bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full">APPROVED</span>
                    </h3>
                    <div class="mt-2 text-sm text-yellow-700 space-y-2">
                        <p class="font-semibold">✅ Your course has passed review and is ready for publishing.</p>
                        <p>After publish, it will be live on Eduvia an accept enrollments.</p>
                        
                        @if($course->review_notes)
                            <div class="mt-2 p-2 bg-yellow-100 rounded border border-yellow-200">
                                <span class="font-medium">Approval Notes:</span>
                                <p class="text-yellow-800">{{ $course->review_notes }}</p>
                            </div>
                        @endif
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3 text-xs">
                            @if($course->reviewed_at)
                                <div>
                                    <span class="font-medium">Approved on:</span>
                                    <p>{{ $course->reviewed_at->format('M d, Y') }}</p>
                                </div>
                            @endif
                            <div>
                                <span class="font-medium">Next Step:</span>
                                <p class="text-yellow-800 font-semibold">Publish to go live</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                        <form action="{{ route('admin.courses.toggle-publish', $course) }}" method="POST" class="inline">
                            @csrf
                            <button type="submit"
                                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition font-medium">
                                <i class="fas fa-eye mr-2"></i> Publish Course Now
                            </button>
                        </form>
                        <span class="text-xs text-yellow-600 mt-2 sm:mt-0">
                            This will make your course visible to all students
                        </span>
                         <span class="text-xs text-yellow-800 mt-2 sm:mt-0">
                            You can poblish your course after approval or eduvia team.
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Course Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Course Info Card -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Information</h3>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Category</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $course->category->name }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Level</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @php
                                    $levels = [
                                        1 => 'Beginner',
                                        2 => 'Intermediate',
                                        3 => 'Advanced',
                                        4 => 'Expert',
                                        5 => 'Beginner to Advanced'
                                    ];
                                @endphp
                                {{ $levels[$course->level] ?? 'Unknown' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Price</label>
                            <p class="mt-1 text-sm text-gray-900">
                                @if($course->is_premium)
                                    ${{ number_format($course->price, 2) }}
                                @else
                                    <span class="text-green-600 font-medium">Free</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Duration</label>
                            <p class="mt-1 text-sm textgray-900">
                                {{ $course->duration ? $course->duration . ' hours' : 'Not specified' }}
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Status</label>
                            <p class="mt-1">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'pending_review' => 'bg-yellow-100 text-yellow-800',
                                        'approved' => 'bg-green-100 text-green-800',
                                        'published' => 'bg-blue-100 text-blue-800',
                                        'rejected' => 'bg-red-100 text-red-800'
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Draft',
                                        'pending_review' => 'Pending Review',
                                        'approved' => 'Approved',
                                        'published' => 'Published',
                                        'rejected' => 'Rejected'
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$course->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ $statusLabels[$course->status] ?? $course->status }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Created</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $course->created_at->format('M d, Y') }}</p>
                        </div>
                    </div>

                    @if($course->objectives)
                    <div class="mt-6">
                        <label class="text-sm font-medium text-gray-500">Learning Objectives</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $course->objectives }}</p>
                    </div>
                    @endif

                    @if($course->target_audience)
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-500">Target Audience</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $course->target_audience }}</p>
                    </div>
                    @endif

                    @if($course->requirements)
                    <div class="mt-4">
                        <label class="text-sm font-medium text-gray-500">Requirements</label>
                        <p class="mt-1 text-sm text-gray-900 whitespace-pre-line">{{ $course->requirements }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Modules Overview -->
            <!-- Enhanced Modules Overview with Toggle Functionality -->
<div class="bg-white shadow rounded-lg">
    <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
        <h3 class="text-lg font-medium text-gray-900">Course Modules & Content</h3>
        <div class="flex items-center space-x-4">
            <span class="text-sm text-gray-500">{{ $course->modules->count() }} modules</span>
            <button onclick="toggleAllModules()" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                <i class="fas fa-expand mr-1"></i> Expand All
            </button>
        </div>
    </div>
    <div class="p-6">
        @if($course->modules->count() > 0)
        <div class="space-y-4">
            @foreach($course->modules->sortBy('order') as $module)
            <div class="border border-gray-200 rounded-lg overflow-hidden hover:border-gray-300 transition-colors duration-200">
                <!-- Module Header - Clickable -->
                <div class="bg-gray-50 px-6 py-4 flex justify-between items-center cursor-pointer group"
                     onclick="toggleModule({{ $module->id }})">
                    <div class="flex items-center space-x-4">
                        <div class="transform group-hover:scale-110 transition-transform duration-200">
                            <i class="fas fa-chevron-down text-gray-400 text-sm" id="module-icon-{{ $module->id }}"></i>
                        </div>
                        <div>
                            <h4 class="font-medium text-gray-900 text-lg">{{ $module->title }}</h4>
                            @if($module->description)
                                <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center space-x-4 text-sm text-gray-500">
                            <span class="bg-white px-2 py-1 rounded border">Order: {{ $module->order }}</span>
                            <span class="bg-white px-2 py-1 rounded border">Items: {{ $module->attachments->count() }}</span>
                            @if($module->is_free)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-semibold">
                                    <i class="fas fa-unlock mr-1"></i> Free Preview
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Module Content - Collapsible -->
                <div class="module-content hidden border-t border-gray-100" id="module-content-{{ $module->id }}">
                    <div class="p-6">
                        @if($module->attachments->count() > 0)
                        <div class="space-y-4">
                            <h5 class="text-md font-semibold text-gray-700 mb-4 flex items-center">
                                <i class="fas fa-file-alt mr-2 text-indigo-600"></i>
                                Module Content ({{ $module->attachments->count() }} items)
                            </h5>
                            
                            @foreach($module->attachments->sortBy('order') as $attachment)
                            <div class="border border-gray-200 rounded-lg p-4 hover:border-indigo-200 hover:bg-indigo-50 transition-all duration-200">
                                <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                    <!-- Attachment Info -->
                                    <div class="flex-1">
                                        <div class="flex items-start space-x-4">
                                            <!-- File Type Icon -->
                                            <div class="flex-shrink-0">
                                                @if($attachment->file_type === 'pdf')
                                                    <div class="bg-red-100 p-3 rounded-lg">
                                                        <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                                                    </div>
                                                @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm']))
                                                    <div class="bg-purple-100 p-3 rounded-lg">
                                                        <i class="fas fa-video text-purple-600 text-xl"></i>
                                                    </div>
                                                @elseif($attachment->file_type === 'external_video')
                                                    <div class="bg-red-100 p-3 rounded-lg">
                                                        <i class="fab fa-youtube text-red-600 text-xl"></i>
                                                    </div>
                                                @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                    <div class="bg-green-100 p-3 rounded-lg">
                                                        <i class="fas fa-image text-green-600 text-xl"></i>
                                                    </div>
                                                @elseif(in_array($attachment->file_type, ['doc', 'docx']))
                                                    <div class="bg-blue-100 p-3 rounded-lg">
                                                        <i class="fas fa-file-word text-blue-600 text-xl"></i>
                                                    </div>
                                                @elseif(in_array($attachment->file_type, ['mp3', 'wav', 'ogg']))
                                                    <div class="bg-yellow-100 p-3 rounded-lg">
                                                        <i class="fas fa-music text-yellow-600 text-xl"></i>
                                                    </div>
                                                @elseif($attachment->file_type === 'zip')
                                                    <div class="bg-orange-100 p-3 rounded-lg">
                                                        <i class="fas fa-file-archive text-orange-600 text-xl"></i>
                                                    </div>
                                                @else
                                                    <div class="bg-gray-100 p-3 rounded-lg">
                                                        <i class="fas fa-file text-gray-600 text-xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Attachment Details -->
                                            <div class="flex-1 min-w-0">
                                                <h6 class="font-semibold text-gray-900 text-lg mb-2">{{ $attachment->title }}</h6>
                                                
                                                <!-- Metadata -->
                                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm text-gray-600 mb-3">
                                                    <div class="space-y-1">
                                                        <div class="flex items-center">
                                                            <i class="fas fa-tag mr-2 w-4 text-gray-400"></i>
                                                            <span class="font-medium">Type:</span>
                                                            <span class="ml-2 bg-gray-200 px-2 py-1 rounded text-xs font-bold uppercase">
                                                                {{ $attachment->file_type }}
                                                            </span>
                                                        </div>
                                                        <div class="flex items-center">
                                                            <i class="fas fa-sort-numeric-up mr-2 w-4 text-gray-400"></i>
                                                            <span class="font-medium">Order:</span>
                                                            <span class="ml-2">{{ $attachment->order }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="space-y-1">
                                                        @if($attachment->file_size)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-weight-hanging mr-2 w-4 text-gray-400"></i>
                                                            <span class="font-medium">Size:</span>
                                                            <span class="ml-2">{{ formatFileSize($attachment->file_size) }}</span>
                                                        </div>
                                                        @endif
                                                        @if($attachment->duration)
                                                        <div class="flex items-center">
                                                            <i class="fas fa-clock mr-2 w-4 text-gray-400"></i>
                                                            <span class="font-medium">Duration:</span>
                                                            <span class="ml-2">{{ $attachment->duration }} min</span>
                                                        </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <!-- Description -->
                                                @if($attachment->description)
                                                <div class="bg-white rounded-lg p-3 border border-gray-200">
                                                    <p class="text-sm text-gray-700">{{ $attachment->description }}</p>
                                                </div>
                                                @endif

                                                <!-- External URL -->
                                                @if($attachment->video_url)
                                                <div class="mt-2">
                                                    <span class="text-xs text-gray-500">External URL: </span>
                                                    <a href="{{ $attachment->video_url }}" target="_blank" class="text-xs text-blue-600 hover:text-blue-800 break-all">
                                                        {{ $attachment->video_url }}
                                                    </a>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="flex flex-col space-y-2 lg:items-end">
                                        @if($attachment->file_path)
                                            <!-- Preview Button -->
                                            <button onclick="previewContent('{{ $attachment->file_type }}', '{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')"
                                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 font-medium text-sm flex items-center justify-center">
                                                <i class="fas fa-eye mr-2"></i> Preview
                                            </button>
                                            <!-- Download Button -->
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                               download 
                                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium text-sm flex items-center justify-center">
                                                <i class="fas fa-download mr-2"></i> Download
                                            </a>
                                        @elseif($attachment->video_url)
                                            <!-- External Video Button -->
                                            <a href="{{ $attachment->video_url }}" 
                                               target="_blank" 
                                               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 font-medium text-sm flex items-center justify-center">
                                                <i class="fab fa-youtube mr-2"></i> Watch Video
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-folder-open text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No content items in this module</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8">
            <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No modules added yet.</p>
            <a href="{{ route('instructor.courses.modules', $course) }}" 
               class="inline-flex items-center mt-2 text-indigo-600 hover:text-indigo-900 font-medium">
                <i class="fas fa-plus mr-2"></i> Add your first module
            </a>
        </div>
        @endif
    </div>
</div>

<!-- Preview Modal -->
<div id="preview-modal" class="hidden fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center p-4 border-b border-gray-200">
            <h3 id="preview-title" class="text-lg font-semibold text-gray-900"></h3>
            <button onclick="closePreviewModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-4 max-h-[80vh] overflow-auto">
            <div id="preview-content"></div>
        </div>
    </div>
</div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Enrollment Stats -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Enrollment Stats</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Students</label>
                            <p class="mt-1 text-2xl font-semibold text-gray-900">{{ $enrollmentStats['total_students'] }}</p>
                        </div>
                        <div>
                            <label class="text-sm font-medium text-gray-500">Total Revenue</label>
                            <p class="mt-1 text-2xl font-semibold text-green-600">${{ number_format($enrollmentStats['total_revenue'], 2) }}</p>
                        </div>
                    </div>
                    <div class="mt-4">
                        <a href="{{ route('instructor.courses.analytics', $course) }}" 
                           class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                            <i class="fas fa-chart-bar mr-2"></i> View Detailed Analytics
                        </a>
                    </div>
                </div>
            </div>

            <!-- Course Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <!-- Submit for Review Button -->
                    @if($course->isDraft())
                    <form action="{{ route('instructor.courses.submit-review', $course) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700"
                                onclick="return confirm('Are you sure you want to submit this course for review?')">
                            <i class="fas fa-paper-plane mr-2"></i> 
                            Submit for Review
                        </button>
                    </form>
                    @endif

                    <!-- Withdraw from Review Button -->
                    @if($course->isPendingReview())
                    <form action="{{ route('instructor.courses.withdraw-review', $course) }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                onclick="return confirm('Are you sure you want to withdraw this course from review?')">
                            <i class="fas fa-undo mr-2"></i> Withdraw from Review
                        </button>
                    </form>
                    @endif

                    <!-- Manage Modules Button -->
                    <a href="{{ route('instructor.courses.modules', $course) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-layer-group mr-2"></i> Manage Modules
                    </a>

                    <!-- Edit Course Button -->
                    <a href="{{ route('instructor.courses.edit', $course) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-edit mr-2"></i> Edit Course
                    </a>

                    <!-- Analytics Button -->
                    <a href="{{ route('instructor.courses.analytics', $course) }}" 
                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                        <i class="fas fa-chart-bar mr-2"></i> View Analytics
                    </a>

                    <!-- Delete Course Button -->
                    <form action="{{ route('instructor.courses.destroy', $course) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 rounded-md shadow-sm text-sm font-medium text-red-700 bg-white hover:bg-red-50"
                                onclick="return confirmDelete()">
                            <i class="fas fa-trash mr-2"></i> Delete Course
                        </button>
                    </form>
                </div>
            </div>

            <!-- Course Image -->
            @if($course->image)
            <div class="bg-white shadow rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">Course Image</h3>
                </div>
                <div class="p-6">
                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-auto rounded-lg">
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this course? This action cannot be undone.');
}

function showMessage(message) {
    alert(message);
    return false;
}

// Smooth toast notifications
document.addEventListener('DOMContentLoaded', function() {
    @if(session('success'))
        showToast('{{ session('success') }}', 'success');
    @endif
    
    @if(session('error'))
        showToast('{{ session('error') }}', 'error');
    @endif
});

function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white z-50 transform transition-transform duration-300 ${
        type === 'success' ? 'bg-green-500' : 
        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
    }`;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 5000);
}

// Module Toggle Functions
function toggleModule(moduleId) {
    const moduleContent = document.getElementById(`module-content-${moduleId}`);
    const moduleIcon = document.getElementById(`module-icon-${moduleId}`);
    
    if (moduleContent.classList.contains('hidden')) {
        moduleContent.classList.remove('hidden');
        moduleIcon.classList.remove('fa-chevron-down');
        moduleIcon.classList.add('fa-chevron-up');
    } else {
        moduleContent.classList.add('hidden');
        moduleIcon.classList.remove('fa-chevron-up');
        moduleIcon.classList.add('fa-chevron-down');
    }
}

function toggleAllModules() {
    const modules = document.querySelectorAll('.module-content');
    const allHidden = Array.from(modules).every(module => module.classList.contains('hidden'));
    
    modules.forEach((module, index) => {
        const moduleId = module.id.split('-')[2];
        const icon = document.getElementById(`module-icon-${moduleId}`);
        
        if (allHidden) {
            module.classList.remove('hidden');
            icon.classList.remove('fa-chevron-down');
            icon.classList.add('fa-chevron-up');
        } else {
            module.classList.add('hidden');
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        }
    });
}

// Content Preview Functions
function previewContent(fileType, fileUrl, title) {
    const modal = document.getElementById('preview-modal');
    const content = document.getElementById('preview-content');
    const titleElement = document.getElementById('preview-title');
    
    titleElement.textContent = title;
    content.innerHTML = '';
    
    if (fileType === 'pdf') {
        content.innerHTML = `
            <iframe src="${fileUrl}#toolbar=0" class="w-full h-[70vh]" frameborder="0"></iframe>
        `;
    } else if (['mp4', 'mov', 'avi', 'mkv', 'webm'].includes(fileType)) {
        content.innerHTML = `
            <video controls class="w-full h-auto max-h-[70vh]" controlsList="nodownload">
                <source src="${fileUrl}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
        `;
    } else if (['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(fileType)) {
        content.innerHTML = `
            <img src="${fileUrl}" alt="${title}" class="w-full h-auto max-h-[70vh] object-contain">
        `;
    } else if (['mp3', 'wav', 'ogg'].includes(fileType)) {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-music text-4xl text-gray-400 mb-4"></i>
                <audio controls class="w-full mt-4" controlsList="nodownload">
                    <source src="${fileUrl}" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        `;
    } else if (['doc', 'docx'].includes(fileType)) {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-file-word text-4xl text-blue-500 mb-4"></i>
                <h4 class="text-lg font-semibold text-gray-700 mb-2">Word Document</h4>
                <p class="text-gray-600 mb-4">Preview not available in browser</p>
                <a href="${fileUrl}" download class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                    <i class="fas fa-download mr-2"></i> Download Document
                </a>
            </div>
        `;
    } else {
        content.innerHTML = `
            <div class="text-center py-8">
                <i class="fas fa-file text-4xl text-gray-400 mb-4"></i>
                <h4 class="text-lg font-semibold text-gray-700 mb-2">File Preview</h4>
                <p class="text-gray-600 mb-4">Preview not available for this file type</p>
                <a href="${fileUrl}" download class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                    <i class="fas fa-download mr-2"></i> Download File
                </a>
            </div>
        `;
    }
    
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreviewModal() {
    const modal = document.getElementById('preview-modal');
    const content = document.getElementById('preview-content');
    
    // Stop any playing videos/audio
    const videos = content.querySelectorAll('video');
    videos.forEach(video => {
        video.pause();
        video.currentTime = 0;
    });
    
    const audios = content.querySelectorAll('audio');
    audios.forEach(audio => {
        audio.pause();
        audio.currentTime = 0;
    });
    
    modal.classList.add('hidden');
    document.body.style.overflow = 'auto';
}

// Close modal when clicking outside or pressing Escape
document.getElementById('preview-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        closePreviewModal();
    }
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closePreviewModal();
    }
});

// Auto-expand first module on page load
document.addEventListener('DOMContentLoaded', function() {
    const firstModule = document.querySelector('.module-content');
    if (firstModule) {
        const firstModuleId = firstModule.id.split('-')[2];
        setTimeout(() => toggleModule(firstModuleId), 300);
    }
});
</script>
@endsection
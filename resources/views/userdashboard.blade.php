@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Dashboard - Secure Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/docx-preview@0.1.7/dist/docx-preview.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf_viewer.min.css">
    <style>
        .progress-ring {
            transform: rotate(-90deg);
        }
        .attachment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .video-thumbnail {
            position: relative;
            cursor: pointer;
        }
        .video-thumbnail::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
        }
        .video-thumbnail:hover::after {
            opacity: 0.9;
        }
        .module-content {
            transition: max-height 0.3s ease-out;
            overflow: hidden;
        }
        .collapsed {
            max-height: 0;
        }
        .expanded {
            max-height: 5000px;
        }
        .purchase-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .purchase-overlay:hover {
            opacity: 1;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        /* Security measures */
        .secure-content {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* Animation for transitions */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Navigation Breadcrumbs -->
        <div class="mb-6">
            <nav class="flex" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2 text-sm">
                    <li>
                        <a href="{{ route('userdashboard') }}" class="text-indigo-600 hover:text-indigo-800">
                            <i class="fas fa-home mr-1"></i> Dashboard
                        </a>
                    </li>
                    @if(isset($selectedCourse))
                    <li class="flex items-center">
                        <span class="text-gray-400 mx-2">/</span>
                        <a href="{{ route('userdashboard', ['course' => $selectedCourse->id]) }}" 
                           class="text-indigo-600 hover:text-indigo-800">
                            {{ Str::limit($selectedCourse->title, 25) }}
                        </a>
                    </li>
                    @endif
                    @if(isset($selectedModule))
                    <li class="flex items-center">
                        <span class="text-gray-400 mx-2">/</span>
                        <span class="text-gray-600">{{ Str::limit($selectedModule->title, 25) }}</span>
                    </li>
                    @endif
                </ol>
            </nav>
        </div>

        <!-- Welcome Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                    <p class="text-gray-600">Continue your learning journey with your purchased courses.</p>
                </div>
                
                <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
                    <div class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-book-open mr-1"></i>
                        <span>{{ $purchasedCourses->count() }} Courses</span>
                    </div>
                    
                    @if(isset($selectedCourse))
                    <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                        <i class="fas fa-play-circle mr-1"></i>
                        <span>Learning: {{ Str::limit($selectedCourse->title, 15) }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="flex flex-col lg:flex-row gap-8">
            <!-- Course List Sidebar -->
            <div class="lg:w-1/4 xl:w-1/5">
                <div class="bg-white rounded-lg shadow-md p-4 mb-6 sticky top-4">
                    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-graduation-cap mr-2 text-indigo-600"></i> My Courses
                    </h2>
                    
                    @if($purchasedCourses->count() > 0)
                    <div class="space-y-2 max-h-96 overflow-y-auto">
                        @foreach($purchasedCourses as $course)
                        <a href="{{ route('userdashboard', ['course' => $course->id]) }}" 
                           class="block p-2 rounded border hover:bg-indigo-50 hover:border-indigo-300 transition text-sm {{ isset($selectedCourse) && $selectedCourse->id == $course->id ? 'bg-indigo-100 border-indigo-300' : 'bg-gray-50' }}">
                            <div class="flex items-center">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" 
                                         class="w-8 h-8 rounded object-cover mr-2">
                                @else
                                    <div class="w-8 h-8 rounded bg-indigo-200 flex items-center justify-center mr-2">
                                        <i class="fas fa-book text-indigo-600 text-xs"></i>
                                    </div>
                                @endif
                                <div class="flex-1 min-w-0">
                                    <h3 class="font-medium text-gray-800 truncate">{{ $course->title }}</h3>
                                    <p class="text-xs text-gray-600">{{ $course->modules->count() }} modules</p>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4">
                        <i class="fas fa-book-open text-2xl text-gray-300 mb-2"></i>
                        <p class="text-gray-500 text-sm">No courses purchased yet.</p>
                        <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-800 mt-1 inline-block text-xs">
                            Browse Courses
                        </a>
                    </div>
                    @endif
                    
                    <!-- Back to Dashboard Button -->
                    @if(isset($selectedCourse))
                    <div class="mt-4 pt-3 border-t border-gray-200">
                        <a href="{{ route('userdashboard') }}" 
                           class="flex items-center text-sm text-gray-600 hover:text-gray-800">
                            <i class="fas fa-arrow-left mr-1"></i> Back to All Courses
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Learning Content Area -->
            <div class="lg:w-3/4 xl:w-4/5">
                @if(isset($selectedCourse) && isset($selectedModule))
                <!-- Module Learning Interface -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
                    <!-- Module Header -->
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-4 text-white">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                            <div>
                                <div class="flex items-center mb-2">
                                    <a href="{{ route('userdashboard', ['course' => $selectedCourse->id]) }}" 
                                       class="bg-indigo-800 hover:bg-indigo-900 p-2 rounded mr-3 transition">
                                        <i class="fas fa-arrow-left"></i>
                                    </a>
                                    <div>
                                        <h2 class="text-2xl font-bold">{{ $selectedCourse->title }}</h2>
                                    </div>
                                     
                                </div>
                                <div class="flex rounded items-center">
                                    <h3 class="font-semibold">
                                        <span class="text-black text-xl">Module:</span> 
                                        <span class="text-indigo-100 mt-1">{{  $selectedModule->title}}</span>
                                    </h3>
                                </div>
                            </div>
                            <div class="mt-2 sm:mt-0">
                                <span class="bg-indigo-800 bg-opacity-50 px-3 py-1 rounded text-sm">
                                    Module {{ $moduleIndex + 1 }} of {{ $selectedCourse->modules->count() }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Module Content -->
                    <div class="p-6">
                        @if($selectedModule->description)
                        <div class="prose max-w-none mb-6 p-4 bg-gray-50 rounded-lg">
                            <p class="text-gray-700">{{ $selectedModule->description }}</p>
                        </div>
                        @endif

                        <!-- Learning Resources Section -->
                        @if($selectedModule->attachments->count() > 0)
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                    <i class="fas fa-paperclip mr-2 text-indigo-600"></i> 
                                    Learning Resources
                                    <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded ml-3">
                                        {{ $selectedModule->attachments->count() }} files
                                    </span>
                                </h3>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($selectedModule->attachments as $attachment)
                                <div class="border rounded-lg p-4 hover:shadow-md transition group secure-content">
                                    <div class="flex items-start">
                                        <!-- File Icon -->
                                        <div class="mr-4 flex-shrink-0">
                                            @if($attachment->file_type === 'pdf')
                                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                                                </div>
                                            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm']))
                                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-video text-purple-600 text-xl"></i>
                                                </div>
                                            @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']))
                                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-image text-green-600 text-xl"></i>
                                                </div>
                                            @elseif(in_array($attachment->file_type, ['doc', 'docx']))
                                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-word text-blue-600 text-xl"></i>
                                                </div>
                                            @else
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file text-gray-600 text-xl"></i>
                                                </div>
                                            @endif
                                        </div>
                                        
                                        <!-- File Info -->
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-800 truncate">{{ $attachment->title }}</h4>
                                            <p class="text-sm text-gray-500 mt-1">
                                                {{ strtoupper($attachment->file_type) }} • 
                                                @if($attachment->file_size)
                                                    {{ number_format($attachment->file_size / 1024, 1) }} KB
                                                @else
                                                    Size unknown
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Button -->
                                    <div class="mt-4">
                                        <button onclick="openResourceViewer('{{ $attachment->id }}', '{{ $attachment->file_type }}', '{{ $attachment->title }}', '{{ asset('storage/' . $attachment->file_path) }}')" 
                                                class="w-full bg-indigo-600 text-white py-2 px-4 rounded-lg hover:bg-indigo-700 transition flex items-center justify-center group-hover:bg-indigo-700">
                                            <i class="fas fa-eye mr-2"></i> View Resource
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="text-center py-8 bg-gray-50 rounded-lg">
                            <i class="fas fa-file-alt text-3xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500">No learning resources available for this module.</p>
                        </div>
                        @endif

                        <!-- Module Navigation -->
                        <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-3 sm:space-y-0">
                            <div>
                                @if($moduleIndex > 0)
                                <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $selectedCourse->modules[$moduleIndex - 1]->id]) }}" 
                                   class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition inline-flex items-center">
                                    <i class="fas fa-arrow-left mr-2"></i> Previous Module
                                </a>
                                @endif
                            </div>
                            
                            <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded">
                                Progress: {{ $moduleIndex + 1 }}/{{ $selectedCourse->modules->count() }}
                            </div>

                            <div>
                                @if($moduleIndex < $selectedCourse->modules->count() - 1)
                                <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $selectedCourse->modules[$moduleIndex + 1]->id]) }}" 
                                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
                                    Next Module <i class="fas fa-arrow-right ml-2"></i>
                                </a>
                                @else
                                <span class="bg-green-600 text-white px-4 py-2 rounded inline-flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i> Course Completed
                                </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Resource Viewer (Udemy-style) -->
                <div id="resourceViewer" class="fixed inset-0 bg-white z-50 hidden flex-col">
                    <!-- Viewer Header -->
                    <div class="bg-gray-900 text-white p-4 flex justify-between items-center shadow-md">
                        <div class="flex items-center flex-1 min-w-0">
                            <button onclick="closeResourceViewer()" class="p-2 text-gray-400 hover:text-white mr-3">
                                <i class="fas fa-arrow-left text-xl"></i>
                            </button>
                            <h3 id="viewerTitle" class="text-lg font-semibold truncate"></h3>
                        </div>
                        <button onclick="closeResourceViewer()" class="p-2 text-gray-400 hover:text-white ml-4">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    
                    <!-- Viewer Content -->
                    <div class="flex-1 bg-gray-100 relative overflow-auto">
                        <div id="viewerContent" class="max-w-4xl mx-auto p-6">
                            <!-- Content will be loaded here -->
                        </div>
                        
                        <!-- Loading Indicator -->
                        <div id="viewerLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center hidden">
                            <div class="text-center">
                                <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-3"></i>
                                <p class="text-gray-600">Loading content securely...</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Viewer Footer -->
                    <div class="bg-gray-800 text-white p-3 text-center text-sm border-t border-gray-700">
                        <i class="fas fa-shield-alt mr-1"></i> Secure content viewer - Protected intellectual property
                    </div>
                </div>

                @elseif(isset($selectedCourse))
                <!-- Course Overview -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <!-- Back Button -->
                    <div class="mb-4">
                        <a href="{{ route('userdashboard') }}" 
                           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm">
                            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                        </a>
                    </div>

                    <div class="flex flex-col md:flex-row md:items-start gap-6 mb-6">
                        @if($selectedCourse->image)
                            <img src="{{ asset('storage/' . $selectedCourse->image) }}" alt="{{ $selectedCourse->title }}" 
                                 class="w-full md:w-48 h-48 rounded-lg object-cover">
                        @else
                            <div class="w-full md:w-48 h-48 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-book text-indigo-600 text-4xl"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $selectedCourse->title }}</h2>
                            <p class="text-gray-600 mb-4">{{ $selectedCourse->description }}</p>
                            
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="text-gray-500">Level</div>
                                    <div class="font-semibold">
                                        @if($selectedCourse->level == 1) Beginner
                                        @elseif($selectedCourse->level == 2) Intermediate
                                        @else Advanced
                                        @endif
                                    </div>
                                </div>
                                <div class="bg-gray-50 p-3 rounded">
                                    <div class="text-gray-500">Modules</div>
                                    <div class="font-semibold">{{ $selectedCourse->modules->count() }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Course Modules -->
                    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-layer-group mr-2 text-indigo-600"></i> Course Curriculum
                    </h3>
                    
                    <div class="space-y-3 mb-6">
                        @foreach($selectedCourse->modules as $index => $module)
                        <div class="border rounded-lg p-4 hover:shadow-md transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center flex-1">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <div class="flex rounded items-center">
                                            <i class="fas fa-caret-down mr-2 text-gray-500" id="icon-{{ $module->id }}"></i>
                                            <h3 class="font-semibold text-2xl">
                                                <span class="text-blue-500">Module {{ $loop->iteration }}:</span> 
                                                <span class="text-black">{{ $module->title }}</span>
                                            </h3>
                                        </div>
                                        @if($module->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="flex items-center space-x-2">
                                    @if($module->is_free)
                                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Free</span>
                                    @endif
                                    <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded">
                                        {{ $module->attachments->count() }} resources
                                    </span>
                                    <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $module->id]) }}" 
                                       class="bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700 transition">
                                        <i class="fas fa-play"></i>
                                        Start Module
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Start Learning Button -->
                    @if($selectedCourse->modules->count() > 0)
                    <div class="text-center">
                        <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $selectedCourse->modules->first()->id]) }}" 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition inline-flex items-center">
                            <i class="fas fa-play-circle mr-2"></i> Start Learning
                        </a>
                    </div>
                    @endif
                </div>

                @else
                <!-- Default Dashboard View -->
                <div class="bg-white rounded-lg shadow-md p-8 text-center">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-graduation-cap text-indigo-600 text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-3">Your Learning Dashboard</h2>
                    <p class="text-gray-600 mb-6 max-w-md mx-auto">
                        Select a course from the sidebar to start your learning journey. All your purchased courses are available here in a secure environment.
                    </p>
                    
                    @if($purchasedCourses->count() > 0)
                    <p class="text-indigo-600 font-semibold mb-4">
                        <i class="fas fa-info-circle mr-2"></i> Click on any course to begin learning
                    </p>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-left max-w-md mx-auto">
                        <div class="flex items-start">
                            <i class="fas fa-shield-alt text-blue-500 text-xl mr-3 mt-1"></i>
                            <div>
                                <h4 class="font-semibold text-blue-800">Secure Learning Environment</h4>
                                <p class="text-blue-600 text-sm">All content is protected and cannot be downloaded.</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <a href="{{ route('courses.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition inline-block mt-4">
                        <i class="fas fa-search mr-2"></i> Browse Available Courses
                    </a>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Enhanced Resource Viewer JavaScript -->
    <script>
    // File type support configuration
    const SUPPORTED_FILE_TYPES = {
        // Video formats
        'mp4': { supported: true, type: 'video', player: 'html5' },
        'mov': { supported: true, type: 'video', player: 'html5' },
        'avi': { supported: true, type: 'video', player: 'html5' },
        'mkv': { supported: true, type: 'video', player: 'html5' },
        'webm': { supported: true, type: 'video', player: 'html5' },
        'wmv': { supported: true, type: 'video', player: 'html5' },
        
        // Document formats
        'pdf': { supported: true, type: 'document', viewer: 'pdfjs' },
        'doc': { supported: true, type: 'document', viewer: 'docx' },
        'docx': { supported: true, type: 'document', viewer: 'docx' },
        'txt': { supported: true, type: 'document', viewer: 'text' },
        
        // Image formats
        'jpg': { supported: true, type: 'image' },
        'jpeg': { supported: true, type: 'image' },
        'png': { supported: true, type: 'image' },
        'gif': { supported: true, type: 'image' },
        'bmp': { supported: true, type: 'image' },
        'webp': { supported: true, type: 'image' },
        'svg': { supported: true, type: 'image' },
        
        // Audio formats
        'mp3': { supported: true, type: 'audio', player: 'html5' },
        'wav': { supported: true, type: 'audio', player: 'html5' },
        'ogg': { supported: true, type: 'audio', player: 'html5' },
        
        // Archive formats (show info but don't preview)
        'zip': { supported: false, type: 'archive' },
        'rar': { supported: false, type: 'archive' },
        '7z': { supported: false, type: 'archive' }
    };

    function openResourceViewer(attachmentId, fileType, title, fileUrl) {
        const viewer = document.getElementById('resourceViewer');
        const viewerContent = document.getElementById('viewerContent');
        const viewerTitle = document.getElementById('viewerTitle');
        const viewerLoading = document.getElementById('viewerLoading');
        
        // Show loading
        viewerLoading.classList.remove('hidden');
        viewerContent.innerHTML = '';
        
        // Set title
        viewerTitle.textContent = title;
        
        // Show viewer
        viewer.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        // Check if file type is supported
        const fileConfig = SUPPORTED_FILE_TYPES[fileType.toLowerCase()];
        
        if (!fileConfig || !fileConfig.supported) {
            showUnsupportedFileType(fileType, title);
            viewerLoading.classList.add('hidden');
            return;
        }
        
        // Load appropriate viewer based on file type
        loadFileContent(attachmentId, fileType, fileConfig, title, fileUrl);
    }

    async function loadFileContent(attachmentId, fileType, fileConfig, title, fileUrl) {
        const viewerContent = document.getElementById('viewerContent');
        const viewerLoading = document.getElementById('viewerLoading');
        
        try {
            switch (fileConfig.type) {
                case 'video':
                    viewerContent.innerHTML = createVideoPlayer(fileUrl, fileType, title);
                    break;
                    
                case 'audio':
                    viewerContent.innerHTML = createAudioPlayer(fileUrl, fileType, title);
                    break;
                    
                case 'image':
                    viewerContent.innerHTML = createImageViewer(fileUrl, title);
                    break;
                    
                case 'document':
                    if (fileConfig.viewer === 'pdfjs' && fileType.toLowerCase() === 'pdf') {
                        viewerContent.innerHTML = createPDFViewer();
                        await loadPDFDocument(fileUrl, viewerContent);
                    } else if (fileConfig.viewer === 'docx' && (fileType.toLowerCase() === 'doc' || fileType.toLowerCase() === 'docx')) {
                        await loadDocxDocument(fileUrl, viewerContent);
                    } else if (fileConfig.viewer === 'text') {
                        await loadTextFile(fileUrl, viewerContent);
                    } else {
                        // Use browser's native handling for other documents
                        viewerContent.innerHTML = createDocumentViewer(fileUrl, title, fileType);
                    }
                    break;
                    
                default:
                    showUnsupportedFileType(fileType, title);
            }
        } catch (error) {
            console.error('Error loading file:', error);
            showErrorViewer('Failed to load the file. Please try again later.');
        } finally {
            viewerLoading.classList.add('hidden');
        }
    }

    function createVideoPlayer(url, fileType, title) {
        return `
            <div class="bg-black rounded-lg overflow-hidden shadow-lg">
                <video controls controlsList="nodownload" class="w-full" style="max-height: 70vh;">
                    <source src="${url}" type="${getVideoMimeType(fileType)}">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
                <h4 class="font-semibold text-gray-800 mb-2">${title}</h4>
                <p class="text-sm text-gray-600">Video content - use player controls to navigate</p>
            </div>
        `;
    }

    function createAudioPlayer(url, fileType, title) {
        return `
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <div class="text-center mb-4">
                    <i class="fas fa-music text-4xl text-indigo-600 mb-3"></i>
                    <h4 class="font-semibold text-gray-800">${title}</h4>
                </div>
                <audio controls controlsList="nodownload" class="w-full">
                    <source src="${url}" type="${getAudioMimeType(fileType)}">
                    Your browser does not support the audio element.
                </audio>
            </div>
        `;
    }

    function createImageViewer(url, title) {
        return `
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="text-center">
                    <img src="${url}" alt="${title}" class="max-w-full max-h-96 mx-auto object-contain rounded-lg shadow-md" 
                         oncontextmenu="return false;" loading="lazy">
                </div>
                <div class="mt-4 text-center">
                    <h4 class="font-semibold text-gray-800">${title}</h4>
                    <p class="text-sm text-gray-600">Image preview</p>
                </div>
            </div>
        `;
    }

    function createPDFViewer() {
        return `
            <div class="bg-white rounded-lg shadow-sm">
                <div class="bg-gray-50 p-4 border-b">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <button id="prevPage" class="bg-gray-200 text-gray-700 px-3 py-1 rounded mr-2">
                                <i class="fas fa-chevron-left"></i>
                            </button>
                            <span id="pageInfo" class="text-sm text-gray-600">Page: <span id="currentPage">1</span> / <span id="totalPages">0</span></span>
                            <button id="nextPage" class="bg-gray-200 text-gray-700 px-3 py-1 rounded ml-2">
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>
                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-2">Zoom:</span>
                            <button id="zoomOut" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-l">-</button>
                            <span id="zoomLevel" class="bg-gray-100 px-3 py-1 text-sm">100%</span>
                            <button id="zoomIn" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-r">+</button>
                        </div>
                    </div>
                </div>
                <div class="p-4">
                    <div id="pdfViewer" class="border rounded overflow-auto" style="height: 70vh;"></div>
                </div>
            </div>
        `;
    }

    function createDocumentViewer(url, title, fileType) {
        return `
            <div class="bg-white rounded-lg shadow-sm p-6 text-center">
                <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-file text-blue-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">${title}</h3>
                <p class="text-gray-600 mb-4">This document (${fileType.toUpperCase()}) can be viewed in your browser.</p>
                <a href="${url}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                    Open Document
                </a>
                <p class="text-sm text-gray-500 mt-3">The document will open in a new tab</p>
            </div>
        `;
    }

    async function loadPDFDocument(url, container) {
        try {
            const pdfViewer = container.querySelector('#pdfViewer');
            const pdfDocument = await pdfjsLib.getDocument(url).promise;
            const totalPages = pdfDocument.numPages;
            
            container.querySelector('#totalPages').textContent = totalPages;
            
            let currentPage = 1;
            let currentScale = 1.0;
            
            const renderPage = async (pageNumber) => {
                const page = await pdfDocument.getPage(pageNumber);
                const viewport = page.getViewport({ scale: currentScale });
                
                const canvas = document.createElement('canvas');
                const context = canvas.getContext('2d');
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                await page.render(renderContext).promise;
                
                pdfViewer.innerHTML = '';
                pdfViewer.appendChild(canvas);
                container.querySelector('#currentPage').textContent = pageNumber;
                container.querySelector('#zoomLevel').textContent = Math.round(currentScale * 100) + '%';
            };
            
            // Render first page
            await renderPage(currentPage);
            
            // Add event listeners
            container.querySelector('#prevPage').addEventListener('click', async () => {
                if (currentPage > 1) {
                    currentPage--;
                    await renderPage(currentPage);
                }
            });
            
            container.querySelector('#nextPage').addEventListener('click', async () => {
                if (currentPage < totalPages) {
                    currentPage++;
                    await renderPage(currentPage);
                }
            });
            
            container.querySelector('#zoomIn').addEventListener('click', async () => {
                currentScale += 0.25;
                await renderPage(currentPage);
            });
            
            container.querySelector('#zoomOut').addEventListener('click', async () => {
                if (currentScale > 0.5) {
                    currentScale -= 0.25;
                    await renderPage(currentPage);
                }
            });
            
        } catch (error) {
            console.error('Error loading PDF:', error);
            showErrorViewer('Failed to load the PDF document.');
        }
    }

    async function loadDocxDocument(url, container) {
        try {
            const response = await fetch(url);
            const blob = await response.blob();
            
            container.innerHTML = `
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="bg-gray-50 p-3 rounded mb-4">
                        <h4 class="font-semibold text-gray-800">Document Preview</h4>
                    </div>
                    <div id="docx-container" class="bg-white p-4 border rounded overflow-auto" style="max-height: 70vh;"></div>
                </div>
            `;
            
            // Render DOCX using docx-preview library
            docx.renderAsync(blob, container.querySelector('#docx-container'));
        } catch (error) {
            console.error('Error loading DOCX:', error);
            showErrorViewer('Failed to load the document.');
        }
    }

    async function loadTextFile(url, container) {
        try {
            const response = await fetch(url);
            const text = await response.text();
            
            container.innerHTML = `
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="bg-gray-50 p-3 rounded mb-4">
                        <h4 class="font-semibold text-gray-800">Text Content</h4>
                    </div>
                    <pre class="whitespace-pre-wrap font-mono text-sm bg-gray-50 p-4 rounded overflow-auto" style="max-height: 70vh;">${escapeHtml(text)}</pre>
                </div>
            `;
        } catch (error) {
            console.error('Error loading text file:', error);
            showErrorViewer('Failed to load the text file.');
        }
    }

    function showUnsupportedFileType(fileType, title) {
        const viewerContent = document.getElementById('viewerContent');
        
        viewerContent.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm p-8 text-center max-w-md mx-auto">
                <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">File Type Not Supported</h3>
                <p class="text-gray-600 mb-4">${fileType.toUpperCase()} files cannot be previewed in the browser.</p>
                <div class="bg-gray-50 rounded-lg p-4 text-left">
                    <p class="text-sm text-gray-600 mb-2">File: <strong>${title}</strong></p>
                    <p class="text-sm text-gray-600 mb-2">Type: <strong>${fileType.toUpperCase()}</strong></p>
                    <p class="text-sm text-gray-600">Please contact support if you need assistance with this file type.</p>
                </div>
            </div>
        `;
    }

    function showErrorViewer(message) {
        const viewerContent = document.getElementById('viewerContent');
        
        viewerContent.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Content</h3>
                <p class="text-gray-600 mb-4">${message}</p>
                <button onclick="closeResourceViewer()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                    Close Viewer
                </button>
            </div>
        `;
    }

    function getVideoMimeType(extension) {
        const types = {
            'mp4': 'video/mp4',
            'mov': 'video/quicktime',
            'avi': 'video/x-msvideo',
            'mkv': 'video/x-matroska',
            'webm': 'video/webm',
            'wmv': 'video/x-ms-wmv'
        };
        return types[extension.toLowerCase()] || 'video/mp4';
    }

    function getAudioMimeType(extension) {
        const types = {
            'mp3': 'audio/mpeg',
            'wav': 'audio/wav',
            'ogg': 'audio/ogg',
            'm4a': 'audio/mp4'
        };
        return types[extension.toLowerCase()] || 'audio/mpeg';
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function closeResourceViewer() {
        const viewer = document.getElementById('resourceViewer');
        viewer.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Clean up
        const videos = viewer.querySelectorAll('video');
        videos.forEach(video => {
            video.pause();
            video.src = '';
        });
        
        const audios = viewer.querySelectorAll('audio');
        audios.forEach(audio => {
            audio.pause();
            audio.src = '';
        });
    }

    // Security measures
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('#resourceViewer') || e.target.closest('.secure-content')) {
            e.preventDefault();
            showSecurityToast('Downloading content is disabled to protect intellectual property.');
        }
    });

    document.addEventListener('dragstart', function(e) {
        if (e.target.closest('#resourceViewer') || e.target.closest('.secure-content')) {
            e.preventDefault();
        }
    });

    function showSecurityToast(message) {
        // Remove existing toast if any
        const existingToast = document.getElementById('securityToast');
        if (existingToast) existingToast.remove();
        
        const toast = document.createElement('div');
        toast.id = 'securityToast';
        toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-60 transform transition-transform duration-300 translate-x-full';
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-shield-alt mr-2"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Animate in
        setTimeout(() => {
            toast.classList.remove('translate-x-full');
            toast.classList.add('translate-x-0');
        }, 10);
        
        // Remove after 3 seconds
        setTimeout(() => {
            toast.classList.remove('translate-x-0');
            toast.classList.add('translate-x-full');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.parentNode.removeChild(toast);
                }
            }, 300);
        }, 3000);
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        const viewer = document.getElementById('resourceViewer');
        if (!viewer.classList.contains('hidden')) {
            if (e.key === 'Escape') {
                closeResourceViewer();
            }
        }
    });

    // Initialize modules to be collapsed by default
    document.addEventListener('DOMContentLoaded', function() {
        // Add any initialization code here if needed
    });
    </script>
</body>
@endsection
@extends('layouts.admin')

@section('content')
<!-- Enhanced Notifications -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if(session('success'))
    <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6 rounded-r-lg animate-slide-in">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-check-circle text-green-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-green-700 font-medium">{!! session('success') !!}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-green-50 rounded-md p-1.5 text-green-500 hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-green-50 focus:ring-green-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-4 w-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg animate-slide-in">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-circle text-red-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-red-700 font-medium">{!! session('error') !!}</p>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-4 w-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($errors->any())
    <div class="bg-yellow-50 border-l-4 border-yellow-500 p-4 mb-6 rounded-r-lg animate-slide-in">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400 text-xl"></i>
            </div>
            <div class="ml-3">
                <p class="text-yellow-700 font-medium">Please fix the following errors:</p>
                <ul class="mt-2 text-yellow-700 list-disc list-inside text-sm">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            <div class="ml-auto pl-3">
                <div class="-mx-1.5 -my-1.5">
                    <button type="button" class="inline-flex bg-yellow-50 rounded-md p-1.5 text-yellow-500 hover:bg-yellow-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-yellow-50 focus:ring-yellow-600" onclick="this.parentElement.parentElement.parentElement.style.display='none'">
                        <span class="sr-only">Dismiss</span>
                        <i class="fas fa-times h-4 w-4"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<div class="max-w-7xl mx-auto">
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Manage Modules: {{ $course->title }}</h1>
            <p class="text-gray-600">Add and organize course modules with secure content delivery</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('instructor.courses.show', $course) }}" class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
                Back to Course
            </a>
            @if($course->status === 'draft' && $course->canBeSubmittedForReview())
            <form action="{{ route('instructor.courses.submit-review', $course) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Submit for Review
                </button>
            </form>
            @endif
            @if($course->status === 'rejected')
            <form action="{{ route('instructor.courses.submit-review', $course) }}" method="POST">
                @csrf
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition">
                    Resubmit for Review
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-4 gap-6">
        <!-- Add Module Form -->
        <div class="xl:col-span-1">
            <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-plus-circle text-indigo-600 mr-2"></i> Add New Module
                </h2>
                
                <form action="{{ route('instructor.courses.modules.store', $course) }}" method="POST">
                    @csrf
                    
                    <div class="space-y-4">
                        <div>
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Module Title *</label>
                            <input type="text" name="title" id="title" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   placeholder="Introduction to Course">
                        </div>
                        
                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                      placeholder="Module overview and learning objectives"></textarea>
                        </div>
                        
                        <div>
                            <label for="order" class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                            <input type="number" name="order" id="order" min="0" required
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                   value="{{ ($course->modules->max('order') ?? -1) + 1 }}">
                        </div>
                        
                        <div class="flex items-center">
                            <input type="checkbox" name="is_free" id="is_free" value="1"
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <label for="is_free" class="ml-2 block text-sm text-gray-900">
                                Free Preview Module
                                <span class="text-xs text-gray-500 block">Students can access without purchase</span>
                            </label>
                        </div>
                    </div>
                    
                    <button type="submit" class="w-full mt-6 bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700 transition duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i> Add Module
                    </button>
                </form>
            </div>

            <!-- Course Stats -->
            <div class="mt-6 bg-white shadow-lg rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-bar text-indigo-600 mr-2"></i> Course Stats
                </h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-layer-group text-gray-400 mr-2"></i> Total Modules
                        </span>
                        <span class="font-semibold text-gray-800">{{ $course->modules->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-unlock text-green-400 mr-2"></i> Free Modules
                        </span>
                        <span class="font-semibold text-green-600">{{ $course->modules->where('is_free', true)->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-paperclip text-blue-400 mr-2"></i> Total Attachments
                        </span>
                        <span class="font-semibold text-gray-800">{{ $course->modules->sum(fn($module) => $module->attachments->count()) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 hover:bg-gray-50 rounded">
                        <span class="text-gray-600 flex items-center">
                            <i class="fas fa-shield-alt text-purple-400 mr-2"></i> Secure Videos
                        </span>
                        <span class="font-semibold text-purple-600">
                            {{ $course->modules->sum(fn($module) => $module->attachments->where('file_type', 'secure_video')->count()) }}
                        </span>
                    </div>
                </div>

                <!-- Storage Usage -->
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Storage Usage</span>
                        <span class="text-sm text-gray-600">
                            {{ number_format($course->modules->sum(fn($module) => $module->attachments->sum('file_size')) / 1024 / 1024, 1) }} MB
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        @php
                            $totalSize = $course->modules->sum(fn($module) => $module->attachments->sum('file_size'));
                            $usagePercent = min(($totalSize / (500 * 1024 * 1024)) * 100, 100); // 500MB limit
                        @endphp
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $usagePercent }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1 text-center">500 MB storage limit</p>
                </div>
            </div>
        </div>
        
        <!-- Modules List -->
        <div class="xl:col-span-3">
            <div class="bg-white shadow-lg rounded-lg p-6 border border-gray-200">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-lg font-semibold text-gray-800 flex items-center">
                        <i class="fas fa-list-ol text-indigo-600 mr-2"></i> Course Modules
                    </h2>
                    <span class="text-sm text-gray-500">{{ $course->modules->count() }} modules</span>
                </div>
                
                @if($course->modules->count() > 0)
                <div class="space-y-6">
                    @foreach($course->modules->sortBy('order') as $module)
                    <div class="border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-shadow duration-200">
                        <!-- Module Header -->
                        <div class="bg-gradient-to-r from-gray-50 to-white px-6 py-4 border-b border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-lg font-semibold text-gray-800">{{ $module->title }}</h3>
                                        @if($module->is_free)
                                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full font-medium flex items-center">
                                                <i class="fas fa-unlock mr-1"></i> Free Preview
                                            </span>
                                        @endif
                                        <span class="bg-gray-100 text-gray-600 text-xs px-2 py-1 rounded-full">
                                            Order: {{ $module->order }}
                                        </span>
                                    </div>
                                    @if($module->description)
                                        <p class="text-gray-600 text-sm">{{ $module->description }}</p>
                                    @endif
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button onclick="toggleModuleEdit({{ $module->id }})" 
                                            class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition"
                                            title="Edit Module">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form action="{{ route('instructor.courses.modules.destroy', [$course, $module]) }}" 
                                          method="POST" onsubmit="return confirm('Are you sure you want to delete this module and all its content?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition"
                                                title="Delete Module">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Edit Module Form -->
                        <div id="edit-module-{{ $module->id }}" class="hidden bg-gray-50 p-6 border-b border-gray-200">
                            <form action="{{ route('instructor.courses.modules.update', [$course, $module]) }}" method="POST">
                                @csrf
                                @method('PUT')
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                        <input type="text" name="title" value="{{ $module->title }}" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                        <input type="number" name="order" value="{{ $module->order }}" min="0" required
                                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                    <textarea name="description" rows="3"
                                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">{{ $module->description }}</textarea>
                                </div>
                                
                                <div class="mb-4">
                                    <div class="flex items-center">
                                        <input type="checkbox" name="is_free" id="is_free_{{ $module->id }}" value="1"
                                               {{ $module->is_free ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="is_free_{{ $module->id }}" class="ml-2 block text-sm text-gray-900">
                                            Free Preview Module
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="flex justify-end space-x-3">
                                    <button type="button" onclick="toggleModuleEdit({{ $module->id }})" 
                                            class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                                        Cancel
                                    </button>
                                    <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                        Update Module
                                    </button>
                                </div>
                            </form>
                        </div>
                        
                        <!-- Attachments Section -->
                        <div class="p-6">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="font-semibold text-gray-700 flex items-center">
                                    <i class="fas fa-paperclip text-gray-400 mr-2"></i> 
                                    Learning Materials
                                    <span class="ml-2 text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded-full">
                                        {{ $module->attachments->count() }} items
                                    </span>
                                </h4>
                                <button onclick="toggleAttachmentForm({{ $module->id }})" 
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition flex items-center text-sm font-medium">
                                    <i class="fas fa-plus mr-2"></i> Add Content
                                </button>
                            </div>
                            
                            <!-- Add Attachment Form -->
                            <div id="attachment-form-{{ $module->id }}" class="hidden mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                                <h5 class="font-medium text-gray-800 mb-3 flex items-center">
                                    <i class="fas fa-upload mr-2"></i> Upload New Content
                                </h5>
                                <form action="{{ route('instructor.courses.attachments.store', [$course, $module]) }}" 
                                    method="POST" enctype="multipart/form-data" id="upload-form-{{ $module->id }}">
                                    @csrf
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                            <input type="text" name="title" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                placeholder="e.g., Introduction Video">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                            <input type="number" name="order" min="0" required
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                value="{{ ($module->attachments->max('order') ?? -1) + 1 }}">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                        <textarea name="description" rows="2"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                placeholder="Brief description of this content"></textarea>
                                    </div>
                                    
                                    <!-- Content Type Selection -->
                                    <div class="mb-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Content Type</label>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3" id="content-type-selector-{{ $module->id }}">
                                            <div class="relative">
                                                <input type="radio" name="content_type" id="file_upload_{{ $module->id }}" value="file" class="hidden peer" checked>
                                                <label for="file_upload_{{ $module->id }}" class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-colors">
                                                    <i class="fas fa-file-upload text-2xl text-gray-400 mb-2 peer-checked:text-indigo-600"></i>
                                                    <span class="text-sm font-medium">File Upload</span>
                                                    <span class="text-xs text-gray-500 text-center mt-1">PDF, Video, Documents</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="radio" name="content_type" id="secure_video_{{ $module->id }}" value="secure_video" class="hidden peer">
                                                <label for="secure_video_{{ $module->id }}" class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-colors">
                                                    <i class="fas fa-shield-alt text-2xl text-gray-400 mb-2 peer-checked:text-indigo-600"></i>
                                                    <span class="text-sm font-medium">Secure Video</span>
                                                    <span class="text-xs text-gray-500 text-center mt-1">DRM Protected</span>
                                                </label>
                                            </div>
                                            <div class="relative">
                                                <input type="radio" name="content_type" id="external_video_{{ $module->id }}" value="external_video" class="hidden peer">
                                                <label for="external_video_{{ $module->id }}" class="flex flex-col items-center p-3 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-indigo-500 peer-checked:bg-indigo-50 transition-colors">
                                                    <i class="fab fa-youtube text-2xl text-gray-400 mb-2 peer-checked:text-indigo-600"></i>
                                                    <span class="text-sm font-medium">External Video</span>
                                                    <span class="text-xs text-gray-500 text-center mt-1">YouTube/Vimeo</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- File Upload Section -->
                                    <div id="file_upload_section_{{ $module->id }}" class="content-section">
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload File</label>
                                            <input type="file" name="file_upload" id="file_input_{{ $module->id }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.mp4,.mov,.avi,.mkv,.jpg,.jpeg,.png,.gif,.zip,.mp3,.wav">
                                            <p class="text-xs text-gray-500 mt-1">
                                                Supported: PDF, Documents, Videos, Images, Audio (Max: 500MB)
                                            </p>
                                        </div>
                                        <div class="flex items-center mb-3">
                                            <input type="checkbox" name="allow_download" id="allow_download_{{ $module->id }}" value="1" checked
                                                class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="allow_download_{{ $module->id }}" class="ml-2 block text-sm text-gray-900">
                                                Allow students to download this file
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Secure Video Section -->
                                    <div id="secure_video_section_{{ $module->id }}" class="content-section hidden">
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Upload Video File *</label>
                                            <input type="file" name="secure_video_file" id="secure_video_input_{{ $module->id }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                accept=".mp4,.mov,.avi,.mkv,.webm">
                                            <p class="text-xs text-gray-500 mt-1">
                                                Video will be encrypted and protected with DRM (Max: 500MB)
                                            </p>
                                        </div>
                                        <input type="hidden" name="is_secure" value="1">
                                        <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                                            <div class="flex items-start">
                                                <i class="fas fa-shield-alt text-blue-500 mt-0.5 mr-3"></i>
                                                <div>
                                                    <h6 class="text-sm font-medium text-blue-800">Secure Video Protection</h6>
                                                    <p class="text-xs text-blue-600 mt-1">
                                                        This video will be encrypted and protected against downloading. 
                                                        Students can stream it securely but cannot download the file.
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- External Video Section -->
                                    <div id="external_video_section_{{ $module->id }}" class="content-section hidden">
                                        <div class="mb-3">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL *</label>
                                            <input type="url" name="video_url"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                placeholder="https://youtube.com/watch?v=... or https://vimeo.com/...">
                                            <p class="text-xs text-gray-500 mt-1">Supports YouTube and Vimeo links</p>
                                        </div>
                                    </div>
                                    
                                    <!-- Upload Progress -->
                                    <div id="upload-progress-{{ $module->id }}" class="hidden mb-3">
                                        <div class="flex justify-between text-sm text-gray-600 mb-1">
                                            <span>Uploading...</span>
                                            <span id="progress-percentage-{{ $module->id }}">0%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2">
                                            <div id="progress-bar-{{ $module->id }}" class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="flex justify-end space-x-3 pt-3 border-t border-gray-200">
                                        <button type="button" onclick="toggleAttachmentForm({{ $module->id }})" 
                                                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                                            Cancel
                                        </button>
                                        <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition flex items-center">
                                            <i class="fas fa-upload mr-2"></i> Upload Content
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Attachments List -->
                            @if($module->attachments->count() > 0)
                            <div class="space-y-3">
                                @foreach($module->attachments->sortBy('order') as $attachment)
                                <div class="flex items-center justify-between p-4 bg-white border border-gray-200 rounded-lg hover:shadow-sm transition-shadow">
                                    <div class="flex items-center space-x-4 flex-1">
                                        <!-- File Icon -->
                                        <div class="flex-shrink-0">
                                            @if($attachment->file_type === 'secure_video')
                                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-shield-alt text-purple-600 text-xl"></i>
                                                </div>
                                            @elseif($attachment->file_type === 'external_video')
                                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                                    <i class="fab fa-youtube text-red-600 text-xl"></i>
                                                </div>
                                            @elseif($attachment->file_type === 'pdf')
                                                <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-file-pdf text-red-600 text-xl"></i>
                                                </div>
                                            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-video text-purple-600 text-xl"></i>
                                                </div>
                                            @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif']))
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
                                            <div class="flex items-center space-x-2 mb-1">
                                                <h5 class="font-medium text-gray-800 truncate">{{ $attachment->title }}</h5>
                                                @if($attachment->file_type === 'secure_video')
                                                    <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded-full flex items-center">
                                                        <i class="fas fa-shield-alt mr-1"></i> Secure
                                                    </span>
                                                @endif
                                                @if(!$attachment->allow_download && $attachment->file_type !== 'secure_video')
                                                    <span class="bg-orange-100 text-orange-800 text-xs px-2 py-1 rounded-full flex items-center">
                                                        <i class="fas fa-eye mr-1"></i> Preview Only
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            @if($attachment->description)
                                                <p class="text-sm text-gray-600 mb-1">{{ Str::limit($attachment->description, 80) }}</p>
                                            @endif
                                            
                                            <div class="flex items-center space-x-3 text-xs text-gray-500">
                                                <span class="flex items-center">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    {{ $attachment->getDisplayFileType() }}
                                                </span>
                                                @if($attachment->file_size)
                                                    <span class="flex items-center">
                                                        <i class="fas fa-weight-hanging mr-1"></i>
                                                        {{ number_format($attachment->file_size / 1024 / 1024, 2) }} MB
                                                    </span>
                                                @endif
                                                <span class="flex items-center">
                                                    <i class="fas fa-sort-numeric-down mr-1"></i>
                                                    Order: {{ $attachment->order }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2">
                                        <!-- Preview/View -->
                                        @if($attachment->file_type === 'external_video')
                                            <a href="{{ $attachment->video_url }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition"
                                               title="View Video">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        @elseif($attachment->file_type === 'secure_video')
                                            <span class="text-green-600 p-2" title="Secure Video - Students will use secure player">
                                                <i class="fas fa-play-circle"></i>
                                            </span>
                                        @else
                                            <a href="{{ route('attachment.serve', $attachment) }}" 
                                               target="_blank" 
                                               class="text-blue-600 hover:text-blue-900 p-2 rounded-full hover:bg-blue-50 transition"
                                               title="Preview File">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        @endif
                                        
                                        <!-- Download (if allowed) -->
                                        @if($attachment->allow_download && $attachment->file_type !== 'secure_video' && $attachment->file_type !== 'external_video')
                                            <a href="{{ route('attachment.download', $attachment) }}" 
                                               class="text-green-600 hover:text-green-900 p-2 rounded-full hover:bg-green-50 transition"
                                               title="Download File">
                                                <i class="fas fa-download"></i>
                                            </a>
                                        @endif
                                        
                                        <!-- Edit -->
                                        <button onclick="toggleAttachmentEdit({{ $attachment->id }})" 
                                                class="text-yellow-600 hover:text-yellow-900 p-2 rounded-full hover:bg-yellow-50 transition"
                                                title="Edit Attachment">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <!-- Delete -->
                                        <form action="{{ route('instructor.courses.attachments.destroy', [$course, $module, $attachment]) }}" 
                                              method="POST" 
                                              onsubmit="return confirm('Are you sure you want to delete this attachment?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 p-2 rounded-full hover:bg-red-50 transition"
                                                    title="Delete Attachment">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Edit Attachment Form -->
                                <div id="edit-attachment-{{ $attachment->id }}" class="hidden p-4 bg-gray-50 rounded-lg border border-gray-200 mt-2">
                                    <h6 class="font-medium text-gray-800 mb-3 flex items-center">
                                        <i class="fas fa-edit mr-2"></i> Edit Attachment
                                    </h6>
                                    <form action="{{ route('instructor.courses.attachments.update', [$course, $module, $attachment]) }}" 
                                        method="POST" enctype="multipart/form-data">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Title *</label>
                                                <input type="text" name="title" value="{{ $attachment->title }}" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Order *</label>
                                                <input type="number" name="order" value="{{ $attachment->order }}" min="0" required
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">
                                            </div>
                                        </div>
                                        
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                            <textarea name="description" rows="2"
                                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500">{{ $attachment->description }}</textarea>
                                        </div>
                                        
                                        @if($attachment->file_type !== 'external_video' && $attachment->file_type !== 'secure_video')
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Replace File</label>
                                            <input type="file" name="file"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.mp4,.mov,.avi,.mkv,.jpg,.jpeg,.png,.gif,.zip,.mp3,.wav">
                                            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current file</p>
                                        </div>
                                        
                                        <div class="flex items-center mb-4">
                                            <input type="checkbox" name="allow_download" id="allow_download_edit_{{ $attachment->id }}" value="1"
                                                   {{ $attachment->allow_download ? 'checked' : '' }}
                                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                            <label for="allow_download_edit_{{ $attachment->id }}" class="ml-2 block text-sm text-gray-900">
                                                Allow students to download this file
                                            </label>
                                        </div>
                                        @endif
                                        
                                        @if($attachment->file_type === 'external_video')
                                        <div class="mb-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Video URL</label>
                                            <input type="url" name="video_url" value="{{ $attachment->video_url }}"
                                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-indigo-500"
                                                placeholder="https://youtube.com/watch?v=...">
                                        </div>
                                        @endif
                                        
                                        <div class="flex justify-end space-x-3 pt-3 border-t border-gray-200">
                                            <button type="button" onclick="toggleAttachmentEdit({{ $attachment->id }})" 
                                                    class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 transition">
                                                Cancel
                                            </button>
                                            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                                                Update Attachment
                                            </button>
                                        </div>
                                    </form>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="text-center py-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                <i class="fas fa-paperclip text-4xl text-gray-300 mb-3"></i>
                                <p class="text-gray-500 font-medium">No learning materials yet</p>
                                <p class="text-sm text-gray-400 mt-1">Add videos, documents, or external content to get started</p>
                                <button onclick="toggleAttachmentForm({{ $module->id }})" 
                                        class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition text-sm">
                                    <i class="fas fa-plus mr-1"></i> Add First Content
                                </button>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                    <i class="fas fa-layer-group text-5xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-500 mb-2">No modules created yet</h3>
                    <p class="text-gray-400 mb-6">Start by creating your first course module</p>
                    <div class="bg-white inline-block p-2 rounded-lg shadow-sm">
                        <p class="text-sm text-gray-500">Use the form on the left to add your first module</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Toggle functions
function toggleModuleEdit(moduleId) {
    const editForm = document.getElementById('edit-module-' + moduleId);
    editForm.classList.toggle('hidden');
}

function toggleAttachmentEdit(attachmentId) {
    const editForm = document.getElementById('edit-attachment-' + attachmentId);
    editForm.classList.toggle('hidden');
}

function toggleAttachmentForm(moduleId) {
    const attachmentForm = document.getElementById('attachment-form-' + moduleId);
    attachmentForm.classList.toggle('hidden');
    
    // Reset form when showing
    if (!attachmentForm.classList.contains('hidden')) {
        resetAttachmentForm(moduleId);
    }
}

// Content type switching - COMPLETELY FIXED VERSION
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing content type selectors...');
    
    // Initialize all content type selectors on page load
    document.querySelectorAll('[id^="content-type-selector-"]').forEach(selector => {
        const moduleId = selector.id.split('-').pop();
        console.log('Setting up selector for module:', moduleId);
        setupContentTypeSelector(moduleId);
    });

    // Add event listeners to all radio buttons
    document.querySelectorAll('input[name="content_type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const moduleId = this.id.split('_').pop();
            console.log('Content type changed to:', this.value, 'for module:', moduleId);
            handleContentTypeChange(moduleId, this.value);
        });
    });

    // Also initialize on window load for safety
    window.addEventListener('load', function() {
        document.querySelectorAll('[id^="content-type-selector-"]').forEach(selector => {
            const moduleId = selector.id.split('-').pop();
            setupContentTypeSelector(moduleId);
        });
    });
});

function setupContentTypeSelector(moduleId) {
    console.log('Setting up content type selector for module:', moduleId);
    
    // Ensure file upload is selected by default
    const fileUploadRadio = document.getElementById('file_upload_' + moduleId);
    if (fileUploadRadio && !fileUploadRadio.checked) {
        fileUploadRadio.checked = true;
    }
    
    // Get current selected value or default to 'file'
    const selectedValue = document.querySelector(`input[name="content_type"][id$="_${moduleId}"]:checked`)?.value || 'file';
    console.log('Current selected value:', selectedValue);
    
    // Force update the UI
    handleContentTypeChange(moduleId, selectedValue);
}

function handleContentTypeChange(moduleId, contentType) {
    console.log('Handling content type change:', contentType, 'for module:', moduleId);
    
    const sections = {
        'file': document.getElementById('file_upload_section_' + moduleId),
        'secure_video': document.getElementById('secure_video_section_' + moduleId),
        'external_video': document.getElementById('external_video_section_' + moduleId)
    };

    console.log('Sections found:', sections);

    // Hide all sections first and remove ALL required attributes
    Object.values(sections).forEach(section => {
        if (section) {
            section.classList.add('hidden');
            // Remove required attributes from all inputs in hidden sections
            const inputs = section.querySelectorAll('input[required]');
            inputs.forEach(input => {
                input.removeAttribute('required');
                console.log('Removed required from:', input.name);
            });
        }
    });

    // Show the selected section
    const selectedSection = sections[contentType];
    if (selectedSection) {
        selectedSection.classList.remove('hidden');
        console.log('Showing section:', selectedSection.id);
        
        // Add required attributes only to visible inputs
        if (contentType === 'secure_video') {
            const fileInput = selectedSection.querySelector('input[type="file"]');
            if (fileInput) {
                fileInput.setAttribute('required', 'required');
                console.log('Added required to secure video file input:', fileInput.id);
            }
        } else if (contentType === 'external_video') {
            const urlInput = selectedSection.querySelector('input[type="url"]');
            if (urlInput) {
                urlInput.setAttribute('required', 'required');
                console.log('Added required to external video URL input');
            }
        }
        // File upload section doesn't require the file input
    }

    // Update file input requirements
    updateFileInputRequirements(moduleId, contentType);
}

function updateFileInputRequirements(moduleId, contentType) {
    const fileInput = document.getElementById('file_input_' + moduleId);
    const secureVideoInput = document.getElementById('secure_video_input_' + moduleId);
    
    console.log('Updating file requirements for:', contentType, 'module:', moduleId);
    console.log('File input:', fileInput);
    console.log('Secure video input:', secureVideoInput);
    
    if (fileInput) {
        if (contentType === 'file') {
            fileInput.setAttribute('accept', '.pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.mp4,.mov,.avi,.mkv,.jpg,.jpeg,.png,.gif,.zip,.mp3,.wav');
            fileInput.removeAttribute('required');
        } else {
            fileInput.removeAttribute('required');
        }
    }
    
    if (secureVideoInput) {
        if (contentType === 'secure_video') {
            secureVideoInput.setAttribute('accept', '.mp4,.mov,.avi,.mkv,.webm');
            secureVideoInput.setAttribute('required', 'required');
        } else {
            secureVideoInput.removeAttribute('required');
        }
    }
}

function resetAttachmentForm(moduleId) {
    const form = document.getElementById('upload-form-' + moduleId);
    if (form) {
        form.reset();
        
        // Reset to file upload by default
        const fileUploadRadio = document.getElementById('file_upload_' + moduleId);
        if (fileUploadRadio) {
            fileUploadRadio.checked = true;
            // Use setTimeout to ensure the change event fires properly
            setTimeout(() => {
                handleContentTypeChange(moduleId, 'file');
            }, 50);
        }
        
        // Hide progress
        const progress = document.getElementById('upload-progress-' + moduleId);
        if (progress) progress.classList.add('hidden');
    }
}

// Enhanced form submission handler - FIXED VERSION
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('form[id^="upload-form-"]').forEach(form => {
        form.addEventListener('submit', function(e) {
            console.log('Form submission detected');
            console.log('Content type:', this.querySelector('input[name="content_type"]:checked')?.value);
            console.log('File input files:', document.getElementById('file_input_' + moduleId)?.files);
            const moduleId = this.id.split('-').pop();
            const contentType = this.querySelector('input[name="content_type"]:checked')?.value;
            console.log('Form submission for content type:', contentType, 'module:', moduleId);
            
            // Clean up any hidden required inputs before submission
            const allInputs = this.querySelectorAll('input[required]');
            allInputs.forEach(input => {
                const parentSection = input.closest('.content-section');
                if (parentSection && parentSection.classList.contains('hidden')) {
                    input.removeAttribute('required');
                    console.log('Removed required from hidden input:', input.name);
                }
            });

            // Validate based on content type
            let isValid = true;
            let errorMessage = '';
            
            if (contentType === 'secure_video') {
                const secureVideoInput = document.querySelector(`#secure_video_input_${moduleId}`);
                console.log('Secure video input found:', secureVideoInput);
                console.log('Secure video files:', secureVideoInput?.files);
                
                if (!secureVideoInput || !secureVideoInput.files || secureVideoInput.files.length === 0) {
                    isValid = false;
                    errorMessage = 'Please select a video file for secure upload.';
                    console.log('Secure video validation failed');
                }
            } else if (contentType === 'external_video') {
                const urlInput = this.querySelector('input[name="video_url"]');
                console.log('External video URL:', urlInput?.value);
                
                if (!urlInput || !urlInput.value.trim()) {
                    isValid = false;
                    errorMessage = 'Please enter a video URL.';
                    console.log('External video validation failed');
                }
            } else if (contentType === 'file') {
                const fileInput = document.getElementById('file_input_' + moduleId);
                console.log('File input files:', fileInput?.files);
                
                if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                    isValid = false;
                    errorMessage = 'Please select a file to upload.';
                    console.log('File upload validation failed');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
                return false;
            }

            console.log('Form validation passed, submitting...');

            // Show progress for ALL file uploads (not just large files)
            let fileInputToCheck = null;
            if (contentType === 'secure_video') {
                fileInputToCheck = document.querySelector(`#secure_video_input_${moduleId}`);
            } else if (contentType === 'file') {
                fileInputToCheck = document.getElementById('file_input_' + moduleId);
            }
            
            // Show progress for any file upload
            if (fileInputToCheck && fileInputToCheck.files.length > 0) {
                showUploadProgress(moduleId);
            }
        });
    });
});

// Improved progress bar function
function showUploadProgress(moduleId) {
    const progressDiv = document.getElementById('upload-progress-' + moduleId);
    const progressBar = document.getElementById('progress-bar-' + moduleId);
    const progressPercentage = document.getElementById('progress-percentage-' + moduleId);
    
    if (progressDiv && progressBar && progressPercentage) {
        progressDiv.classList.remove('hidden');
        progressBar.style.width = '0%';
        progressPercentage.textContent = '0%';
        
        // Simulate progress (in real implementation, you'd use XMLHttpRequest with progress events)
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
            }
            progressBar.style.width = progress + '%';
            progressPercentage.textContent = Math.round(progress) + '%';
        }, 200);
        
        // Clear interval when form is done (this is just simulation)
        setTimeout(() => {
            clearInterval(interval);
        }, 5000);
    }
}

function showUploadProgress(moduleId) {
    const progressDiv = document.getElementById('upload-progress-' + moduleId);
    const progressBar = document.getElementById('progress-bar-' + moduleId);
    const progressPercentage = document.getElementById('progress-percentage-' + moduleId);
    
    if (progressDiv && progressBar && progressPercentage) {
        progressDiv.classList.remove('hidden');
        
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 10;
            if (progress >= 90) {
                clearInterval(interval);
            }
            progressBar.style.width = progress + '%';
            progressPercentage.textContent = Math.round(progress) + '%';
        }, 200);
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('[id^="edit-module-"]').forEach(form => {
            form.classList.add('hidden');
        });
        document.querySelectorAll('[id^="edit-attachment-"]').forEach(form => {
            form.classList.add('hidden');
        });
        document.querySelectorAll('[id^="attachment-form-"]').forEach(form => {
            form.classList.add('hidden');
        });
    }
});
</script>

<style>
.content-section {
    transition: all 0.3s ease;
}

/* Custom radio button styles */
input[type="radio"]:checked + label {
    border-color: #4f46e5;
    background-color: #eef2ff;
}

input[type="radio"]:checked + label i {
    color: #4f46e5;
}

/* Smooth transitions */
.hidden {
    display: none !important;
}

/* Hover effects */
.hover-lift:hover {
    transform: translateY(-2px);
    transition: transform 0.2s ease;
}

/* Custom scrollbar for modules */
.module-container {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

.module-container::-webkit-scrollbar {
    width: 6px;
}

.module-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.module-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.module-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

@keyframes slide-in {
    from {
        transform: translateX(-100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

.animate-slide-in {
    animation: slide-in 0.3s ease-out;
}
</style>
@endsection
@extends('layouts.admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header with Actions -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div class="mb-4 lg:mb-0">
            <h1 class="text-3xl font-bold text-gray-900">Course Review Dashboard</h1>
            <p class="text-gray-600 mt-2">Detailed overview of all course content for publishing approval</p>
            
            <!-- Course Status Badge -->
            <div class="flex items-center mt-3 space-x-4">
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $course->is_published ? 'Published' : 'Draft' }}
                </span>
                <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $course->status === 'approved' ? 'bg-green-100 text-green-800' : ($course->status === 'rejected' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                    {{ ucfirst($course->status ?? 'pending') }}
                </span>
            </div>
        </div>
        
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.courses.edit', $course) }}" 
               class="bg-blue-600 text-white px-5 py-2.5 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                <i class="fas fa-edit mr-2"></i> Edit Course
            </a>
            <a href="{{ route('admin.courses.modules', $course) }}" 
               class="bg-purple-600 text-white px-5 py-2.5 rounded-lg hover:bg-purple-700 transition duration-200 font-semibold">
                <i class="fas fa-layer-group mr-2"></i> Manage Content
            </a>
            <a href="{{ route('admin.courses.index') }}" 
               class="bg-gray-300 text-gray-700 px-5 py-2.5 rounded-lg hover:bg-gray-400 transition duration-200 font-semibold">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Quick Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Modules</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->modules->count() }}</p>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-layer-group text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Content Items</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->modules->sum(fn($module) => $module->attachments->count()) }}</p>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-file-alt text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Duration</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $course->duration ?? 'N/A' }} hours</p>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-clock text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Course Level</p>
                    <p class="text-lg font-bold text-gray-900">
                        @if($course->level == 1) Beginner
                        @elseif($course->level == 2) Intermediate
                        @elseif($course->level == 3) Advanced
                        @elseif($course->level == 4) Expert
                        @else Mixed Level
                        @endif
                    </p>
                </div>
                <div class="bg-orange-100 p-3 rounded-full">
                    <i class="fas fa-signal text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Main Content -->
        <div class="lg:w-2/3 space-y-8">
            <!-- Course Overview -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex flex-col lg:flex-row gap-8">
                    <!-- Course Image -->
                    <div class="lg:w-1/3">
                        <div class="h-64 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl overflow-hidden flex items-center justify-center relative">
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-book-open text-white text-6xl"></i>
                            @endif
                            <div class="absolute top-4 right-4 bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                                {{ $course->is_premium ? 'Premium' : 'Free' }}
                            </div>
                        </div>
                        
                        <!-- Quick Info Cards -->
                        <div class="mt-6 space-y-4">
                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Category</span>
                                    <i class="fas fa-tag text-indigo-600"></i>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $course->category->name ?? 'Uncategorized' }}</p>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Price</span>
                                    <i class="fas fa-dollar-sign text-green-600"></i>
                                </div>
                                <div class="flex items-center justify-between">
                                    <p class="font-bold text-2xl {{ $course->is_premium ? 'text-gray-900' : 'text-green-600' }}">
                                        ${{ number_format($course->price, 2) }}
                                    </p>
                                    @if($course->is_premium)
                                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-bold">PREMIUM</span>
                                    @else
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-bold">FREE</span>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-gray-50 rounded-xl p-4">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-sm font-medium text-gray-600">Created</span>
                                    <i class="fas fa-calendar text-blue-600"></i>
                                </div>
                                <p class="font-semibold text-gray-900">{{ $course->created_at->format('F d, Y') }}</p>
                                <p class="text-sm text-gray-500">{{ $course->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Course Details -->
                    <div class="lg:w-2/3">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4">{{ $course->title }}</h2>
                        
                        <div class="prose max-w-none mb-6">
                            <p class="text-gray-600 text-lg leading-relaxed">{{ $course->description }}</p>
                        </div>
                        
                        <!-- Course Metadata Grid -->
                        <div class="grid gap-6 mb-6">
                            @if($course->objectives)
                            <div class="bg-blue-50 rounded-xl p-5">
                                <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                                    <i class="fas fa-bullseye mr-2"></i> Learning Objectives
                                </h3>
                                <div class="prose max-w-none text-blue-800">
                                    {!! nl2br(e($course->objectives)) !!}
                                </div>
                            </div>
                            @endif
                            
                            @if($course->target_audience)
                            <div class="bg-green-50 rounded-xl p-5">
                                <h3 class="text-lg font-semibold text-green-900 mb-3 flex items-center">
                                    <i class="fas fa-users mr-2"></i> Target Audience
                                </h3>
                                <div class="prose max-w-none text-green-800">
                                    {!! nl2br(e($course->target_audience)) !!}
                                </div>
                            </div>
                            @endif
                            
                            @if($course->requirements)
                            <div class="bg-orange-50 rounded-xl p-5">
                                <h3 class="text-lg font-semibold text-orange-900 mb-3 flex items-center">
                                    <i class="fas fa-tools mr-2"></i> Requirements
                                </h3>
                                <div class="prose max-w-none text-orange-800">
                                    {!! nl2br(e($course->requirements)) !!}
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Modules & Content -->
            <div class="bg-white rounded-2xl shadow-lg p-8">
                <div class="flex justify-between items-center mb-8">
                    <h2 class="text-2xl font-bold text-gray-900">Course Content Details</h2>
                    <div class="flex items-center space-x-4">
                        <span class="bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full font-semibold">
                            {{ $course->modules->count() }} Modules
                        </span>
                        <span class="bg-purple-100 text-purple-800 px-4 py-2 rounded-full font-semibold">
                            {{ $course->modules->sum(fn($module) => $module->attachments->count()) }} Content Items
                        </span>
                    </div>
                </div>
                
                @if($course->modules->count() > 0)
                <div class="space-y-6">
                    @foreach($course->modules->sortBy('order') as $module)
                    <div class="border-2 border-gray-200 rounded-2xl overflow-hidden hover:border-indigo-300 transition duration-200">
                        <!-- Module Header -->
                        <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-5 border-b border-gray-200">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                                <div class="flex items-start space-x-4">
                                    <div class="bg-white rounded-lg p-3 shadow-sm">
                                        <span class="text-lg font-bold text-indigo-600">#{{ $module->order }}</span>
                                    </div>
                                    <div>
                                        <h3 class="text-xl font-bold text-gray-900 mb-1">{{ $module->title }}</h3>
                                        @if($module->description)
                                            <p class="text-gray-600">{{ $module->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="flex items-center space-x-3 mt-4 lg:mt-0">
                                    @if($module->is_free)
                                        <span class="bg-green-100 text-green-800 px-3 py-1.5 rounded-full text-sm font-semibold">
                                            <i class="fas fa-unlock mr-1"></i> Free Preview
                                        </span>
                                    @endif
                                    <span class="bg-blue-100 text-blue-800 px-3 py-1.5 rounded-full text-sm font-semibold">
                                        <i class="fas fa-file mr-1"></i> {{ $module->attachments->count() }} items
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Module Attachments -->
                        @if($module->attachments->count() > 0)
                        <div class="p-6">
                            <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                                <i class="fas fa-list-ul mr-2 text-indigo-600"></i> Content Items
                            </h4>
                            <div class="space-y-4">
                                @foreach($module->attachments->sortBy('order') as $attachment)
                                <div class="bg-gray-50 rounded-xl p-5 hover:bg-white hover:shadow-md transition duration-200 border border-gray-100">
                                    <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                        <!-- Attachment Info -->
                                        <div class="flex-1">
                                            <div class="flex items-start space-x-4">
                                                <!-- File Type Icon -->
                                                <div class="flex-shrink-0">
                                                    @if($attachment->file_type === 'pdf')
                                                        <div class="bg-red-100 p-3 rounded-lg">
                                                            <i class="fas fa-file-pdf text-red-600 text-2xl"></i>
                                                        </div>
                                                    @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv', 'webm']))
                                                        <div class="bg-purple-100 p-3 rounded-lg">
                                                            <i class="fas fa-video text-purple-600 text-2xl"></i>
                                                        </div>
                                                    @elseif($attachment->file_type === 'external_video')
                                                        <div class="bg-red-100 p-3 rounded-lg">
                                                            <i class="fab fa-youtube text-red-600 text-2xl"></i>
                                                        </div>
                                                    @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                        <div class="bg-green-100 p-3 rounded-lg">
                                                            <i class="fas fa-image text-green-600 text-2xl"></i>
                                                        </div>
                                                    @elseif(in_array($attachment->file_type, ['doc', 'docx']))
                                                        <div class="bg-blue-100 p-3 rounded-lg">
                                                            <i class="fas fa-file-word text-blue-600 text-2xl"></i>
                                                        </div>
                                                    @elseif(in_array($attachment->file_type, ['mp3', 'wav', 'ogg']))
                                                        <div class="bg-yellow-100 p-3 rounded-lg">
                                                            <i class="fas fa-music text-yellow-600 text-2xl"></i>
                                                        </div>
                                                    @elseif($attachment->file_type === 'zip')
                                                        <div class="bg-orange-100 p-3 rounded-lg">
                                                            <i class="fas fa-file-archive text-orange-600 text-2xl"></i>
                                                        </div>
                                                    @else
                                                        <div class="bg-gray-100 p-3 rounded-lg">
                                                            <i class="fas fa-file text-gray-600 text-2xl"></i>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Attachment Details -->
                                                <div class="flex-1 min-w-0">
                                                    <h5 class="text-lg font-semibold text-gray-900 mb-2">{{ $attachment->title }}</h5>
                                                    
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm text-gray-600 mb-3">
                                                        <div class="space-y-1">
                                                            <div class="flex items-center">
                                                                <i class="fas fa-tag mr-2 w-4"></i>
                                                                <span class="font-medium">Type:</span>
                                                                <span class="ml-2 bg-gray-200 px-2 py-1 rounded text-xs font-bold uppercase">
                                                                    {{ $attachment->file_type }}
                                                                </span>
                                                            </div>
                                                            <div class="flex items-center">
                                                                <i class="fas fa-sort-numeric-up mr-2 w-4"></i>
                                                                <span class="font-medium">Order:</span>
                                                                <span class="ml-2">{{ $attachment->order }}</span>
                                                            </div>
                                                            @if($attachment->file_size)
                                                            <div class="flex items-center">
                                                                <i class="fas fa-weight-hanging mr-2 w-4"></i>
                                                                <span class="font-medium">Size:</span>
                                                                <span class="ml-2">{{ formatFileSize($attachment->file_size) }}</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="space-y-1">
                                                            @if($attachment->duration)
                                                            <div class="flex items-center">
                                                                <i class="fas fa-clock mr-2 w-4"></i>
                                                                <span class="font-medium">Duration:</span>
                                                                <span class="ml-2">{{ $attachment->duration }} minutes</span>
                                                            </div>
                                                            @endif
                                                            @if($attachment->video_url)
                                                            <div class="flex items-center">
                                                                <i class="fas fa-link mr-2 w-4"></i>
                                                                <span class="font-medium">External URL:</span>
                                                                <span class="ml-2 truncate">{{ $attachment->video_url }}</span>
                                                            </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if($attachment->description)
                                                    <div class="bg-white rounded-lg p-3 border">
                                                        <p class="text-sm text-gray-700">{{ $attachment->description }}</p>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Actions -->
                                        <div class="flex flex-col space-y-2 lg:items-end">
                                            @if($attachment->file_path)
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                               target="_blank" 
                                               class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold text-sm flex items-center">
                                                <i class="fas fa-eye mr-2"></i> Preview
                                            </a>
                                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                                               download 
                                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-semibold text-sm flex items-center">
                                                <i class="fas fa-download mr-2"></i> Download
                                            </a>
                                            @elseif($attachment->video_url)
                                            <a href="{{ $attachment->video_url }}" 
                                               target="_blank" 
                                               class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200 font-semibold text-sm flex items-center">
                                                <i class="fab fa-youtube mr-2"></i> View Video
                                            </a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @else
                        <div class="p-8 text-center bg-gray-50">
                            <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                            <p class="text-gray-500 font-medium">No content items in this module</p>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-12 bg-gray-50 rounded-2xl">
                    <i class="fas fa-layer-group text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Modules Created</h3>
                    <p class="text-gray-500 mb-6">This course doesn't have any modules yet.</p>
                    <a href="{{ route('admin.courses.modules', $course) }}" 
                       class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold">
                        <i class="fas fa-plus mr-2"></i> Create First Module
                    </a>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:w-1/3 space-y-6">
            <!-- Review Actions -->
            @if($course->status === 'pending_review' || !$course->is_published)
            <div class="bg-white rounded-2xl shadow-lg p-6 border-2 border-yellow-200">
                <h3 class="text-xl font-bold text-yellow-800 mb-4 flex items-center">
                    <i class="fas fa-clipboard-check mr-2"></i> Review & Approval
                </h3>
                
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4">
                        <h4 class="font-semibold text-yellow-800 mb-2">Course Status</h4>
                        <p class="text-yellow-700 text-sm">
                            @if($course->status === 'pending_review')
                                This course is awaiting review and approval before it can be published.
                            @elseif($course->status === 'rejected')
                                This course was rejected. Please review the feedback and make necessary changes.
                            @else
                                This course is in draft mode and needs approval.
                            @endif
                        </p>
                    </div>

                    <div class="flex flex-col space-y-3">
                        @if($course->status !== 'approved')
                            <button type="button" 
                                    onclick="showApproveModal()"
                                    class="bg-green-600 text-white px-4 py-3 rounded-xl hover:bg-green-700 transition duration-200 font-semibold flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Approve Course
                            </button>
                        @else
                            <button type="button"
                                    class="bg-green-400 text-white px-4 py-3 rounded-xl cursor-not-allowed opacity-70 font-semibold flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Already Approved
                            </button>
                        @endif

                        <button type="button" 
                                onclick="showRejectModal()"
                                class="bg-red-600 text-white px-4 py-3 rounded-xl hover:bg-red-700 transition duration-200 font-semibold flex items-center justify-center">
                            <i class="fas fa-times-circle mr-2"></i> Reject Course
                        </button>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-3">
                        <p class="text-blue-800 text-sm font-medium flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>
                            Course can only be published after approval
                        </p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Publish Controls -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Publish Controls</h3>
                
                <form action="{{ route('admin.courses.toggle-publish', $course) }}" method="POST">
                    @csrf
                    <button type="submit"
                        class="w-full {{ $course->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} 
                            text-white px-4 py-3 rounded-xl transition duration-200 font-semibold
                            {{ $course->status !== 'approved' ? 'opacity-50 cursor-not-allowed' : '' }}"
                        {{ $course->status !== 'approved' ? 'disabled' : '' }}>
                        <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                        {{ $course->is_published ? 'Unpublish Course' : 'Publish Course' }}
                    </button>

                    @if($course->status !== 'approved')
                        <div class="mt-3 bg-red-50 border border-red-200 rounded-xl p-3">
                            <p class="text-red-700 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-2"></i>
                                Cannot publish until this course is approved.
                            </p>
                        </div>
                    @endif
                </form>
            </div>

            <!-- Course Statistics -->
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-900 mb-4">Content Statistics</h3>
                
                @php
                    $attachments = $course->modules->flatMap->attachments;
                    $fileTypes = $attachments->groupBy('file_type');
                @endphp
                
                <div class="space-y-4">
                    @foreach($fileTypes as $type => $items)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            @if($type === 'pdf')
                                <i class="fas fa-file-pdf text-red-500 mr-3"></i>
                            @elseif(in_array($type, ['mp4', 'mov', 'avi', 'mkv']))
                                <i class="fas fa-video text-purple-500 mr-3"></i>
                            @elseif($type === 'external_video')
                                <i class="fab fa-youtube text-red-500 mr-3"></i>
                            @elseif(in_array($type, ['jpg', 'jpeg', 'png', 'gif']))
                                <i class="fas fa-image text-green-500 mr-3"></i>
                            @else
                                <i class="fas fa-file text-gray-500 mr-3"></i>
                            @endif
                            <span class="font-medium text-gray-700">{{ strtoupper($type) }}</span>
                        </div>
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-sm font-bold">
                            {{ $items->count() }}
                        </span>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-2xl p-6">
                <h3 class="text-xl font-bold text-red-800 mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i> Danger Zone
                </h3>
                
                <div class="space-y-3">
                    <div>
                        <h4 class="font-semibold text-red-800">Delete this course</h4>
                        <p class="text-red-600 text-sm mt-1">Once deleted, this course and all its content cannot be recovered.</p>
                    </div>
                    
                    <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" 
                          onsubmit="return confirm('Are you absolutely sure you want to delete this course? This will permanently remove all modules and content!')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-600 text-white px-4 py-3 rounded-xl hover:bg-red-700 transition duration-200 font-semibold flex items-center justify-center">
                            <i class="fas fa-trash mr-2"></i> Delete Course Permanently
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div id="reject-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mt-4 text-center">Reject Course</h3>
            <form action="{{ route('admin.courses.reject', $course) }}" method="POST" class="mt-6">
                @csrf
                <div class="mb-4">
                    <label for="rejection_reason" class="block text-sm font-medium text-gray-700 text-left mb-2">
                        Reason for Rejection
                    </label>
                    <textarea name="review_notes" id="review_notes" 
                            rows="5" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                            placeholder="Please provide specific, constructive feedback for the instructor to help them improve the course..."
                            required></textarea>
                    <p class="text-xs text-gray-500 mt-2 text-left">This feedback will be sent to the course instructor.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideRejectModal()"
                            class="bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-400 transition duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-red-600 text-white px-6 py-2.5 rounded-xl hover:bg-red-700 transition duration-200 font-medium">
                        Confirm Rejection
                    </button>
                </div>
                @if ($errors->any())
                    <div class="text-red-600 mt-3 text-sm bg-red-50 p-3 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<!-- Approve Course Modal -->
<div id="approve-modal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50 transition-opacity duration-300">
    <div class="relative top-20 mx-auto p-5 border w-full max-w-md shadow-xl rounded-2xl bg-white">
        <div class="mt-3">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                <i class="fas fa-check text-green-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mt-4 text-center">Approve Course</h3>
            <form action="{{ route('admin.courses.approve', $course) }}" method="POST" class="mt-6">
                @csrf
                <div class="mb-4">
                    <label for="approve_notes" class="block text-sm font-medium text-gray-700 text-left mb-2">
                        Approval Notes (Optional)
                    </label>
                    <textarea name="review_notes" id="approve_notes" 
                            rows="4" 
                            class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                            placeholder="Add any positive feedback or notes for the instructor (optional)..."></textarea>
                    <p class="text-xs text-gray-500 mt-2 text-left">This message will be visible to the instructor.</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" 
                            onclick="hideApproveModal()"
                            class="bg-gray-300 text-gray-700 px-6 py-2.5 rounded-xl hover:bg-gray-400 transition duration-200 font-medium">
                        Cancel
                    </button>
                    <button type="submit" 
                            class="bg-green-600 text-white px-6 py-2.5 rounded-xl hover:bg-green-700 transition duration-200 font-medium flex items-center">
                        <i class="fas fa-check mr-2"></i> Approve Course
                    </button>
                </div>
                @if ($errors->any())
                    <div class="text-red-600 mt-3 text-sm bg-red-50 p-3 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-2"></i> {{ $error }}</p>
                        @endforeach
                    </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script>
    function showRejectModal() {
        document.getElementById('reject-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideRejectModal() {
        document.getElementById('reject-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showApproveModal() {
        document.getElementById('approve-modal').classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function hideApproveModal() {
        document.getElementById('approve-modal').classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const approveModal = document.getElementById('approve-modal');
        const rejectModal = document.getElementById('reject-modal');
        
        if (event.target === approveModal) {
            hideApproveModal();
        }
        if (event.target === rejectModal) {
            hideRejectModal();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            hideApproveModal();
            hideRejectModal();
        }
    });
</script>

<style>
.prose {
    max-width: none;
}
.prose p {
    margin-bottom: 0.75rem;
    line-height: 1.6;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

@endsection
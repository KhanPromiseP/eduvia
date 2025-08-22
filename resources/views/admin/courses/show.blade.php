@extends('layouts.admin')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header with Actions -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Course Details</h1>
            <p class="text-gray-600">View and manage course information</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('admin.courses.edit', $course) }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
                <i class="fas fa-edit mr-2"></i> Edit Course
            </a>
            <a href="{{ route('admin.courses.modules', $course) }}" 
               class="bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition">
                <i class="fas fa-layer-group mr-2"></i> Manage Modules
            </a>
            <a href="{{ route('admin.courses.index') }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
                <i class="fas fa-arrow-left mr-2"></i> Back to List
            </a>
        </div>
    </div>

    <!-- Course Overview Card -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-6">
            <!-- Course Image -->
            <div class="md:w-1/3">
                <div class="h-48 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-full object-cover">
                    @else
                        <div class="text-gray-400 text-4xl">
                            <i class="fas fa-book"></i>
                        </div>
                    @endif
                </div>
                
                <!-- Quick Stats -->
                <div class="mt-4 space-y-2">
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $course->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Price:</span>
                        <span class="font-semibold">${{ number_format($course->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Level:</span>
                        <span class="font-semibold">
                            @if($course->level == 1) Beginner
                            @elseif($course->level == 2) Intermediate
                            @else Advanced
                            @endif
                        </span>
                    </div>
                    @if($course->duration)
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-semibold">{{ $course->duration }} hours</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Modules:</span>
                        <span class="font-semibold">{{ $course->modules->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-semibold">{{ $course->created_at->format('M d, Y') }}</span>
                    </div>
                     <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Updated:</span>
                        <span class="font-semibold">{{ $course->updated_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Course Details -->
            <div class="md:w-2/3">
                <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $course->title }}</h2>
                
                <div class="prose max-w-none mb-6">
                    <p class="text-gray-600">{{ $course->description }}</p>
                </div>
                
                <!-- Course Metadata -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                    @if($course->objectives)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Learning Objectives</h3>
                        <div class="prose max-w-none">
                            {!! nl2br(e($course->objectives)) !!}
                        </div>
                    </div>
                    @endif
                    
                    @if($course->target_audience)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Target Audience</h3>
                        <div class="prose max-w-none">
                            {!! nl2br(e($course->target_audience)) !!}
                        </div>
                    </div>
                    @endif
                    
                    @if($course->requirements)
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Requirements</h3>
                        <div class="prose max-w-none">
                            {!! nl2br(e($course->requirements)) !!}
                        </div>
                    </div>
                    @endif
                </div>
                
                <!-- Publish Toggle -->
                <form action="{{ route('admin.courses.toggle-publish', $course) }}" method="POST" class="mt-4">
                    @csrf
                    <button type="submit" 
                            class="{{ $course->is_published ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded transition">
                        <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }} mr-2"></i>
                        {{ $course->is_published ? 'Unpublish Course' : 'Publish Course' }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modules Section -->
    <div class="bg-white shadow-md rounded-lg p-6 mb-6">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-xl font-bold text-gray-800">Course Modules</h2>
            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                {{ $course->modules->count() }} modules
            </span>
        </div>
        
        @if($course->modules->count() > 0)
        <div class="space-y-4">
            @foreach($course->modules->sortBy('order') as $module)
            <div class="border rounded-lg overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 flex justify-between items-center">
                    <div>
                        <h3 class="font-semibold">{{ $module->title }}</h3>
                        <p class="text-sm text-gray-600">Order: {{ $module->order }}</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        @if($module->is_free)
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Free Preview</span>
                        @endif
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                            {{ $module->attachments->count() }} attachments
                        </span>
                    </div>
                </div>
                
                @if($module->description)
                <div class="p-4 border-b">
                    <p class="text-gray-600">{{ $module->description }}</p>
                </div>
                @endif
                
                <!-- Attachments -->
                @if($module->attachments->count() > 0)
                <div class="p-4">
                    <h4 class="font-medium text-gray-700 mb-3">Attachments</h4>
                    <div class="space-y-2">
                        @foreach($module->attachments->sortBy('order') as $attachment)
                        <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                            <div class="flex items-center">
                                @if($attachment->file_type === 'pdf')
                                    <i class="fas fa-file-pdf text-red-600 mr-2"></i>
                                @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi']))
                                    <i class="fas fa-video text-purple-600 mr-2"></i>
                                @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                    <i class="fas fa-image text-green-600 mr-2"></i>
                                @elseif(in_array($attachment->file_type, ['doc', 'docx']))
                                    <i class="fas fa-file-word text-blue-600 mr-2"></i>
                                @else
                                    <i class="fas fa-file text-gray-600 mr-2"></i>
                                @endif
                                <div>
                                    <span class="text-sm">{{ $attachment->title }}</span>
                                    <span class="text-xs text-gray-500 block">
                                        {{ strtoupper($attachment->file_type) }} • 
                                        @if($attachment->file_size)
                                            {{ number_format($attachment->file_size / 1024, 1) }} KB
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <a href="{{ asset('storage/' . $attachment->file_path) }}" 
                               target="_blank" 
                               class="text-indigo-600 hover:text-indigo-900 text-sm">
                                <i class="fas fa-download"></i>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 bg-gray-50 rounded-lg">
            <i class="fas fa-layer-group text-4xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">No modules added yet.</p>
            <a href="{{ route('admin.courses.modules', $course) }}" 
               class="text-indigo-600 hover:text-indigo-800 mt-2 inline-block">
                Add your first module
            </a>
        </div>
        @endif
    </div>

    <!-- Danger Zone -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <h2 class="text-xl font-bold text-red-800 mb-4">Danger Zone</h2>
        
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div class="mb-4 sm:mb-0">
                <h3 class="font-semibold text-red-800">Delete this course</h3>
                <p class="text-red-600 text-sm">Once deleted, this course and all its modules cannot be recovered.</p>
            </div>
            
            <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" 
                  onsubmit="return confirm('Are you absolutely sure you want to delete this course? This action cannot be undone!')">
                @csrf
                @method('DELETE')
                <button type="submit" 
                        class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition">
                    <i class="fas fa-trash mr-2"></i> Delete Course
                </button>
            </form>
        </div>
    </div>
</div>

<style>
.prose {
    max-width: none;
}
.prose p {
    margin-bottom: 0.5rem;
}
</style>
@endsection
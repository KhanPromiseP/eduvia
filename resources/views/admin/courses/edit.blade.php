@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Edit Course</h1>
            <p class="text-gray-600">Update course information and settings</p>
        </div>
        <a href="{{ route('admin.courses.show', $course) }}" 
           class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
            <i class="fas fa-arrow-left mr-2"></i> Back to Course
        </a>
    </div>

    <!-- Form -->
    <form action="{{ route('admin.courses.update', $course) }}" method="POST" enctype="multipart/form-data" 
          class="bg-white shadow-md rounded-lg p-6">
        @csrf
        @method('PUT')

        <!-- Image Preview & Upload -->
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 mb-2">Course Image</label>
            <div class="flex flex-col md:flex-row gap-6 items-start">
                <div class="w-full md:w-1/3">
                    <div class="h-48 bg-gray-200 rounded-lg overflow-hidden flex items-center justify-center">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" 
                                 alt="{{ $course->title }}" 
                                 id="image-preview"
                                 class="w-full h-full object-cover">
                        @else
                            <div id="image-preview" class="text-gray-400 text-4xl flex items-center justify-center w-full h-full">
                                <i class="fas fa-book"></i>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="w-full md:w-2/3">
                    <input type="file" name="image" id="image" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                           accept="image/*" 
                           onchange="previewImage(this)">
                    @error('image')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-sm text-gray-500 mt-2">Recommended: 800x450px, JPG, PNG or GIF (Max: 2MB)</p>
                    
                    @if($course->image)
                    <div class="mt-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="remove_image" value="1" 
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-red-600">Remove current image</span>
                        </label>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Basic Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $course->title) }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Premium Course</label>
                <div class="flex items-center space-x-3">
                    <input type="checkbox" id="is_premium" name="is_premium" value="1" 
                           {{ old('is_premium', $course->price > 0 ? 1 : 0) ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <label for="is_premium" class="text-gray-900 text-sm">Make this a premium course</label>
                </div>

                <input type="number" name="price" id="price" value="{{ old('price', $course->price) }}" 
                       step="0.01" min="0"
                       class="w-full mt-2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       placeholder="Free Course / No Price">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Category -->
        <div class="mb-6">
            <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
            <select name="category_id" id="category_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    required>
                <option value="">Select Category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ old('category_id', $course->category_id) == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
            @error('category_id')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Description -->
        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      required>{{ old('description', $course->description) }}</textarea>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Level & Duration -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                <select name="level" id="level" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select Level</option>
                    <option value="1" {{ old('level', $course->level) == 1 ? 'selected' : '' }}>Beginner</option>
                    <option value="2" {{ old('level', $course->level) == 2 ? 'selected' : '' }}>Intermediate</option>
                    <option value="3" {{ old('level', $course->level) == 3 ? 'selected' : '' }}>Advanced</option>
                    <option value="4" {{ old('level', $course->level) == 4 ? 'selected' : '' }}>Expart</option>
                    <option value="5" {{ old('level', $course->level) == 5 ? 'selected' : '' }}>Beginner to Advanced</option>
                </select>
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                <input type="number" name="duration" id="duration" value="{{ old('duration', $course->duration) }}" 
                       min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <!-- Course Content -->
        <div class="mb-6">
            <label for="objectives" class="block text-sm font-medium text-gray-700 mb-1">Learning Objectives</label>
            <textarea name="objectives" id="objectives" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="Enter each objective on a new line">{{ old('objectives', $course->objectives) }}</textarea>
            @error('objectives')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
            <textarea name="target_audience" id="target_audience" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('target_audience', $course->target_audience) }}</textarea>
            @error('target_audience')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
            <textarea name="requirements" id="requirements" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('requirements', $course->requirements) }}</textarea>
            @error('requirements')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Status -->
        <div class="mb-6">
            <div class="flex items-center">
                <input type="checkbox" name="is_published" id="is_published" value="1" 
                       {{ old('is_published', $course->is_published) ? 'checked' : '' }}
                       class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                <label for="is_published" class="ml-2 block text-sm text-gray-900">Publish this course</label>
            </div>
            @error('is_published')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Form Actions -->
        <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
            <a href="{{ route('admin.courses.show', $course) }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                Update Course
            </button>
        </div>
    </form>

    <!-- Modules Quick Stats -->
    <div class="bg-white shadow-md rounded-lg p-6 mt-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4">Course Modules</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div class="bg-blue-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-blue-100 p-3 rounded-full mr-4">
                        <i class="fas fa-layer-group text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-blue-600">{{ $course->modules->count() }}</p>
                        <p class="text-sm text-blue-800">Total Modules</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-green-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-green-100 p-3 rounded-full mr-4">
                        <i class="fas fa-unlock text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-green-600">
                            {{ $course->modules->where('is_free', true)->count() }}
                        </p>
                        <p class="text-sm text-green-800">Free Preview Modules</p>
                    </div>
                </div>
            </div>
            
            <div class="bg-purple-50 p-4 rounded-lg">
                <div class="flex items-center">
                    <div class="bg-purple-100 p-3 rounded-full mr-4">
                        <i class="fas fa-paperclip text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-purple-600">
                            {{ $course->modules->sum(fn($module) => $module->attachments->count()) }}
                        </p>
                        <p class="text-sm text-purple-800">Total Attachments</p>
                    </div>
                </div>
            </div>
        </div>
        
        <a href="{{ route('admin.courses.modules', $course) }}" 
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
            <i class="fas fa-layer-group mr-2"></i> Manage Modules
        </a>
    </div>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('image-preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = '';
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'w-full h-full object-cover';
            preview.appendChild(img);
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Auto-resize textareas
document.addEventListener('DOMContentLoaded', function() {
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
        // Trigger initial resize
        textarea.dispatchEvent(new Event('input'));
    });
});

// Premium / Free toggle
document.addEventListener('DOMContentLoaded', function() {
    const premiumCheckbox = document.getElementById('is_premium');
    const priceInput = document.getElementById('price');

    function togglePrice() {
        if (premiumCheckbox.checked) {
            priceInput.disabled = false;
            priceInput.required = true;
            priceInput.placeholder = '';
        } else {
            priceInput.disabled = true;
            priceInput.required = false;
            priceInput.placeholder = 'Free Course / No Price';
            priceInput.value = '';
        }
    }

    premiumCheckbox.addEventListener('change', togglePrice);
    togglePrice(); // initial load
});
</script>

<style>
textarea {
    resize: none;
    min-height: 80px;
}
</style>
@endsection
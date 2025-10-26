@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Create New Course</h1>

    <form action="{{ route('instructor.courses.store') }}" method="POST" enctype="multipart/form-data" class="bg-white shadow-md rounded-lg p-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Course Title *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                       required>
                @error('title')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                <select name="category_id" id="category_id" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label class="flex items-center space-x-2">
                    <input type="checkbox" id="is_premium" name="is_premium" value="1" {{ old('is_premium') ? 'checked' : '' }}
                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                    <span class="text-sm text-gray-900">Premium Course (Paid)</span>
                </label>
                @error('is_premium')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Price ($)</label>
                <input type="number" name="price" id="price" value="{{ old('price') }}" step="0.01" min="0"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500
                            disabled:bg-gray-100 disabled:text-gray-500"
                    disabled placeholder="Free course">
                @error('price')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-6">
            <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description *</label>
            <textarea name="description" id="description" rows="4"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      required>{{ old('description') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Minimum 100 characters</p>
            @error('description')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Level and Duration -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <label for="level" class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                <select name="level" id="level" 
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        required>
                    <option value="">Select Level</option>
                    @foreach($levels as $value => $label)
                        <option value="{{ $value }}" {{ old('level') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
                @error('level')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (hours)</label>
                <input type="number" name="duration" id="duration" value="{{ old('duration') }}" min="0"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500">
                @error('duration')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

         <div class="mb-6">
            <label for="objectives" class="block text-sm font-medium text-gray-700 mb-1">Learning Objectives</label>
            <textarea name="objectives" id="objectives" rows="3"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="What will students learn from this course?">{{ old('objectives') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Minimum 50 characters</p>
            @error('objectives')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="target_audience" class="block text-sm font-medium text-gray-700 mb-1">Target Audience</label>
            <textarea name="target_audience" id="target_audience" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="Who is this course for?">{{ old('target_audience') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Minimum 50 characters</p>
            @error('target_audience')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="requirements" class="block text-sm font-medium text-gray-700 mb-1">Requirements</label>
            <textarea name="requirements" id="requirements" rows="2"
                      class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                      placeholder="What do students need to know before taking this course?">{{ old('requirements') }}</textarea>
            <p class="mt-1 text-xs text-gray-500">Minimum 50 characters</p>
            @error('requirements')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Course Image</label>
            <input type="file" name="image" id="image" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500"
                   accept="image/*">
            <p class="mt-1 text-xs text-gray-500">Supported formats: JPEG, PNG, JPG, GIF, WEBP. Max size: 2MB</p>
            @error('image')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('instructor.courses.index') }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded hover:bg-gray-400 transition">
                Cancel
            </a>
            <button type="submit" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                Create Course
            </button>
        </div>
    </form>
</div>

<script>
    const premiumCheckbox = document.getElementById('is_premium');
    const priceInput = document.getElementById('price');

    function togglePriceField() {
        if (premiumCheckbox.checked) {
            priceInput.disabled = false;
            priceInput.required = true;
            priceInput.placeholder = "Enter price in USD";
        } else {
            priceInput.disabled = true;
            priceInput.required = false;
            priceInput.value = "";
            priceInput.placeholder = "Free course";
        }
    }

    premiumCheckbox.addEventListener('change', togglePriceField);

    // Initialize on page load
    togglePriceField();
</script>
@endsection
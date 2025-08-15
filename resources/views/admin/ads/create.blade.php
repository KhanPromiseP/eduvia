@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-8">Create New Ad</h1>
            
            <form action="{{ route('admin.ads.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                {{-- Basic Information --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Ad Title</label>
                        <input type="text" id="title" name="title" value="{{ old('title') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               required>
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">Ad Type</label>
                        <select id="type" name="type" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Select Ad Type</option>
                            @foreach($adTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- User and Product --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_id" class="block text-sm font-medium text-gray-700 mb-2">Advertiser</label>
                        <select id="user_id" name="user_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                                required>
                            <option value="">Select Advertiser</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="product_id" class="block text-sm font-medium text-gray-700 mb-2">Product (Optional)</label>
                        <select id="product_id" name="product_id" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>
                                    {{ $product->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('product_id')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Content --}}
                <div>
                    <label for="content" class="block text-sm font-medium text-gray-700 mb-2">Ad Content</label>
                    <div id="content-help" class="text-sm text-gray-500 mb-2">
                        For image ads: Enter image URL or upload file<br>
                        For video ads: Enter video URL<br>
                        For banner/JS ads: Enter HTML/JavaScript code
                    </div>
                    <textarea id="content" name="content" rows="6" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                              required>{{ old('content') }}</textarea>
                    @error('content')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Link and Placement --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="link" class="block text-sm font-medium text-gray-700 mb-2">Target URL (Optional)</label>
                        <input type="url" id="link" name="link" value="{{ old('link') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500" 
                               placeholder="https://example.com">
                        @error('link')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="placement" class="block text-sm font-medium text-gray-700 mb-2">Placement</label>
                        <select id="placement" name="placement" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Any Placement</option>
                            @foreach($placements as $value => $label)
                                <option value="{{ $value }}" {{ old('placement') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('placement')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Schedule --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="start_at" class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                        <input type="datetime-local" id="start_at" name="start_at" value="{{ old('start_at') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('start_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label for="end_at" class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                        <input type="datetime-local" id="end_at" name="end_at" value="{{ old('end_at') }}" 
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        @error('end_at')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Advanced Options --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Advanced Options</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label for="weight" class="block text-sm font-medium text-gray-700 mb-2">Weight (1-10)</label>
                            <input type="number" id="weight" name="weight" min="1" max="10" value="{{ old('weight', 1) }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Higher weight = more likely to be shown</p>
                        </div>
                        
                        <div>
                            <label for="max_impressions" class="block text-sm font-medium text-gray-700 mb-2">Max Impressions</label>
                            <input type="number" id="max_impressions" name="max_impressions" min="0" value="{{ old('max_impressions') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Leave empty for unlimited</p>
                        </div>
                        
                        <div>
                            <label for="max_clicks" class="block text-sm font-medium text-gray-700 mb-2">Max Clicks</label>
                            <input type="number" id="max_clicks" name="max_clicks" min="0" value="{{ old('max_clicks') }}" 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-xs text-gray-500">Leave empty for unlimited</p>
                        </div>
                    </div>
                </div>

                {{-- Targeting --}}
                <div class="border-t pt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Targeting Options</h3>
                    
                    <div>
                        <label for="targeting" class="block text-sm font-medium text-gray-700 mb-2">Targeting JSON</label>
                        <textarea id="targeting" name="targeting" rows="4" 
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                  placeholder='{"device": "mobile", "hours": [9,10,11,12,13,14,15,16,17], "urls": ["/products", "/categories"]}'>{{ old('targeting') }}</textarea>
                        <p class="mt-1 text-xs text-gray-500">
                            Example targeting options: device (mobile/tablet/desktop), hours (0-23), urls (path patterns)
                        </p>
                        @error('targeting')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Status Options --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               {{ old('is_active') ? 'checked' : '' }}>
                        <label for="is_active" class="ml-2 block text-sm text-gray-900">
                            Active
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="is_random" name="is_random" value="1" 
                               class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded" 
                               {{ old('is_random') ? 'checked' : '' }}>
                        <label for="is_random" class="ml-2 block text-sm text-gray-900">
                            Random Display
                        </label>
                    </div>
                </div>

                {{-- Submit Buttons --}}
                <div class="flex justify-end space-x-4 pt-6 border-t">
                    <a href="{{ route('admin.ads.index') }}" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Cancel
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Create Ad
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // JSON validation for targeting field
    const targetingField = document.getElementById('targeting');
    targetingField.addEventListener('blur', function() {
        if (this.value.trim()) {
            try {
                JSON.parse(this.value);
                this.classList.remove('border-red-300');
                this.classList.add('border-green-300');
            } catch (e) {
                this.classList.remove('border-green-300');
                this.classList.add('border-red-300');
            }
        }
    });

    // Dynamic content help based on ad type
    const typeField = document.getElementById('type');
    const contentHelp = document.getElementById('content-help');
    
    typeField.addEventListener('change', function() {
        const helpTexts = {
            'image': 'Enter the image URL (e.g., https://example.com/image.jpg)',
            'video': 'Enter the video URL (e.g., https://example.com/video.mp4)',
            'banner': 'Enter HTML code for the banner advertisement',
            'js': 'Enter JavaScript code for the advertisement',
            'popup': 'Enter HTML content for the popup advertisement',
            'persistent': 'Enter HTML content for the persistent bottom banner',
            'interstitial': 'Enter HTML content for the full-screen advertisement'
        };
        
        contentHelp.textContent = helpTexts[this.value] || 'Enter the appropriate content for your ad type';
    });
});
</script>
@endsection
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-blue-600">Step 1 of 4</div>
                <div class="text-sm text-gray-500">Basic Information</div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 25%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Tell Us About Yourself</h1>
                <p class="text-gray-600">Let's start with some basic information about you and your expertise</p>
            </div>

            <form action="{{ route('instructor.apply.step1') }}" method="POST">
                @csrf
                
                <!-- Headline -->
                <div class="mb-6">
                    <label for="headline" class="block text-sm font-medium text-gray-700 mb-2">
                        Professional Headline *
                    </label>
                    <input type="text" name="headline" id="headline" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="e.g., Senior Web Developer & Educator"
                        value="{{ old('headline') }}" required>
                    <p class="text-sm text-gray-500 mt-1">A short, compelling description of your professional identity</p>
                    @error('headline')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Area of Expertise -->
                <div class="mb-6">
                    <label for="expertise" class="block text-sm font-medium text-gray-700 mb-2">
                        Area of Expertise *
                    </label>
                    <select name="expertise" id="expertise" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition" required>
                        <option value="">Select your main expertise area</option>
                        <option value="Web Development">Web Development</option>
                        <option value="Mobile Development">Mobile Development</option>
                        <option value="Data Science">Data Science</option>
                        <option value="Machine Learning">Machine Learning</option>
                        <option value="Digital Marketing">Digital Marketing</option>
                        <option value="Graphic Design">Graphic Design</option>
                        <option value="Business">Business & Entrepreneurship</option>
                        <option value="Photography">Photography</option>
                        <option value="Music">Music</option>
                        <option value="Language">Language Learning</option>
                        <option value="Other">Other</option>
                    </select>
                    @error('expertise')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bio -->
                <div class="mb-8">
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Professional Bio *
                    </label>
                    <textarea name="bio" id="bio" rows="6" 
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                        placeholder="Tell us about your professional background, experience, achievements, and why you want to become an instructor..."
                        required>{{ old('bio') }}</textarea>
                    <div class="flex justify-between items-center mt-1">
                        <p class="text-sm text-gray-500">Minimum 100 characters. Share your expertise and teaching philosophy.</p>
                        <span id="bio-counter" class="text-sm text-gray-500">0/100</span>
                    </div>
                    @error('bio')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Navigation -->
                <div class="flex justify-end">
                    <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105">
                        Continue to Step 2
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const bioTextarea = document.getElementById('bio');
    const bioCounter = document.getElementById('bio-counter');
    
    bioTextarea.addEventListener('input', function() {
        const count = this.value.length;
        bioCounter.textContent = `${count}/100`;
        
        if (count < 100) {
            bioCounter.classList.add('text-red-500');
            bioCounter.classList.remove('text-green-500');
        } else {
            bioCounter.classList.remove('text-red-500');
            bioCounter.classList.add('text-green-500');
        }
    });

    // Trigger input event to update counter initially
    bioTextarea.dispatchEvent(new Event('input'));
});
</script>
@endsection
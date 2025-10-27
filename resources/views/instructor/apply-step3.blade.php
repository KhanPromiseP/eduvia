@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-12">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Progress Bar -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <div class="text-sm font-medium text-blue-600">Step 3 of 5</div>
                <div class="text-sm text-gray-500">Professional Links</div>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-blue-600 h-2 rounded-full" style="width: 60%"></div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-xl p-8">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Professional Links</h1>
                <p class="text-gray-600">Add your professional profiles and introduction</p>
            </div>

            <form action="{{ route('instructor.apply.step3.store') }}" method="POST">
                @csrf
                
                <!-- Application Review -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-blue-800 mb-4">Application Summary</h3>
                    
                    @php
                        $step1 = session('application.step1');
                        $step2 = session('application.step2');
                    @endphp
                    
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Headline:</span>
                            <span class="font-medium">{{ $step1['headline'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Expertise:</span>
                            <span class="font-medium">{{ $step1['expertise'] }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Skills:</span>
                            <span class="font-medium">{{ implode(', ', array_slice($step2['skills'], 0, 3)) }}{{ count($step2['skills']) > 3 ? '...' : '' }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Languages:</span>
                            <span class="font-medium">{{ implode(', ', $step2['languages']) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Professional Links -->
                <div class="space-y-6 mb-8">
                    <!-- LinkedIn -->
                    <div>
                        <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-2">
                            LinkedIn Profile
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                <i class="fab fa-linkedin"></i>
                            </span>
                            <input type="url" name="linkedin_url" id="linkedin_url" 
                                class="flex-1 min-w-0 block w-full px-3 py-3 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="https://linkedin.com/in/yourprofile"
                                value="{{ old('linkedin_url') }}">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Your LinkedIn profile helps verify your professional background</p>
                    </div>

                    <!-- Website/Blog -->
                    <div>
                        <label for="website_url" class="block text-sm font-medium text-gray-700 mb-2">
                            Personal Website/Blog
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                <i class="fas fa-globe"></i>
                            </span>
                            <input type="url" name="website_url" id="website_url" 
                                class="flex-1 min-w-0 block w-full px-3 py-3 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="https://yourwebsite.com"
                                value="{{ old('website_url') }}">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Share your portfolio, blog, or personal website</p>
                    </div>

                    <!-- Video Intro -->
                    <div>
                        <label for="video_intro" class="block text-sm font-medium text-gray-700 mb-2">
                            Introduction Video (Optional)
                        </label>
                        <div class="flex">
                            <span class="inline-flex items-center px-3 rounded-l-md border border-r-0 border-gray-300 bg-gray-50 text-gray-500">
                                <i class="fab fa-youtube"></i>
                            </span>
                            <input type="url" name="video_intro" id="video_intro" 
                                class="flex-1 min-w-0 block w-full px-3 py-3 rounded-none rounded-r-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                placeholder="https://youtube.com/your-intro-video"
                                value="{{ old('video_intro') }}">
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Share a short video introducing yourself and your teaching style (YouTube or Vimeo link)</p>
                    </div>
                </div>

                <!-- Next Steps Info -->
                <div class="bg-purple-50 border border-purple-200 rounded-lg p-6 mb-8">
                    <h3 class="font-semibold text-purple-800 mb-3 flex items-center">
                        <i class="fas fa-info-circle mr-2"></i>
                        Next Step: Document Verification
                    </h3>
                    <p class="text-sm text-purple-700">
                        After completing this step, you'll be asked to upload verification documents including:
                    </p>
                    <ul class="text-sm text-purple-700 mt-2 space-y-1 list-disc list-inside">
                        <li>Government-issued ID card</li>
                        <li>Professional certificate or diploma</li>
                        <li>Passport photo (optional)</li>
                    </ul>
                </div>

                <!-- Terms Agreement -->
                <div class="mb-8">
                    <label class="flex items-start">
                        <input type="checkbox" name="agree_terms" value="1" 
                            class="mt-1 rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                            {{ old('agree_terms') ? 'checked' : '' }} required>
                        <span class="ml-3 text-sm text-gray-700">
                            I agree to the 
                            <a href="#" class="text-blue-600 hover:text-blue-500">Instructor Terms</a>
                            and 
                            <a href="#" class="text-blue-600 hover:text-blue-500">Privacy Policy</a>. 
                            I understand that my application will be reviewed and I may be contacted for additional information.
                        </span>
                    </label>
                    @error('agree_terms')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Navigation -->
                <div class="flex justify-between items-center">
                    <a href="{{ route('instructor.apply.step2') }}" 
                        class="px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Back
                    </a>
                    <button type="submit" 
                        class="px-8 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition-all duration-200 transform hover:scale-105">
                        Continue to Documents
                        <i class="fas fa-arrow-right ml-2"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add real-time URL validation
    const linkedinInput = document.getElementById('linkedin_url');
    const websiteInput = document.getElementById('website_url');
    const videoInput = document.getElementById('video_intro');

    function validateURL(input, expectedDomain = null) {
        if (!input.value) return true;
        
        try {
            const url = new URL(input.value);
            if (expectedDomain && !url.hostname.includes(expectedDomain)) {
                input.classList.add('border-red-300');
                input.classList.remove('border-gray-300');
                return false;
            }
            input.classList.remove('border-red-300');
            input.classList.add('border-gray-300');
            return true;
        } catch {
            input.classList.add('border-red-300');
            input.classList.remove('border-gray-300');
            return false;
        }
    }

    linkedinInput.addEventListener('blur', () => validateURL(linkedinInput, 'linkedin.com'));
    websiteInput.addEventListener('blur', () => validateURL(websiteInput));
    videoInput.addEventListener('blur', () => validateURL(videoInput));

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        let isValid = true;

        if (linkedinInput.value && !validateURL(linkedinInput, 'linkedin.com')) {
            isValid = false;
            alert('Please enter a valid LinkedIn URL (should contain linkedin.com)');
        }

        if (websiteInput.value && !validateURL(websiteInput)) {
            isValid = false;
            alert('Please enter a valid website URL');
        }

        if (videoInput.value && !validateURL(videoInput)) {
            isValid = false;
            alert('Please enter a valid video URL');
        }

        if (!isValid) {
            e.preventDefault();
        }
    });
});
</script>

<style>
.border-red-300 {
    border-color: #fca5a5;
}

input:focus.border-red-300 {
    border-color: #fca5a5;
    ring-color: #fca5a5;
}
</style>
@endsection
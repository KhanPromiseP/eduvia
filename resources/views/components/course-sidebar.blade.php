<div class="bg-white rounded-lg shadow-lg sticky top-6">
    <!-- Course Image -->
    <div class="relative">
        @if($course->image)
            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" 
                 class="w-full h-48 object-cover rounded-t-lg">
        @else
            <div class="w-full h-48 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-t-lg flex items-center justify-center">
                <i class="fas fa-graduation-cap text-white text-6xl"></i>
            </div>
        @endif
        
        <!-- Premium Badge -->
        @if($course->is_premium)
            <div class="absolute top-4 right-4 bg-yellow-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                <i class="fas fa-crown mr-1"></i> Premium
            </div>
        @else
            <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                <i class="fas fa-check mr-1"></i> Free
            </div>
        @endif
    </div>

    <!-- Pricing and Enrollment -->
    <div class="p-6 border-b">
        @if($course->is_premium)
            <div class="text-center mb-4">
                <div class="flex items-center justify-center space-x-2 mb-2">
                    <span class="text-3xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                    @if($course->original_price && $course->original_price > $course->price)
                        <span class="text-lg text-gray-500 line-through">${{ number_format($course->original_price, 2) }}</span>
                        <span class="bg-red-100 text-red-800 text-sm px-2 py-1 rounded-full">
                            {{ calculateDiscountPercentage($course->original_price, $course->price) }}% off
                        </span>
                    @endif
                </div>
                @if($course->original_price && $course->original_price > $course->price)
                    <p class="text-sm text-gray-600">Limited time offer</p>
                @endif
            </div>
        @else
            <div class="text-center mb-4">
                <span class="text-3xl font-bold text-green-600">Free</span>
                <p class="text-sm text-gray-600 mt-1">Lifetime access</p>
            </div>
        @endif

        <!-- Enrollment Buttons -->
        @if($userHasPurchased)
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-center mb-4">
                <i class="fas fa-check-circle text-green-500 text-xl mb-2"></i>
                <p class="text-green-800 font-semibold">You're enrolled!</p>
                <a href="{{ route('userdashboard') }}" 
                   class="mt-3 w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 block">
                    Continue Learning
                </a>
            </div>
        @else
            @if($course->is_premium)
                <form action="{{ route('payment.initiate', $course) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-200 mb-3">
                        Enroll Now - ${{ number_format($course->price, 2) }}
                    </button>
                </form>
            @else
                <a href="{{ route('quick.purchase', $course) }}" 
                   class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition duration-200 mb-3 block text-center">
                    Enroll For Free
                </a>
            @endif
            
            <p class="text-center text-sm text-gray-600 mb-4">30-Day Money-Back Guarantee</p>
        @endif

        <!-- Quick Features -->
        <div class="space-y-3">
            <div class="flex items-center text-sm">
                <i class="fas fa-play-circle text-indigo-600 mr-3 w-5"></i>
                <span>{{ $course->modules->sum(fn($m) => $m->attachments->count()) }} on-demand videos</span>
            </div>
            <div class="flex items-center text-sm">
                <i class="fas fa-file-alt text-indigo-600 mr-3 w-5"></i>
                <span>{{ $course->modules->count() }} learning modules</span>
            </div>
            <div class="flex items-center text-sm">
                <i class="fas fa-infinity text-indigo-600 mr-3 w-5"></i>
                <span>Full lifetime access</span>
            </div>
            <div class="flex items-center text-sm">
                <i class="fas fa-mobile-alt text-indigo-600 mr-3 w-5"></i>
                <span>Access on mobile and TV</span>
            </div>
            <div class="flex items-center text-sm">
                <i class="fas fa-trophy text-indigo-600 mr-3 w-5"></i>
                <span>Certificate of completion</span>
            </div>
        </div>
    </div>

    <!-- Share & Save -->
    <div class="p-4 border-b">
        <div class="flex space-x-2">
            <button onclick="shareCourse({{ $course->id }})" 
                    class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                <i class="fas fa-share mr-1"></i> Share
            </button>
            <button onclick="bookmarkCourse({{ $course->id }})" 
                    class="flex-1 bg-gray-100 text-gray-700 py-2 rounded-lg hover:bg-gray-200 transition text-sm font-medium">
                <i class="far fa-bookmark mr-1"></i> Save
            </button>
        </div>
    </div>

    <!-- Training Benefits -->
    <div class="p-6">
        <h4 class="font-semibold text-gray-900 mb-3">This course includes:</h4>
        <ul class="space-y-2 text-sm text-gray-600">
            <li class="flex items-center">
                <i class="fas fa-check text-green-500 mr-2 w-4"></i>
                {{ $course->duration ?? '10' }} hours on-demand video
            </li>
            <li class="flex items-center">
                <i class="fas fa-check text-green-500 mr-2 w-4"></i>
                {{ $course->modules->sum(fn($m) => $m->attachments->whereIn('file_type', ['pdf', 'doc', 'docx'])->count()) }} downloadable resources
            </li>
            <li class="flex items-center">
                <i class="fas fa-check text-green-500 mr-2 w-4"></i>
                Access on mobile and TV
            </li>
            <li class="flex items-center">
                <i class="fas fa-check text-green-500 mr-2 w-4"></i>
                Certificate of completion
            </li>
            <li class="flex items-center">
                <i class="fas fa-check text-green-500 mr-2 w-4"></i>
                Lifetime access
            </li>
        </ul>
    </div>
</div>
<div class="bg-white rounded-lg shadow-lg p-6 mb-6">
    <div class="flex items-start space-x-4">
        <!-- Instructor Avatar -->
        <div class="flex-shrink-0">
            @if(!empty($course->instructor->profile_path))
                <img 
                    src="{{ asset('storage/' . $course->instructor->profile_path) }}" 
                    alt="{{ $course->instructor->name }}" 
                    class="w-16 h-16 rounded-full object-cover border-2 border-indigo-100 shadow-md"
                >
            @else
                <div class="w-16 h-16 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full flex items-center justify-center shadow-md">
                    <span class="text-white font-semibold text-lg">
                        {{ strtoupper(substr($course->instructor->name ?? 'I', 0, 1)) }}
                    </span>
                </div>
            @endif
        </div>
        
        <!-- Instructor Info -->
        <div class="flex-1">
            <h3 class="text-lg font-semibold text-gray-900 mb-1"> 
                {{ $course->instructor->name ?? 'Course Instructor' }}
            </h3>
            
            <!-- Instructor Title -->
            <p class="text-gray-600 text-sm mb-2">
                {{ $course->instructor->headline ?? $course->instructor->bio ?? 'Professional Instructor' }}
            </p>
            
            <!-- Rating -->
            <div class="flex items-center mb-3">
                <div class="flex items-center">
                    @php
                        $instructorRating = $course->instructor->average_rating ?? $course->instructor->rating ?? 4.5;
                        $totalReviews = $course->instructor->total_reviews ?? 1250;
                    @endphp
                    
                    @for($i = 1; $i <= 5; $i++)
                        @if($i <= floor($instructorRating))
                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                        @elseif($i - 0.5 <= $instructorRating)
                            <i class="fas fa-star-half-alt text-yellow-400 text-sm"></i>
                        @else
                            <i class="far fa-star text-yellow-400 text-sm"></i>
                        @endif
                    @endfor
                    
                    <span class="ml-1 text-sm font-semibold text-gray-700">{{ number_format($instructorRating, 1) }}</span>
                    <span class="mx-1 text-gray-400">•</span>
                    <span class="text-sm text-gray-600">{{ $totalReviews }} reviews</span>
                </div>
            </div>
            
            <!-- Instructor Stats -->
            <div class="grid grid-cols-3 gap-4 text-center mb-4">
                <div>
                    <div class="text-lg font-bold text-indigo-600">
                        {{ $course->instructor->total_students ?? '10K+' }}
                    </div>
                    <div class="text-xs text-gray-500">Students</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-indigo-600">
                        {{ $course->instructor->total_courses ?? $course->instructor->courses_count ?? '15' }}
                    </div>
                    <div class="text-xs text-gray-500">Courses</div>
                </div>
                <div>
                    <div class="text-lg font-bold text-indigo-600">
                        {{ $course->instructor->total_reviews ?? '1.2K' }}
                    </div>
                    <div class="text-xs text-gray-500">Reviews</div>
                </div>
            </div>
            
            <!-- Instructor Bio -->
            @if($course->instructor->bio ?? false)
                <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                    {{ Str::limit($course->instructor->bio, 150) }}
                </p>
            @endif
          
        @if($course->instructor && $course->instructor->id)
            <a href="{{ route('instructor.profile', $course->instructor->id) }}" 
            class="w-full bg-indigo-50 text-indigo-700 py-3 rounded-lg hover:bg-indigo-100 transition-all duration-200 text-sm font-semibold flex items-center justify-center space-x-2 group border border-indigo-200 hover:border-indigo-300">
                <i class="fas fa-user-circle text-indigo-500"></i>
                <span>View Instructor Profile</span>
                <i class="fas fa-arrow-right text-xs transform group-hover:translate-x-1 transition-transform duration-200"></i>
            </a>
        @else
            <div class="w-full bg-gray-100 text-gray-500 py-3 rounded-lg text-sm font-semibold flex items-center justify-center space-x-2 border border-gray-200">
                <i class="fas fa-user-slash text-gray-400"></i>
                <span>Instructor Profile Unavailable</span>
            </div>
        @endif
        </div>
    </div>
</div>
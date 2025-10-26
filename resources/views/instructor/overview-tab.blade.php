@if($recentCourses->count() > 0)
    <div class="space-y-4">
        <h4 class="font-semibold text-gray-900">Recent Course Activity</h4>
        @foreach($recentCourses as $course)
        <div class="flex items-center space-x-4 p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors duration-200">
            @if($course->thumbnail)
                <img src="{{ asset('storage/' . $course->thumbnail) }}" 
                     alt="{{ $course->title }}" 
                     class="w-16 h-16 rounded-lg object-cover">
            @else
                <div class="w-16 h-16 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-lg flex items-center justify-center">
                    <i class="fas fa-book text-white text-xl"></i>
                </div>
            @endif
            <div class="flex-1">
                <h5 class="font-semibold text-gray-900">{{ $course->title }}</h5>
                <p class="text-sm text-gray-600">Published {{ $course->created_at->diffForHumans() }}</p>
                <div class="flex items-center space-x-4 mt-1">
                    <span class="text-xs text-gray-500">
                        <i class="fas fa-users mr-1"></i>
                        {{ $course->user_courses_count }} students
                    </span>
                    @if($course->reviews_avg_rating)
                        <span class="text-xs text-gray-500">
                            <i class="fas fa-star text-yellow-400 mr-1"></i>
                            {{ number_format($course->reviews_avg_rating, 1) }}
                        </span>
                    @endif
                </div>
            </div>
            <a href="{{ route('courses.show', $course->id) }}" 
               class="bg-indigo-600 text-white px-3 py-1 rounded text-xs font-medium hover:bg-indigo-700 transition-colors">
                View
            </a>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center text-gray-500 py-8">
        <i class="fas fa-book-open text-4xl mb-4 text-gray-300"></i>
        <p>No recent course activity.</p>
    </div>
@endif
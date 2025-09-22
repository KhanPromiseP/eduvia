<div class="bg-white rounded-lg shadow-md p-4 mb-6 sticky top-4">
    <h2 class="text-lg font-bold text-gray-800 mb-3 flex items-center">
        <i class="fas fa-graduation-cap mr-2 text-indigo-600"></i> My Courses
    </h2>
    
    @if($purchasedCourses->count() > 0)
        <div class="space-y-2 max-h-96 overflow-y-auto">
            @foreach($purchasedCourses as $course)
                @php 
                    $progress = $course->progressPercentage(Auth::id());
                @endphp

                <a href="{{ route('userdashboard', ['course' => $course->id]) }}" 
                   class="block p-2 rounded border hover:bg-indigo-50 hover:border-indigo-300 transition text-sm 
                          {{ isset($selectedCourse) && $selectedCourse->id == $course->id 
                                ? 'bg-indigo-100 border-indigo-300' 
                                : 'bg-gray-50' }}">
                    <div class="flex items-center">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" 
                                 alt="{{ $course->title }}" 
                                 class="w-8 h-8 rounded object-cover mr-2">
                        @else
                            <div class="w-8 h-8 rounded bg-indigo-200 flex items-center justify-center mr-2">
                                <i class="fas fa-book text-indigo-600 text-xs"></i>
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-gray-800 truncate">{{ $course->title }}</h3>
                            <p class="text-xs text-gray-600">{{ $course->modules->count() }} modules</p>

                            <!-- Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-2 mt-2 relative">
                                <div class="bg-green-600 h-2 rounded-full" style="width: {{ $progress }}%"></div>
                                <span class="absolute inset-0 flex items-center justify-center text-[10px] font-medium text-gray-700">
                                    {{ $progress }}%
                                </span>
                            </div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    @else
        <div class="text-center py-4">
            <i class="fas fa-book-open text-2xl text-gray-300 mb-2"></i>
            <p class="text-gray-500 text-sm">No courses purchased yet.</p>
            <a href="{{ route('courses.index') }}" class="text-indigo-600 hover:text-indigo-800 mt-1 inline-block text-xs">
                Browse Courses
            </a>
        </div>
    @endif
    
    <!-- Study Tools Section -->
    <div class="mt-6 pt-4 border-t border-gray-200">
        <h3 class="text-sm font-semibold text-gray-800 mb-3 flex items-center">
            <i class="fas fa-tools mr-2 text-blue-600"></i> Study Tools
        </h3>
        <div class="space-y-2">
            <button onclick="openStudyTools()" 
                    class="w-full text-left p-2 rounded bg-blue-50 hover:bg-blue-100 transition text-sm text-blue-800">
                <i class="fas fa-sticky-note mr-2"></i> Notes
            </button>
            <button onclick="openBookmarkManager()" 
                    class="w-full text-left p-2 rounded bg-blue-50 hover:bg-blue-100 transition text-sm text-blue-800">
                <i class="fas fa-bookmark mr-2"></i> Bookmarks
            </button>
            <button onclick="openFlashcards()" 
                    class="w-full text-left p-2 rounded bg-blue-50 hover:bg-blue-100 transition text-sm text-blue-800">
                <i class="fas fa-layer-group mr-2"></i> Flashcards
            </button>
        </div>
    </div>
    
    <!-- Back to Dashboard Button -->
    @if(isset($selectedCourse))
        <div class="mt-4 pt-3 border-t border-gray-200">
            <a href="{{ route('userdashboard') }}" 
               class="flex items-center text-sm text-gray-600 hover:text-gray-800">
                <i class="fas fa-arrow-left mr-1"></i> Back to All Courses
            </a>
        </div>
    @endif
</div>

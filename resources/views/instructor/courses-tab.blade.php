@if($courses->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300">
            @if($course->image) {{-- Changed from thumbnail to image --}}
                <img src="{{ asset('storage/' . $course->image) }}" 
                     alt="{{ $course->title }}" 
                     class="w-full h-48 object-cover">
            @else
                <div class="w-full h-48 bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                    <i class="fas fa-book-open text-white text-4xl"></i>
                </div>
            @endif
            <div class="p-4">
                <div class="flex items-start justify-between mb-2">
                    <h4 class="font-bold text-gray-900 line-clamp-2 flex-1">{{ $course->title }}</h4>
                </div>
                
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                
                @if($course->category)
                    <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium mb-3">
                        {{ $course->category->name }}
                    </span>
                @endif
                
                <div class="flex justify-between items-center mb-3">
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-star text-yellow-400 text-sm"></i>
                        <span class="text-sm font-semibold">{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</span>
                        <span class="text-gray-400 text-sm">({{ $course->reviews_count ?? 0 }})</span>
                    </div>
                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                        {{ $course->enrollments_count }} students
                    </span>
                </div>
                
                <div class="flex justify-between items-center">
                    <span class="text-lg font-bold text-indigo-600">
                        @if($course->price > 0)
                            ${{ number_format($course->price, 2) }}
                        @else
                            Free
                        @endif
                    </span>
                    <a href="{{ route('courses.show', $course->id) }}" 
                       class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors duration-200">
                        View Course
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
@else
    <div class="text-center text-gray-500 py-12">
        <i class="fas fa-book-open text-6xl mb-4 text-gray-300"></i>
        <h4 class="text-lg font-semibold text-gray-600 mb-2">No Courses Published</h4>
        <p class="text-gray-500">This instructor hasn't published any courses yet.</p>
    </div>
@endif
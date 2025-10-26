<div class="bg-white rounded-xl shadow-lg p-6">
    <!-- Instructor Courses Section -->
    <div class="mb-8">
        <div class="flex items-center mb-6">
            <div class="w-1 h-6 bg-indigo-600 rounded-full mr-3"></div>
            <h3 class="text-xl font-bold text-gray-900">More from this instructor</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $instructorCourses = \App\Models\Course::where('instructor_id', $course->instructor_id)
                    ->where('id', '!=', $course->id)
                    ->where('is_published', true)
                    ->withCount(['reviews as average_rating' => function($query) {
                        $query->select(\DB::raw('COALESCE(AVG(rating), 0)'));
                    }])
                    ->withCount('reviews')
                    ->limit(3)
                    ->get();
            @endphp
            
            @forelse($instructorCourses as $relatedCourse)
                <div class="course-card bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="relative h-32 bg-gradient-to-r from-indigo-500 to-purple-600 overflow-hidden">
                        @if($relatedCourse->image)
                            <img src="{{ asset('storage/' . $relatedCourse->image) }}" 
                                 alt="{{ $relatedCourse->title }}" 
                                 class="w-full h-full object-cover course-image">
                        @else
                            <div class="w-full h-full bg-indigo-100 flex items-center justify-center">
                                <i class="fas fa-book text-indigo-600 text-2xl"></i>
                            </div>
                        @endif
                        <div class="absolute top-2 left-2">
                            <span class="text-xs font-medium text-white bg-indigo-800/80 px-2 py-1 rounded-full">
                                Instructor 
                            </span>
                        </div>
                        <div class="absolute bottom-2 right-2">
                            <span class="text-white text-xs font-medium bg-black/30 px-2 py-1 rounded-full">
                                <i class="fas fa-clock mr-1"></i> 
                                {{ $relatedCourse->duration ?? '5h 30m' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 text-sm mb-2 line-clamp-2 hover:text-indigo-600 transition-colors">
                            {{ $relatedCourse->title }}
                        </h4>
                        <p class="text-gray-600 text-xs mb-3 line-clamp-2">
                            {{ $relatedCourse->short_description ?? 'Explore this comprehensive course to enhance your skills and knowledge in this field.' }}
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                @php 
                                    $rating = $relatedCourse->average_rating ?? 0;
                                    $totalReviews = $relatedCourse->reviews_count ?? 0;
                                @endphp
                                <div class="flex text-yellow-400 text-xs mr-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($rating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $rating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ number_format($rating, 1) }} 
                                    @if($totalReviews > 0)
                                        <span class="text-gray-400">({{ $totalReviews }})</span>
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center gap-2">
                               <div class="relative group w-max">
                                <!-- Icon-only button -->
                                <a href="{{ route('courses.show', $relatedCourse) }}" 
                                class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-50 text-indigo-700 text-lg hover:bg-indigo-100 transition-all duration-300 shadow-md">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <!-- Fixed tooltip -->
                                <div class="absolute -top-10 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 bg-gray-700 border border-gray-200 rounded-lg shadow-lg px-3 py-1 z-50 text-xs text-white pointer-events-none">
                                    View Course
                                </div>
                            </div>

                                <span class="font-bold text-indigo-600 text-sm">
                                    ${{ $relatedCourse->is_premium ? number_format($relatedCourse->price, 2) : 'Free' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8">
                    <i class="fas fa-book-open text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">No other courses from this instructor yet.</p>
                </div>
            @endforelse
        </div>
    </div>
    
    <!-- Category Courses Section -->
@if($course->category)
    <div class="mt-10">
        <div class="flex items-center mt-8 mb-6">
            <div class="w-1 h-6 bg-green-600 rounded-full mr-3"></div>
            <h3 class="text-xl font-bold text-gray-900">Related Courses you will love to explore</h3>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @php
                $categoryCourses = \App\Models\Course::where('category_id', $course->category_id)
                    ->where('id', '!=', $course->id)
                    ->where('is_published', true)
                    ->withCount(['reviews as average_rating' => function($query) {
                        $query->select(\DB::raw('COALESCE(AVG(rating), 0)'));
                    }])
                    ->withCount('reviews')
                    ->limit(3)
                    ->get();
            @endphp
            
            @forelse($categoryCourses as $categoryCourse)
                <div class="course-card bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-md transition-all duration-300">
                    <div class="relative h-32 bg-gradient-to-r from-green-500 to-emerald-600 overflow-hidden">
                        @if($categoryCourse->image)
                            <img src="{{ asset('storage/' . $categoryCourse->image) }}" 
                                 alt="{{ $categoryCourse->title }}" 
                                 class="w-full h-full object-cover course-image">
                        @else
                            <div class="w-full h-full bg-green-100 flex items-center justify-center">
                                <i class="fas fa-book text-green-600 text-2xl"></i>
                            </div>
                        @endif
                        <div class="absolute top-2 left-2">
                            <span class="text-xs font-medium text-white bg-green-800/80 px-2 py-1 rounded-full">
                                Related
                            </span>
                        </div>
                        <div class="absolute bottom-2 right-2">
                            <span class="text-white text-xs font-medium bg-black/30 px-2 py-1 rounded-full">
                                <i class="fas fa-clock mr-1"></i> 
                                {{ $categoryCourse->duration ?? '6h 15m' }}
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        <h4 class="font-bold text-gray-900 text-sm mb-2 line-clamp-2 hover:text-green-600 transition-colors">
                            {{ $categoryCourse->title }}
                        </h4>
                        <p class="text-gray-600 text-xs mb-3 line-clamp-2">
                            {{ $categoryCourse->short_description ?? 'Dive into this topic with expert guidance and practical examples to master key concepts.' }}
                        </p>
                        <div class="flex justify-between items-center">
                            <div class="flex items-center">
                                @php 
                                    $rating = $categoryCourse->average_rating ?? 0;
                                    $totalReviews = $categoryCourse->reviews_count ?? 0;
                                @endphp
                                <div class="flex text-yellow-400 text-xs mr-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($rating))
                                            <i class="fas fa-star"></i>
                                        @elseif($i - 0.5 <= $rating)
                                            <i class="fas fa-star-half-alt"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                                <span class="text-xs text-gray-500">
                                    {{ number_format($rating, 1) }} 
                                    @if($totalReviews > 0)
                                        <span class="text-gray-400">({{ $totalReviews }})</span>
                                    @endif
                                </span>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <div class="relative group w-max">
                                    <a href="{{ route('courses.show', $categoryCourse) }}" 
                                       class="flex items-center justify-center w-10 h-10 rounded-full bg-indigo-50 text-indigo-700 text-lg hover:bg-indigo-100 transition-all duration-300 shadow-md">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    <!-- Fixed tooltip -->
                                    <div class="absolute -top-10 transform -translate-x-1/2 opacity-0 group-hover:opacity-100 transition-all duration-300 bg-gray-700 border border-gray-200 rounded-lg shadow-lg px-3 py-1 z-50 text-xs text-white pointer-events-none">
                                        View Course
                                    </div>
                                </div>

                                <span class="font-bold text-green-600 text-sm">
                                    ${{ $categoryCourse->is_premium ? number_format($categoryCourse->price, 2) : 'Free' }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-8">
                    <i class="fas fa-layer-group text-gray-300 text-4xl mb-3"></i>
                    <p class="text-gray-500">No related courses in this category yet.</p>
                </div>
            @endforelse
        </div>
    </div>
@endif
</div>

<style>
.course-card {
    transition: all 0.3s ease;
}
.course-card:hover {
    transform: translateY(-5px);
}
.course-image {
    transition: transform 0.5s ease;
}
.course-card:hover .course-image {
    transform: scale(1.05);
}
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
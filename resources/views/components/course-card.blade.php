<div class="course-card group relative bg-white rounded-xl overflow-hidden border border-gray-200 hover:border-gray-300 transition-all duration-300 shadow-sm hover:shadow-lg"
    data-level="{{ $course->level }}" 
    data-price="{{ $course->price == 0 ? 'free' : ($course->is_premium ? 'premium' : 'paid') }}"
>

    <!-- Course Image -->
    <div class="relative h-48 overflow-hidden bg-gray-100">
        @if($course->image)
            <img src="{{ asset('storage/' . $course->image) }}" 
                 alt="{{ $course->title }}" 
                 class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105">
        @else
            <div class="w-full h-full flex items-center justify-center bg-gray-50">
                <i class="fas fa-book-open text-3xl text-gray-400"></i>
            </div>
        @endif

        <!-- Top Badges -->
        <div class="absolute top-3 left-3 flex flex-col gap-2">
            <!-- Level Badge -->
            <span class="bg-gray-900 text-white px-3 py-1.5 rounded text-xs font-medium">
                @if($course->level == 1) Beginner
                @elseif($course->level == 2) Intermediate
                @elseif($course->level == 3) Advanced
                @elseif($course->level == 4) Expert
                @else All Levels
                @endif
            </span>

            <!-- Premium Badge -->
            @if($course->is_premium)
                <span class="bg-yellow-500 text-gray-900 px-3 py-1.5 rounded text-xs font-medium">
                    <i class="fas fa-star mr-1"></i>Premium
                </span>
            @endif
        </div>

        <!-- Enrollment Status -->
        @if(auth()->check() && auth()->user()->hasPurchased($course))
            <div class="absolute bottom-3 left-3 right-3">
                <div class="bg-white/95 rounded-lg p-3 shadow-sm">
                    <div class="flex justify-between text-xs font-medium text-gray-700 mb-2">
                        <span>Your Progress</span>
                        <span>{{ auth()->user()->getCourseProgress($course) }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                        <div class="bg-green-500 h-1.5 rounded-full transition-all duration-500" 
                             style="width: {{ auth()->user()->getCourseProgress($course) }}%"></div>
                    </div>
                </div>
            </div>
        @else
            <span class="absolute bottom-3 right-3 bg-black/70 text-white px-2 py-1 rounded text-xs font-medium">
                {{ number_format($course->total_enrollments) }} enrolled
            </span>

        @endif
    </div>

    <!-- Course Content -->
    <div class="p-5">
        <!-- Category -->
        <div class="flex items-center gap-2 mb-3">
            <span class="text-xs font-medium text-blue-600 uppercase tracking-wide">
                {{ $course->category?->name ?? 'General' }}
            </span>
        </div>

        <!-- Course Title -->
        <h3 class="font-bold text-lg mb-3 text-gray-900 leading-tight line-clamp-2 group-hover:text-blue-700 transition-colors">
            {{ $course->title }}
        </h3>

        <!-- Course Description -->
        <p class="text-gray-600 text-sm mb-4 leading-relaxed line-clamp-2">
            {{ $course->short_description ?? Str::limit($course->description, 100) }}
        </p>

        <!-- Instructor & Rating -->
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full overflow-hidden bg-gray-200">
                    @if(!empty($course->instructor->profile_path))
                        <img 
                            src="{{ asset('storage/' . $course->instructor->profile_path) }}" 
                            alt="{{ $course->instructor->name }}" 
                            class="w-full h-full object-cover"
                        >
                    @else
                        <div class="w-full h-full bg-gray-300 flex items-center justify-center text-gray-600 text-xs font-medium">
                            {{ strtoupper(substr($course->instructor->name ?? 'I', 0, 1)) }}
                        </div>
                    @endif
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ $course->instructor->name ?? 'Instructor' }}</p>
                </div>
            </div>

            <!-- Rating -->
            <div class="text-right">
                @php
                    $rating = $course->average_rating ?? 0;
                    $ratingCount = $course->total_reviews ?? 0;
                @endphp
                <div class="flex items-center gap-1 mb-1">
                    <div class="flex text-yellow-400 text-sm">
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
                    <span class="text-sm font-bold text-gray-900">{{ number_format($rating, 1) }}</span>
                </div>
                <span class="text-xs text-gray-500">{{ $ratingCount }} reviews</span>
            </div>
        </div>
   

        <!-- Course Features -->
        <div class="flex items-center gap-3 mb-4 text-xs text-gray-600">
            <span class="flex items-center gap-1">
                <i class="fas fa-clock"></i>
                {{ $course->duration ?? '6h 30m' }}
            </span>
            <span class="flex items-center gap-1">
                <i class="fas fa-play-circle"></i>
                {{ $course->modules->sum(fn($module) => $module->attachments->count()) }} lectures
            </span>
            @if($course->features && is_array($course->features) && count($course->features) > 0)
                <span class="flex items-center gap-1">
                    <i class="fas fa-check"></i>
                    {{ count($course->features) }} features
                </span>
            @endif
        </div>

        

        <!-- Price & Actions -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <!-- Price -->
            <div class="flex items-center gap-2">
                @if($course->is_premium)
                    <span class="text-xl font-bold text-gray-900">${{ number_format($course->price, 2) }}</span>
                    @if($course->original_price && $course->original_price > $course->price)
                        <span class="text-sm text-gray-500 line-through">${{ number_format($course->original_price, 2) }}</span>
                        <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-medium">
                            -{{ round((($course->original_price - $course->price) / $course->original_price) * 100) }}%
                        </span>
                    @endif
                @else
                    <span class="text-xl font-bold text-gray-900">Free</span>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="flex gap-2">
                <a href="{{ route('courses.show', $course) }}" 
                   class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors flex items-center gap-2">
                    <i class="fas fa-eye text-xs"></i>
                    View
                </a>

                @if(auth()->check() && auth()->user()->hasPurchased($course))
                    <a href="{{ route('userdashboard', ['course' => $course->id]) }}" 
                       class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-medium hover:bg-green-700 transition-colors flex items-center gap-2">
                        <i class="fas fa-play text-xs"></i>
                        Continue
                    </a>
                @else
                    @if(!$course->is_premium)
                        <a href="{{ route('quick.purchase', $course) }}" 
                           class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                            <i class="fas fa-rocket text-xs"></i>
                            Enroll
                        </a>
                    @else
                        <form action="{{ route('payment.initiate', $course) }}" method="POST" class="m-0">
                            @csrf
                            <button type="submit" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <i class="fas fa-shopping-cart text-xs"></i>
                                Enroll
                            </button>
                        </form>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>

<style>
.course-card {
    transition: all 0.3s ease;
}

.course-card:hover {
    transform: translateY(-2px);
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* Professional shadow system */
.shadow-sm {
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}

.shadow-lg {
    box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}

/* Smooth transitions */
* {
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
}
</style>
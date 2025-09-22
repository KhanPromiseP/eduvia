<div class="course-card border rounded-lg overflow-hidden hover:shadow-xl transition bg-white"
     data-level="{{ $course->level }}" 
     data-price="{{ $course->price == 0 ? 'free' : ($course->is_premium ? 'premium' : 'paid') }}"
     data-category="{{ $course->category?->id ?? '' }}">

    <div class="relative h-48 bg-gray-200">
        @if($course->image)
            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
        @else
            <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                <i class="fas fa-book text-4xl text-indigo-600"></i>
            </div>
        @endif

        {{-- Level Badge --}}
        <span class="absolute top-2 left-2 bg-indigo-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
            @if($course->level == 1) Beginner
            @elseif($course->level == 2) Intermediate
            @elseif($course->level == 3) Advanced
            @elseif($course->level == 4) Expert
            @else Beginner to Advanced
            @endif
        </span>

        {{-- Premium Badge --}}
        @if($course->is_premium)
            <span class="absolute top-10 left-2 bg-yellow-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                Premium
            </span>
        @endif

        {{-- Enrolled Badge --}}
        @if(auth()->check() && auth()->user()->hasPurchased($course))
            <span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                <i class="fas fa-check-circle mr-1"></i> Enrolled
            </span>
        @endif
    </div>

    <div class="p-4 flex flex-col justify-between h-56">
        <div>
            <h3 class="font-bold text-xl mb-2 text-gray-800">{{ $course->title }}</h3>
            <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ Str::limit($course->description, 120) }}</p>
        </div>

        <div class="flex justify-between items-center mt-auto gap-2">
            {{-- Show "Free" if not premium, else show price --}}
            <span class="text-indigo-600 font-bold text-lg">
                @if($course->is_premium)
                    ${{ number_format($course->price, 2) }}
                @else
                    Free
                @endif
            </span>

            <div class="flex gap-2">
                <a href="{{ route('courses.show', $course) }}" class="bg-gray-800 text-white px-3 py-2 rounded-lg font-medium hover:bg-gray-900 transition">
                    View Details
                </a>

                @if(auth()->check() && auth()->user()->hasPurchased($course))
                    <a href="{{ route('userdashboard', ['course' => $course->id]) }}" class="bg-green-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-green-700 transition flex items-center">
                        <i class="fas fa-check-circle mr-2"></i> Access
                    </a>
                @else
                   

                    @if(!$course->is_premium)
                       
                        <a href="{{ route('quick.purchase', $course) }}" 
                            class="bg-indigo-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                Enroll Now
                        </a>
                    @else
                       
                        <form action="{{ route('payment.initiate', $course) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                Enroll Now
                            </button>
                        </form>
                    @endif
                    
                @endif
            </div>
        </div>
    </div>
</div>

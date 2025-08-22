@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    {{-- Hero Banner --}}
    <div class="relative bg-indigo-600 rounded-lg overflow-hidden mb-8">
        <img src="{{ asset('images/hero-bg.jpg') }}" alt="Hero Banner" class="absolute inset-0 w-full h-full object-cover opacity-30">
        <div class="relative z-10 px-6 py-16 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Explore Our Courses</h1>
            <p class="text-indigo-100 text-lg mb-6">Learn new skills, improve yourself, and advance your career.</p>
            <a href="#filters" class="inline-block bg-white text-indigo-600 font-semibold px-6 py-3 rounded-lg hover:bg-indigo-100 transition">
                Browse Courses
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div id="filters" class="mb-6 flex flex-wrap items-center gap-4">
        <select id="levelFilter" class="border rounded px-3 py-2 text-gray-700">
            <option value="">All Levels</option>
            <option value="1">Beginner</option>
            <option value="2">Intermediate</option>
            <option value="3">Advanced</option>
        </select>

        <select id="priceFilter" class="border rounded px-3 py-2 text-gray-700">
            <option value="">All Prices</option>
            <option value="free">Free</option>
            <option value="paid">Paid</option>
        </select>

        <input type="text" id="searchInput" placeholder="Search courses..." class="border rounded px-3 py-2 text-gray-700 flex-grow">
    </div>

    {{-- Courses Grid --}}
    <div id="coursesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        <div class="course-card border rounded-lg overflow-hidden hover:shadow-xl transition bg-white" 
             data-level="{{ $course->level }}" data-price="{{ $course->price == 0 ? 'free' : 'paid' }}">

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
                    @else Advanced
                    @endif
                </span>
            </div>

            {{-- Course Content --}}
            <div class="p-4 flex flex-col justify-between h-56">
                <div>
                    <h3 class="font-bold text-xl mb-2 text-gray-800">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ Str::limit($course->description, 120) }}</p>
                </div>

                {{-- Price + Buttons --}}
                <div class="flex justify-between items-center mt-auto gap-2">
                    <span class="text-indigo-600 font-bold text-lg">${{ number_format($course->price, 2) }}</span>
                    <div class="flex gap-2">
                        <a href="{{ route('courses.show', $course) }}" class="bg-gray-800 text-white px-3 py-2 rounded-lg font-medium hover:bg-gray-900 transition">
                            View Details
                        </a>
                        <form action="{{ route('purchase.course', $course) }}" method="POST" class="flex-shrink-0">
                            @csrf
                            <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                Enroll Now
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
function filterCourses() {
    const levelFilter = document.querySelector('#levelFilter').value;
    const priceFilter = document.querySelector('#priceFilter').value;
    const searchQuery = document.querySelector('#searchInput').value.toLowerCase();

    document.querySelectorAll('#coursesGrid .course-card').forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const level = card.dataset.level;
        const price = card.dataset.price;

        let visible = true;

        if (levelFilter && levelFilter !== level) visible = false;
        if (priceFilter && priceFilter !== price) visible = false;
        if (searchQuery && !title.includes(searchQuery)) visible = false;

        card.style.display = visible ? 'block' : 'none';
    });
}

// Event listeners
document.querySelector('#levelFilter').addEventListener('change', filterCourses);
document.querySelector('#priceFilter').addEventListener('change', filterCourses);
document.querySelector('#searchInput').addEventListener('keyup', filterCourses);
</script>
@endsection

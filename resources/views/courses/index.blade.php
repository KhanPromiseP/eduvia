@extends('layouts.app')

@section('content')
<div class="max-w-9xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

   
    <div class="relative bg-indigo-600 rounded-lg overflow-hidden mb-8">
      
        <div class="relative z-10 px-6 py-16 text-center">
            <h1 class="text-4xl font-bold text-white mb-4">Explore Our Courses</h1>
            <p class="text-indigo-100 text-lg mb-6">Learn new skills, improve yourself, and advance your career.</p>
            <a href="#filters" class="inline-block bg-white text-indigo-600 font-semibold px-6 py-3 rounded-lg hover:bg-indigo-100 transition">
                Browse Courses
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div id="filters" class="max-w-7xl mx-auto mb-6 flex flex-wrap items-center gap-4">
        <select id="levelFilter" class="border rounded px-3 py-2 text-gray-700">
            <option value="">All Levels</option>
            <option value="1">Beginner</option>
            <option value="2">Intermediate</option>
            <option value="3">Advanced</option>
            <option value="4">Expart</option>
            <option value="5">Beginner to Advanced</option>
        </select>

        <select id="priceFilter" class="border rounded px-3 py-2 text-gray-700">
            <option value="">All Prices</option>
            {{-- <option value="free">Free</option> --}}
            <option value="paid">Free</option>
            <option value="premium">premium</option>
        </select>
        
        <select id="categoryFilter" class="border rounded px-3 py-2 text-gray-700">
            <option value="">All Categories</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}">{{ $category->name }}</option>
            @endforeach
        </select>


        <input type="text" id="searchInput" placeholder="Search courses..." class="border rounded px-3 py-2 text-gray-700 flex-grow">
    </div>

    {{-- Courses Grid --}}
     {{-- Courses Grid --}}
        <div id="coursesGrid" class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($courses as $course)
                @include('components.course-card', ['course' => $course])
            @endforeach
        </div>

</div>

<script>
function filterCourses() {
    const levelFilter = document.querySelector('#levelFilter').value;
    const priceFilter = document.querySelector('#priceFilter').value;
    const categoryFilter = document.querySelector('#categoryFilter').value;
    const searchQuery = document.querySelector('#searchInput').value.toLowerCase();

    document.querySelectorAll('#coursesGrid .course-card').forEach(card => {
        const title = card.querySelector('h3').textContent.toLowerCase();
        const level = card.dataset.level;
        const price = card.dataset.price;
        const category = card.dataset.category;

        let visible = true;

        if (levelFilter && levelFilter !== level) visible = false;
        if (priceFilter && priceFilter !== price) visible = false;
        if (categoryFilter && categoryFilter !== category) visible = false;
        if (searchQuery && !title.includes(searchQuery)) visible = false;

        card.style.display = visible ? 'block' : 'none';
    });
}

// Event listeners
document.querySelector('#levelFilter').addEventListener('change', filterCourses);
document.querySelector('#priceFilter').addEventListener('change', filterCourses);
document.querySelector('#categoryFilter').addEventListener('change', filterCourses);
document.querySelector('#searchInput').addEventListener('keyup', filterCourses);

</script>
@endsection

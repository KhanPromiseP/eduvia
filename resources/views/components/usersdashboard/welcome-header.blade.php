<div class="bg-white rounded-lg shadow-md p-6 mb-8">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
            <p class="text-gray-600">Continue your learning journey with your enrolled courses.</p>
        </div>
        
        <div class="mt-4 md:mt-0 flex flex-wrap gap-2">
            <div class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm">
                <i class="fas fa-book-open mr-1"></i>
                <span>{{ $purchasedCourses->count() }} Courses</span>
            </div>
            
            @if(isset($selectedCourse))
            <div class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm">
                <i class="fas fa-play-circle mr-1"></i>
                <span>Learning: {{ Str::limit($selectedCourse->title, 15) }}</span>
            </div>
            @endif
            
            <!-- Study Tools Quick Access -->
            <div class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                <i class="fas fa-tools mr-1"></i>
                <span>Study Tools</span>
            </div>
        </div>
    </div>
</div>
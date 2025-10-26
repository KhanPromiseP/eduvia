<div class="bg-gradient-to-r from-blue-50 via-pink-50 to-white rounded-2xl shadow-lg p-6 mb-6 border border-blue-100 backdrop-blur-md transition-all duration-300 hover:shadow-xl">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
    
    <!-- Welcome Section -->
    <div>
      <h1 class="text-3xl font-extrabold bg-gradient-to-r from-blue-600 via-pink-500 to-purple-600 text-transparent bg-clip-text">
        Welcome back, {{ Auth::user()->name }} 👋
      </h1>
      <p class="text-gray-600 text-sm mt-1">
        Continue your learning journey with your enrolled courses on <span class="font-semibold text-indigo-500">Eduvia</span>.
      </p>
    </div>

    <!-- Quick Info Badges -->
    <div class="flex flex-wrap justify-start md:justify-end gap-3 mt-2 md:mt-0">
      
      <!-- Course Count -->
      <div class="flex items-center gap-2 bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 px-4 py-2 rounded-full text-sm font-medium shadow-sm">
        <i class="fas fa-book-open text-blue-600"></i>
        <span>{{ $purchasedCourses->count() }} Courses</span>
      </div>

      <!-- Current Course -->
      @if(isset($selectedCourse))
      <div class="flex items-center gap-2 bg-gradient-to-r from-pink-100 to-pink-200 text-pink-800 px-4 py-2 rounded-full text-sm font-medium shadow-sm">
        <i class="fas fa-play-circle text-pink-500"></i>
        <span>Learning: {{ Str::limit($selectedCourse->title, 15) }}</span>
      </div>
      @endif

      <!-- Study Tools -->
      <div class="flex items-center gap-2 bg-gradient-to-r from-purple-100 to-blue-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium shadow-sm hover:from-blue-200 hover:to-pink-100 transition">
        <i class="fas fa-tools text-blue-500"></i>
        <span>Study Tools</span>
      </div>
    </div>

  </div>
</div>

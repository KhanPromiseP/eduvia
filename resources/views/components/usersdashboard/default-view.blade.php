<div class="bg-white rounded-lg shadow-md p-8 text-center">
    <!-- Welcome Illustration -->
    <div class="w-24 h-24 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-6">
        <i class="fas fa-graduation-cap text-white text-4xl"></i>
    </div>
    
    <h2 class="text-2xl font-bold text-gray-800 mb-3">Your Learning Dashboard</h2>
    <p class="text-gray-600 mb-6 max-w-md mx-auto">
        Welcome to your personalized learning space! Select a course from the sidebar to begin your educational journey. 
        All your enrolled courses are available here in a secure, optimized environment.
    </p>
    
    @if($purchasedCourses->count() > 0)
    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6 max-w-2xl mx-auto">
        <div class="bg-indigo-50 p-4 rounded-lg border border-indigo-100">
            <div class="text-2xl font-bold text-indigo-600 mb-1">{{ $purchasedCourses->count() }}</div>
            <div class="text-sm text-indigo-800">Courses</div>
        </div>
        
        @php
            $totalModules = $purchasedCourses->sum(fn($course) => $course->modules->count());
            $totalResources = $purchasedCourses->sum(fn($course) => $course->modules->sum(fn($module) => $module->attachments->count()));
        @endphp
        
        <div class="bg-green-50 p-4 rounded-lg border border-green-100">
            <div class="text-2xl font-bold text-green-600 mb-1">{{ $totalModules }}</div>
            <div class="text-sm text-green-800">Modules</div>
        </div>
        
        <div class="bg-blue-50 p-4 rounded-lg border border-blue-100">
            <div class="text-2xl font-bold text-blue-600 mb-1">{{ $totalResources }}</div>
            <div class="text-sm text-blue-800">Resources</div>
        </div>
    </div>

    <!-- Quick Access to Recent Courses -->
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Quick Access</h3>
        <div class="flex flex-wrap justify-center gap-3">
            @foreach($purchasedCourses->take(3) as $course)
            <a href="{{ route('userdashboard', ['course' => $course->id]) }}" 
               class="bg-white border border-gray-200 rounded-lg p-3 hover:shadow-md transition min-w-[200px]">
                <div class="flex items-center mb-2">
                    @if($course->image)
                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" 
                             class="w-10 h-10 rounded object-cover mr-2">
                    @else
                        <div class="w-10 h-10 rounded bg-indigo-100 flex items-center justify-center mr-2">
                            <i class="fas fa-book text-indigo-600"></i>
                        </div>
                    @endif
                    <h4 class="font-semibold text-gray-800 text-sm truncate">{{ Str::limit($course->title, 20) }}</h4>
                </div>
                <div class="text-xs text-gray-500">{{ $course->modules->count() }} modules</div>
            </a>
            @endforeach
        </div>
    </div>

    <p class="text-indigo-600 font-semibold mb-4">
        <i class="fas fa-info-circle mr-2"></i> Select any course to begin your learning journey
    </p>
    
    <!-- Learning Tips -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-left max-w-2xl mx-auto mb-6">
        <div class="flex items-start">
            <i class="fas fa-lightbulb text-yellow-500 text-xl mr-3 mt-1"></i>
            <div>
                <h4 class="font-semibold text-yellow-800 mb-2">Learning Tip</h4>
                <p class="text-yellow-700 text-sm">
                    Use the study tools (notes, bookmarks, flashcards) to enhance your learning experience. 
                    These tools are automatically saved and available across all your devices.
                </p>
            </div>
        </div>
    </div>

    @else
    <!-- Empty State -->
    <div class="bg-gray-50 rounded-lg p-6 mb-6">
        <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-book-open text-gray-400 text-2xl"></i>
        </div>
        <h3 class="text-lg font-semibold text-gray-800 mb-2">No Courses Yet</h3>
        <p class="text-gray-600 mb-4">You haven't purchased any courses yet. Explore our catalog to find courses that match your interests.</p>
        <a href="{{ route('courses.index') }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition inline-block">
            <i class="fas fa-search mr-2"></i> Browse Available Courses
        </a>
    </div>
    @endif

    <!-- Platform Features -->
    <div class="border-t border-gray-200 pt-6 mt-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">Platform Features</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-left max-w-4xl mx-auto">
            <div class="text-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-shield-alt text-blue-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Secure Learning</h4>
                <p class="text-gray-600 text-sm">All content is protected with advanced security measures.</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-mobile-alt text-green-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Mobile Friendly</h4>
                <p class="text-gray-600 text-sm">Learn anywhere, anytime on any device.</p>
            </div>
            
            <div class="text-center">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <i class="fas fa-chart-line text-purple-600 text-xl"></i>
                </div>
                <h4 class="font-semibold text-gray-800 mb-2">Progress Tracking</h4>
                <p class="text-gray-600 text-sm">Monitor your learning progress and achievements.</p>
            </div>
        </div>
    </div>

    <!-- Quick Support -->
    <div class="mt-6 pt-6 border-t border-gray-200">
        <p class="text-gray-600 text-sm mb-3">Need help getting started?</p>
        <div class="flex justify-center space-x-4">
            {{-- <a href="{{ route('help.center') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                <i class="fas fa-question-circle mr-1"></i> Help Center
            </a> --}}
            <a href="{{ route('contact.index') }}" class="text-indigo-600 hover:text-indigo-800 text-sm">
                <i class="fas fa-envelope mr-1"></i> Contact Support
            </a>
        </div>
    </div>
</div>
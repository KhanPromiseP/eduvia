<div class="bg-white rounded-lg shadow-md p-6">
    <!-- Back Button -->
    <div class="mb-4">
        <a href="{{ route('userdashboard') }}" 
           class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm">
            <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="flex flex-col md:flex-row md:items-start gap-6 mb-6">
        <!-- Course Image -->
        @if($selectedCourse->image)
            <img src="{{ asset('storage/' . $selectedCourse->image) }}" alt="{{ $selectedCourse->title }}" 
                 class="w-full md:w-48 h-48 rounded-lg object-cover shadow-md">
        @else
            <div class="w-full md:w-48 h-48 rounded-lg bg-indigo-100 flex items-center justify-center shadow-md">
                <i class="fas fa-book text-indigo-600 text-4xl"></i>
            </div>
        @endif
        
        <!-- Course Details -->
        <div class="flex-1">
            <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $selectedCourse->title }}</h2>
            <p class="text-gray-600 mb-4">{{ $selectedCourse->description }}</p>
            
            <!-- Course Stats -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm mb-4">
                <div class="bg-gray-50 p-3 rounded border">
                    <div class="text-gray-500">Level</div>
                    <div class="font-semibold">
                        @if($selectedCourse->level == 1) Beginner
                        @elseif($selectedCourse->level == 2) Intermediate
                        @else Advanced
                        @endif
                    </div>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <div class="text-gray-500">Modules</div>
                    <div class="font-semibold">{{ $selectedCourse->modules->count() }}</div>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <div class="text-gray-500">Resources</div>
                    <div class="font-semibold">{{ $selectedCourse->modules->sum(fn($module) => $module->attachments->count()) }}</div>
                </div>
                <div class="bg-gray-50 p-3 rounded border">
                    <div class="text-gray-500">Duration</div>
                    <div class="font-semibold">{{ $selectedCourse->duration ? $selectedCourse->duration . ' hours' : 'Self-paced' }}</div>
                </div>
            </div>

            <!-- Progress Bar -->
        @php
            $totalModules = $selectedCourse->modules->count();
            $completedModules = $selectedCourse->modules->filter(function($module) {
                return $module->progress->firstWhere('user_id', auth()->id())?->completed;
            })->count();

            $progressPercentage = $totalModules > 0 
                ? round(($completedModules / $totalModules) * 100) 
                : 0;
        @endphp


        <div class="mb-4">
            <div class="flex justify-between items-center mb-2">
                <span class="text-sm font-medium text-gray-700">Course Progress</span>
                <span class="text-sm text-gray-600">{{ $progressPercentage }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                    style="width: {{ $progressPercentage }}%"></div>
            </div>
        </div>

        </div>
    </div>

    <!-- Course Objectives -->
    @if($selectedCourse->objectives)
    <div class="mb-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-blue-800 mb-3 flex items-center">
            <i class="fas fa-bullseye mr-2"></i> What You'll Learn
        </h3>
        <div class="prose prose-blue max-w-none">
            {!! nl2br(e($selectedCourse->objectives)) !!}
        </div>
    </div>
    @endif

    <!-- Course Modules -->
    <h3 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
        <i class="fas fa-layer-group mr-2 text-indigo-600"></i> Course Curriculum
    </h3>
    
    <div class="space-y-3 mb-6">
        @foreach($selectedCourse->modules as $index => $module)
        <div class="border rounded-lg p-4 hover:shadow-md transition group">
            <div class="flex items-center justify-between">
                <div class="flex items-center flex-1">
                    <!-- Module Number -->
                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-3 flex-shrink-0">
                        {{ $index + 1 }}
                    </div>
                    
                    <!-- Module Info -->
                    <div class="flex-1 min-w-0">
                        <h4 class="font-semibold text-gray-800 text-lg">{{ $module->title }}</h4>
                        @if($module->description)
                            <p class="text-sm text-gray-600 mt-1">{{ $module->description }}</p>
                        @endif
                        
                        <!-- Module Resources -->
                        <div class="flex items-center mt-2 space-x-3 text-xs text-gray-500">
                            <span class="flex items-center">
                                <i class="fas fa-paperclip mr-1"></i>
                                {{ $module->attachments->count() }} resources
                            </span>
                            @if($module->is_free)
                                <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                    Free Preview
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons -->
                <div class="flex items-center space-x-2">
                    <!-- Progress Indicator -->
                   @php
                        $moduleProgress = $module->users()
                            ->where('user_id', auth()->id())
                            ->first();
                    @endphp

                    @if($moduleProgress && $moduleProgress->pivot->completed)
                        <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                            <i class="fas fa-check-circle mr-1"></i> Completed
                        </span>
                    @elseif($moduleProgress)
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">
                            <i class="fas fa-eye mr-1"></i> Viewed
                        </span>
                    @endif

                    
                    <!-- Start Module Button -->
                    <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $module->id]) }}" 
                       class="bg-indigo-600 text-white p-2 rounded hover:bg-indigo-700 transition flex items-center text-sm">
                        <i class="fas fa-play mr-1"></i>
                        Start
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Start Learning Button -->
    @if($selectedCourse->modules->count() > 0)
    <div class="text-center">
        <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $selectedCourse->modules->first()->id]) }}" 
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition inline-flex items-center text-lg font-semibold">
            <i class="fas fa-play-circle mr-2"></i> Start Learning
        </a>
        
        <!-- Quick Actions -->
        <div class="mt-4 flex justify-center space-x-3">
            <button onclick="bookmarkCourse('{{ $selectedCourse->id }}')" 
                    class="text-indigo-600 hover:text-indigo-800 text-sm flex items-center">
                <i class="fas fa-bookmark mr-1"></i> Bookmark Course
            </button>
            <button onclick="shareCourse('{{ $selectedCourse->id }}')" 
                    class="text-gray-600 hover:text-gray-800 text-sm flex items-center">
                <i class="fas fa-share-alt mr-1"></i> Share
            </button>
        </div>
    </div>
    @endif
</div>
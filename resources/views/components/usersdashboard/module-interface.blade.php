<!-- Learning Content Area -->
<div class="{{ isset($selectedCourse)? 'bg-gray-100 rounded-2xl hover:shadow border border-blue-200'  : '' }}">
    @if(isset($selectedCourse) && isset($selectedModule))
        <!-- Module Interface with Enhanced Content Display -->
        <div class="bg-white shadow-md overflow-hidden mb-6 rounded-2xl hover:shadow">
            <!-- Module Header -->
            <div class="bg-gradient-to-r from-blue-50 via-pink-50 to-white rounded-2xl shadow-lg border  border-blue-100 backdrop-blur-md transition-all duration-300 hover:shadow-xl p-4 text-black">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center mb-2">
                            <a href="{{ route('userdashboard', ['course' => $selectedCourse->id]) }}" 
                               class="bg-indigo-500 hover:bg-indigo-300 text-xl p-1 rounded-full h-8 w-8 ml-2 mr-3 transition">
                                <i class="fas fa-arrow-left"></i>
                            </a>
                            <div>
                                <h2 class="text-2xl text-indigo-800 font-bold">{{ $selectedCourse->title }}</h2>
                            </div>
                            <div class="flex rounded items-center">
                            <h3 class="ml-6 font-semibold">
                                <span class="text-black text-xl">Module:</span> 
                                <span class="text-indigo-800 mt-1 text-xl">{{ $selectedModule->title }}</span>
                            </h3>
                        </div>
                        </div>
                        
                    </div>
                       <div class="mt-2 sm:mt-0 mr-2">
                        <span class="bg-gradient-to-r from-purple-300 to-blue-100 px-3 py-1 rounded-2xl text-sm">
                            <i class="fas fa-person mr-2 text-indigo-800"></i>Enjoy Your Learning at Eduvia
                        </span>
                    </div>
                     <div class="mt-2 sm:mt-0 mr-2">
                        <span class="bg-gradient-to-r from-purple-300 to-blue-100 px-3 py-1 rounded-2xl text-sm">
                            <i class="fas fa-book mr-2 text-indigo-800"></i>Eduvia Learning Space
                        </span>
                    </div>
                    <div class="mt-2 sm:mt-0">
                        <span class="bg-gradient-to-r from-purple-300 to-blue-100 px-3 py-1 rounded-2xl text-sm">
                            Module {{ $moduleIndex + 1 }} of {{ $selectedCourse->modules->count() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Module Content -->
            <div class="p-6">
               

                <!-- Main Content Display Area -->
                <div class="mb-6 hover:shadow rounded-2xl border border-blue-100">
                    <!-- Default welcome message or selected content will appear here -->
                    <div id="defaultContent" class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-play-circle text-4xl text-gray-300 mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">Welcome to {{ $selectedModule->title }}</h3>
                        <p class="text-gray-500">Select a resource from the sidebar or bottom pannel to start learning</p>
                    </div>
                    
                    <!-- Active content will be displayed here -->
                    <div id="activeContent" class="hidden">
                        <!-- Content will be dynamically loaded here -->
                    </div>
                </div>

                 @if($selectedModule->description)
                <div class="prose max-w-none mb-6 p-4 bg-gray-50 rounded-lg hover:shadow rounded-2xl border border-blue-100">
                    <p class="text-gray-700">{{ $selectedModule->description }}</p>
                </div>
                @endif

                <!-- Learning Resources Section -->
                @if($selectedModule->attachments->count() > 0)
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-paperclip mr-2 text-indigo-600"></i> 
                            Learning Resources
                            <span class="bg-indigo-100 text-indigo-800 text-xs px-2 py-1 rounded ml-3">
                                {{ $selectedModule->attachments->count() }} files
                            </span>
                        </h3>
                        <div class="flex items-center space-x-2">
                            <button onclick="toggleResourceView()" class="text-sm text-indigo-600 hover:text-indigo-800">
                                <i class="fas fa-th-large mr-1"></i> Change View
                            </button>
                        </div>
                    </div>
                    
                    <div id="resourceGrid" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($selectedModule->attachments as $attachment)
                            @include('components.usersdashboard.attachment-card', ['attachment' => $attachment])
                        @endforeach
                    </div>
                    
                    <div id="resourceList" class="hidden space-y-3">
                        @foreach($selectedModule->attachments as $attachment)
                            @include('components.usersdashboard.attachment-list-item', ['attachment' => $attachment])
                        @endforeach
                    </div>
                </div>
                @else
                <div class="text-center py-8 bg-gray-50 rounded-lg">
                    <i class="fas fa-file-alt text-3xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No learning resources available for this module.</p>
                </div>
                @endif

                <!-- Study Tools Section -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center">
                        <i class="fas fa-tools mr-2 text-blue-600"></i> Study Tools
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <button onclick="takeNotes('{{ $selectedModule->id }}')" class="bg-white border rounded p-3 hover:shadow-md transition">
                            <i class="fas fa-sticky-note text-blue-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium">Take Notes</div>
                        </button>
                        <button onclick="bookmarkModule('{{ $selectedModule->id }}')" class="bg-white border rounded p-3 hover:shadow-md transition">
                            <i class="fas fa-bookmark text-green-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium">Bookmark</div>
                        </button>
                        <button onclick="createFlashcards('{{ $selectedModule->id }}')" class="bg-white border rounded p-3 hover:shadow-md transition">
                            <i class="fas fa-layer-group text-purple-600 text-xl mb-2"></i>
                            <div class="text-sm font-medium">Flashcards</div>
                        </button>
                    </div>
                </div>

                <!-- Module Navigation -->
                <div class="flex flex-col sm:flex-row justify-between items-center pt-6 border-t border-gray-200 space-y-3 sm:space-y-0">
                    <div>
                        @if($moduleIndex > 0)
                        <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $selectedCourse->modules[$moduleIndex - 1]->id]) }}" 
                           class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700 transition inline-flex items-center">
                            <i class="fas fa-arrow-left mr-2"></i> Previous Module
                        </a>
                        @endif
                    </div>
                    
                    <div class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded">
                        Progress: {{ $moduleIndex + 1 }}/{{ $selectedCourse->modules->count() }}
                    </div>

                    <div>
                        @if($moduleIndex < $selectedCourse->modules->count() - 1)
                        <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $selectedCourse->modules[$moduleIndex + 1]->id]) }}" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
                            Next Module <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        @else
                        <span class="bg-green-600 text-white px-4 py-2 rounded inline-flex items-center">
                            <i class="fas fa-check-circle mr-2"></i> Course Completed
                        </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @elseif(isset($selectedCourse))
        <!-- Course Overview (keep your existing course overview) -->
        @include('components.usersdashboard.course-overview')
    @else
        <!-- Default View (keep your existing default view) -->
        @include('components.usersdashboard.default-view')
    @endif
</div>
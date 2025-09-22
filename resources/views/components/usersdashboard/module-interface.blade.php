<div class="bg-white rounded-lg shadow-md overflow-hidden mb-6">
    <!-- Module Header -->
    <div class="bg-gradient-to-r from-indigo-600 to-purple-700 p-4 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center mb-2">
                    <a href="{{ route('userdashboard', ['course' => $selectedCourse->id]) }}" 
                       class="bg-indigo-800 hover:bg-indigo-900 p-2 rounded mr-3 transition">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h2 class="text-2xl font-bold">{{ $selectedCourse->title }}</h2>
                    </div>
                </div>
                <div class="flex rounded items-center">
                    <h3 class="font-semibold">
                        <span class="text-black text-xl">Module:</span> 
                        <span class="text-indigo-100 mt-1">{{ $selectedModule->title }}</span>
                    </h3>
                </div>
            </div>
            <div class="mt-2 sm:mt-0">
                <span class="bg-indigo-800 bg-opacity-50 px-3 py-1 rounded text-sm">
                    Module {{ $moduleIndex + 1 }} of {{ $selectedCourse->modules->count() }}
                </span>
            </div>
        </div>
    </div>

    <!-- Module Content -->
    <div class="p-6">
        @if($selectedModule->description)
        <div class="prose max-w-none mb-6 p-4 bg-gray-50 rounded-lg">
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
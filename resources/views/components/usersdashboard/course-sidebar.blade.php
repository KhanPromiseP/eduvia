<!-- Enhanced Course Sidebar -->
<div class="bg-white rounded-2xl shadow-md p-2 mb-6 border border-blue-200 sticky top-4 w-80 lg:top-20">
    <!-- Close button for mobile -->
    <div class="flex justify-between items-center mb-6 lg:hidden">
        <h2 class="text-xl font-bold text-gray-800">Learning Hub</h2>
        <button id="closeSidebar" class="text-gray-500 hover:text-gray-700 p-2 rounded-full hover:bg-gray-100">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    @if(isset($selectedCourse))
        <!-- Navigation Tabs -->
        <div class="flex mb-6 bg-gray-100 rounded-lg p-1">
            <button id="resourcesTab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-all duration-200 tab-button active" onclick="switchTab('resources')">
                <i class="fas fa-book mr-2"></i>Resources
            </button>
            <button id="assistantTab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-all duration-200 tab-button" onclick="switchTab('assistant')">
                <i class="fas fa-robot mr-2"></i>Assistant
            </button>
            <button id="toolsTab" class="flex-1 py-2 px-3 rounded-md text-sm font-medium transition-all duration-200 tab-button" onclick="switchTab('tools')">
                <i class="fas fa-tools mr-2"></i>Tools
            </button>
        </div>
        
        <!-- Course Info -->
        <div class="mb-6 pb-4 border-b border-gray-200">
            <div class="flex items-center mb-4">
                @if($selectedCourse->image)
                    <img src="{{ asset('storage/' . $selectedCourse->image) }}" 
                         alt="{{ $selectedCourse->title }}" 
                         class="w-12 h-12 rounded-lg object-cover mr-3 shadow-sm">
                @else
                    <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-3 shadow-sm">
                        <i class="fas fa-book text-white text-lg"></i>
                    </div>
                @endif
                <div>
                    <h3 class="font-bold text-gray-800">{{ Str::limit($selectedCourse->title, 25) }}</h3>
                    <p class="text-xs text-gray-500 mt-1">{{ $selectedCourse->modules->count() }} modules • {{ $selectedCourse->level_name }}</p>
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
            
            <div class="mb-2">
                <div class="flex justify-between items-center text-sm text-gray-600 mb-2">
                    <span class="font-medium">Course Progress</span>
                    <span class="font-semibold">{{ $progressPercentage }}%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full transition-all duration-500 shadow-sm" 
                        style="width: {{ $progressPercentage }}%"></div>
                </div>
                <p class="text-xs text-gray-500 mt-2 text-right">{{ $completedModules }}/{{ $totalModules }} modules completed</p>
            </div>
        </div>
        
        <!-- Resources Tab Content (Default) -->
        <div id="resourcesContent" class="tab-content active">
            <!-- Course Modules -->
            <div class="mb-2">
                <h3 class="font-semibold text-gray-800 mb-4 flex items-center justify-between">
                    <span class="flex items-center">
                        <i class="fas fa-layer-group mr-2 text-indigo-500"></i>
                        Course Content
                    </span>
                    <span class="text-xs text-gray-500 bg-indigo-50 px-2 py-1 rounded-full">
                        {{ $selectedCourse->modules->count() }} modules
                    </span>
                </h3>
                
                <div class="space-y-2 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($selectedCourse->modules as $index => $module)
                        @php
                            $moduleCompleted = false;
                            if ($module->progress && method_exists($module, 'progress')) {
                                $moduleProgress = $module->progress->firstWhere('user_id', auth()->id());
                                $moduleCompleted = $moduleProgress && $moduleProgress->completed;
                            }
                            
                            $isCurrentModule = isset($selectedModule) && $selectedModule->id == $module->id;
                        @endphp
                        
                        <div class="module-item rounded-lg p-3 border border-gray-200 hover:border-indigo-300 transition-all duration-200 {{ $isCurrentModule ? 'active-module bg-indigo-50 border-indigo-300' : '' }}"
                             data-module-id="{{ $module->id }}"
                             data-course-id="{{ $selectedCourse->id }}"
                             id="module-{{ $module->id }}">
                            
                            <!-- Module Header -->
                            <div class="flex items-center justify-between">
                                <a href="{{ route('userdashboard', ['course' => $selectedCourse->id, 'module' => $module->id]) }}" 
                                   class="flex items-center flex-1 cursor-pointer no-underline text-gray-800 hover:text-indigo-600">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-100 to-purple-100 text-indigo-600 flex items-center justify-center text-sm font-semibold mr-3 shadow-sm">
                                        {{ $index + 1 }}
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold">{{ Str::limit($module->title, 22) }}</h4>
                                        <p class="text-xs text-gray-500 mt-1">{{ $module->attachments->count() }} resources</p>
                                    </div>
                                </a>
                                <div class="flex items-center space-x-2">
                                    @if($moduleCompleted)
                                        <i class="fas fa-check-circle text-green-500 text-lg" title="Completed"></i>
                                    @endif
                                    <!-- Dynamic Start/Close Button -->
                                    <button onclick="handleModuleAction(this)" 
                                            class="module-action-btn bg-indigo-600 text-white p-2 rounded-xl hover:bg-indigo-500 transition flex items-center text-xs ml-2"
                                            data-module-id="{{ $module->id }}"
                                            data-module-index="{{ $index }}"
                                            data-course-id="{{ $selectedCourse->id }}"
                                            data-action="start">
                                        <i class="fas fa-play mr-1"></i>
                                        Start
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Module Resources Dropdown -->
                            <div id="moduleResources-{{ $module->id }}" class="module-resources mt-3 ml-1 space-y-2 hidden">
                                @foreach($module->attachments as $attachmentIndex => $attachment)
                                    @php
                                        $isExternalVideo = $attachment->video_url != null;
                                        $fileUrl = $isExternalVideo ? $attachment->video_url : asset('storage/' . $attachment->file_path);
                                        $resourceType = $isExternalVideo ? 'external_video' : 'file';
                                        $isCurrentAttachment = isset($selectedAttachment) && $selectedAttachment->id == $attachment->id;
                                        
                                        $fileTypeInfo = getFileTypeInfo($attachment->file_type, $attachment->video_url);
                                    @endphp

                                    <div class="resource-item p-3 rounded-lg border border-gray-200 hover:border-indigo-300 hover:shadow-sm transition-all duration-200 flex items-center justify-between group cursor-pointer
                                        {{ $isCurrentAttachment ? 'active-resource bg-blue-50 border-blue-300' : '' }}"
                                         onclick="openAttachmentInDashboard({{ $attachment->id }}, '{{ $attachment->file_type }}', '{{ $attachment->title }}', '{{ $attachment->isExternalVideo() ? $attachment->video_url : asset('storage/' . $attachment->file_path) }}', '{{ $attachment->isExternalVideo() ? 'external_video' : 'file' }}', '{{ addslashes($attachment->description) }}')" 
                                         data-resource-id="{{ $attachment->id }}">
                                        <div class="flex items-center flex-1 min-w-0">
                                            <div class="mr-3 flex-shrink-0">
                                                <div class="w-10 h-10 {{ $fileTypeInfo['bgColor'] }} rounded-lg flex items-center justify-center group-hover:{{ $fileTypeInfo['hoverBgColor'] }} transition-colors">
                                                    <i class="{{ $fileTypeInfo['icon'] }} {{ $fileTypeInfo['iconColor'] }}"></i>
                                                </div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <h5 class="font-medium text-gray-800 truncate">{{ $attachment->title }}</h5>
                                                <p class="text-xs text-gray-500 truncate">
                                                    {{ $fileTypeInfo['typeName'] }}
                                                    @if($attachment->file_size && !$isExternalVideo)
                                                        • {{ formatFileSize($attachment->file_size) }}
                                                    @endif
                                                    @if($fileTypeInfo['duration'])
                                                        • {{ $fileTypeInfo['duration'] }}
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <div class="flex items-center">
                                            @if($attachment->is_free)
                                                <span class="text-xs text-green-600 bg-green-100 px-2 py-1 rounded-full mr-2">Free</span>
                                            @endif
                                            <button onclick="openAttachmentInDashboard({{ $attachment->id }}, '{{ $attachment->file_type }}', '{{ $attachment->title }}', '{{ $attachment->isExternalVideo() ? $attachment->video_url : asset('storage/' . $attachment->file_path) }}', '{{ $attachment->isExternalVideo() ? 'external_video' : 'file' }}', '{{ addslashes($attachment->description) }}')"  class="bg-indigo-600 text-white rounded-full h-7 w-9 hover:bg-indigo-700 transition-all duration-200 opacity-0 group-hover:opacity-100 shadow-sm">
                                                <i class="fas fa-eye text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- AI Assistant Tab Content -->
        <div id="assistantContent" class="tab-content hidden">
            <div class="text-center py-4">
                <div class="bg-gradient-to-br from-blue-50 to-purple-50 rounded-xl p-6 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-robot text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">AI Learning Assistant</h3>
                    <p class="text-gray-600 text-sm mb-4">Get personalized help with your course content</p>
                </div>
                
                <div class="space-y-3">
                    <button onclick="openAIAssistant()" class="w-full bg-gradient-to-r from-blue-500 to-purple-600 text-white py-3 px-4 rounded-xl hover:from-blue-600 hover:to-purple-700 transition-all duration-300 flex items-center justify-center shadow-md hover:shadow-lg">
                        <i class="fas fa-comments mr-3"></i> Ask Course Assistant
                    </button>
                    
                    <div class="grid grid-cols-2 gap-3 mt-4">
                        <button class="bg-white border border-gray-200 rounded-lg p-3 hover:bg-blue-50 hover:border-blue-300 transition-all duration-200 text-center group">
                            <i class="fas fa-lightbulb text-yellow-500 text-xl mb-2 group-hover:text-yellow-600"></i>
                            <div class="text-xs font-medium">Explain Concept</div>
                        </button>
                        <button class="bg-white border border-gray-200 rounded-lg p-3 hover:bg-green-50 hover:border-green-300 transition-all duration-200 text-center group">
                            <i class="fas fa-question-circle text-green-500 text-xl mb-2 group-hover:text-green-600"></i>
                            <div class="text-xs font-medium">Quiz Me</div>
                        </button>
                        <button class="bg-white border border-gray-200 rounded-lg p-3 hover:bg-purple-50 hover:border-purple-300 transition-all duration-200 text-center group">
                            <i class="fas fa-sticky-note text-purple-500 text-xl mb-2 group-hover:text-purple-600"></i>
                            <div class="text-xs font-medium">Summarize</div>
                        </button>
                        <button class="bg-white border border-gray-200 rounded-lg p-3 hover:bg-orange-50 hover:border-orange-300 transition-all duration-200 text-center group">
                            <i class="fas fa-tasks text-orange-500 text-xl mb-2 group-hover:text-orange-600"></i>
                            <div class="text-xs font-medium">Study Plan</div>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Course Tools Tab Content -->
        <div id="toolsContent" class="tab-content hidden">
            <div class="text-center py-4">
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-xl p-6 mb-4">
                    <div class="w-16 h-16 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                        <i class="fas fa-tools text-white text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Learning Tools</h3>
                    <p class="text-gray-600 text-sm">Enhance your learning experience</p>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="takeCourseNotes()" class="bg-white border border-gray-200 rounded-xl p-4 hover:bg-blue-50 hover:border-blue-300 hover:shadow-md transition-all duration-200 text-center group">
                        <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-blue-200 transition-colors">
                            <i class="fas fa-sticky-note text-blue-600 text-xl"></i>
                        </div>
                        <div class="font-medium text-gray-800">Notes</div>
                        <p class="text-xs text-gray-500 mt-1">Take course notes</p>
                    </button>
                    
                    <button onclick="openCourseBookmarks()" class="bg-white border border-gray-200 rounded-xl p-4 hover:bg-green-50 hover:border-green-300 hover:shadow-md transition-all duration-200 text-center group">
                        <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-green-200 transition-colors">
                            <i class="fas fa-bookmark text-green-600 text-xl"></i>
                        </div>
                        <div class="font-medium text-gray-800">Bookmarks</div>
                        <p class="text-xs text-gray-500 mt-1">Save important content</p>
                    </button>
                    
                    <button onclick="openCourseFlashcards()" class="bg-white border border-gray-200 rounded-xl p-4 hover:bg-purple-50 hover:border-purple-300 hover:shadow-md transition-all duration-200 text-center group">
                        <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-purple-200 transition-colors">
                            <i class="fas fa-layer-group text-purple-600 text-xl"></i>
                        </div>
                        <div class="font-medium text-gray-800">Flashcards</div>
                        <p class="text-xs text-gray-500 mt-1">Study with flashcards</p>
                    </button>
                    
                    <button onclick="downloadCourseResources()" class="bg-white border border-gray-200 rounded-xl p-4 hover:bg-orange-50 hover:border-orange-300 hover:shadow-md transition-all duration-200 text-center group">
                        <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mx-auto mb-3 group-hover:bg-orange-200 transition-colors">
                            <i class="fas fa-download text-orange-600 text-xl"></i>
                        </div>
                        <div class="font-medium text-gray-800">Resources</div>
                        <p class="text-xs text-gray-500 mt-1">Download materials</p>
                    </button>
                </div>
                
                <div class="mt-6 pt-4 border-t border-gray-200">
                    <h4 class="font-semibold text-gray-800 mb-3 text-left">Quick Actions</h4>
                    <div class="space-y-2">
                        <button class="w-full bg-white border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-all duration-200 text-left flex items-center">
                            <i class="fas fa-print text-gray-500 mr-3"></i>
                            <span class="text-sm">Print Module</span>
                        </button>
                        <button class="w-full bg-white border border-gray-200 rounded-lg p-3 hover:bg-gray-50 transition-all duration-200 text-left flex items-center">
                            <i class="fas fa-share-alt text-gray-500 mr-3"></i>
                            <span class="text-sm">Share Progress</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        
    @else
        <!-- Default Course List -->
        <h2 class="text-xl font-bold text-gray-800 mb-6 flex items-center">
            <i class="fas fa-graduation-cap mr-3 text-indigo-600"></i> My Courses
        </h2>
        
        @if($purchasedCourses->count() > 0)
            <div class="space-y-4 max-h-96 overflow-y-auto pr-2 custom-scrollbar">
                @foreach($purchasedCourses as $course)
                    @php 
                        $progress = $course->progressPercentage(Auth::id());
                    @endphp

                    <a href="{{ route('userdashboard', ['course' => $course->id]) }}" 
                       class="block p-4 rounded-xl border border-gray-200 hover:border-indigo-300 hover:shadow-md transition-all duration-200 
                              {{ isset($selectedCourse) && $selectedCourse->id == $course->id 
                                    ? 'bg-indigo-50 border-indigo-300' 
                                    : 'bg-white' }}">
                        <div class="flex items-center">
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-12 h-12 rounded-lg object-cover mr-4 shadow-sm">
                            @else
                                <div class="w-12 h-12 rounded-lg bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center mr-4 shadow-sm">
                                    <i class="fas fa-book text-white"></i>
                                </div>
                            @endif
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-800 truncate">{{ $course->title }}</h3>
                                <p class="text-xs text-gray-600 mt-1">{{ $course->modules->count() }} modules</p>
                                
                                <!-- Progress Bar -->
                                <div class="mt-3">
                                    <div class="flex justify-between items-center text-xs text-gray-600 mb-1">
                                        <span>Progress</span>
                                        <span class="font-semibold">{{ $progress }}%</span>
                                    </div>
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                            style="width: {{ $progress }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-book-open text-3xl text-gray-300"></i>
                </div>
                <p class="text-gray-500 mb-4">No courses purchased yet.</p>
                <a href="{{ route('courses.index') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition inline-flex items-center text-sm">
                    <i class="fas fa-search mr-2"></i> Browse Courses
                </a>
            </div>
        @endif
    @endif
</div>

<style>
.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.tab-button {
    color: #6b7280;
}

.tab-button.active {
    background: white;
    color: #4f46e5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.active-module {
    background-color: #f0f4ff;
    border-color: #c7d2fe;
}

.active-resource {
    background-color: #f0f9ff;
    border-color: #bae6fd;
}

.resource-item:hover {
    cursor: pointer;
    transform: translateY(-1px);
}

.module-item {
    transition: all 0.3s ease;
}

/* Smooth dropdown animation */
.module-resources {
    transition: all 0.3s ease-in-out;
    overflow: hidden;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

.tab-button {
    color: #6b7280;
}

.tab-button.active {
    background: white;
    color: #4f46e5;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.active-module {
    background-color: #f0f4ff;
    border-color: #c7d2fe;
}

.active-resource {
    background-color: #f0f9ff;
    border-color: #bae6fd;
}

.resource-item:hover {
    cursor: pointer;
    transform: translateY(-1px);
}

.module-item {
    transition: all 0.3s ease;
}

.bg-red-600 {
    background-color: #dc2626;
}

.hover\:bg-red-700:hover {
    background-color: #b91c1c;
}

.module-resources {
    transition: all 0.3s ease-in-out;
    overflow: hidden;
}
</style>

<script>
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    document.getElementById(tabName + 'Content').classList.add('active');
    document.getElementById(tabName + 'Content').classList.remove('hidden');
    
    // Activate selected tab button
    document.getElementById(tabName + 'Tab').classList.add('active');
}


// Start Module function - Does BOTH actions
// Module State Management
const ModuleManager = {
    // Handle module button actions
    handleAction(button) {
        const moduleId = button.getAttribute('data-module-id');
        const courseId = button.getAttribute('data-course-id');
        const action = button.getAttribute('data-action');
        
        if (action === 'start') {
            this.startModule(moduleId, courseId);
        } else {
            this.closeModule(moduleId);
        }
    },
    
    // Start module - expands AND navigates
    startModule(moduleId, courseId) {
        this.expandModule(moduleId);
        
        // Navigate to dashboard
        const dashboardUrl = `${window.location.origin}/userdashboard?course=${courseId}&module=${moduleId}`;
        window.location.href = dashboardUrl;
    },
    
    // Expand module
    expandModule(moduleId) {
        const resources = document.getElementById(`moduleResources-${moduleId}`);
        const moduleElement = document.getElementById(`module-${moduleId}`);
        
        if (!resources || !moduleElement) return;
        
        // Close all other modules
        this.closeAllOtherModules(moduleId);
        
        // Expand this module
        resources.classList.remove('hidden');
        moduleElement.classList.add('active-module', 'bg-indigo-50', 'border-indigo-300');
        
        // Update button to Close
        this.updateButtonState(moduleId, 'close');
    },
    
    // Close module
    closeModule(moduleId) {
        const resources = document.getElementById(`moduleResources-${moduleId}`);
        const moduleElement = document.getElementById(`module-${moduleId}`);
        
        if (resources) resources.classList.add('hidden');
        if (moduleElement) moduleElement.classList.remove('active-module', 'bg-indigo-50', 'border-indigo-300');
        
        // Update button to Start
        this.updateButtonState(moduleId, 'start');
    },
    
    // Close all other modules
    closeAllOtherModules(currentModuleId) {
        document.querySelectorAll('.module-item').forEach(module => {
            const moduleId = module.getAttribute('data-module-id');
            if (moduleId !== currentModuleId) {
                this.closeModule(moduleId);
            }
        });
    },
    
    // Update button state
    updateButtonState(moduleId, action) {
        const buttons = document.querySelectorAll(`button[data-module-id="${moduleId}"]`);
        
        buttons.forEach(button => {
            if (action === 'close') {
                button.innerHTML = '<i class="fas fa-times mr-1"></i>Close';
                button.className = button.className.replace('bg-indigo-600 hover:bg-indigo-700', 'bg-red-600 hover:bg-red-700');
                button.setAttribute('data-action', 'close');
            } else {
                button.innerHTML = '<i class="fas fa-play mr-1"></i>Start';
                button.className = button.className.replace('bg-red-600 hover:bg-red-700', 'bg-indigo-600 hover:bg-indigo-700');
                button.setAttribute('data-action', 'start');
            }
        });
    },
    
    // Auto-expand module from URL parameters
    autoExpandFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const moduleId = urlParams.get('module');
        
        if (moduleId) {
            this.expandModule(moduleId);
        }
    },
    
    // Handle resource clicks
    openResource(resourceId, fileType, title, fileUrl, resourceType, description) {
        // Remove active class from all resources
        document.querySelectorAll('.resource-item').forEach(item => {
            item.classList.remove('active-resource', 'bg-blue-50', 'border-blue-300');
        });
        
        // Add active class to clicked resource
        const clickedResource = document.querySelector(`[data-resource-id="${resourceId}"]`);
        if (clickedResource) {
            clickedResource.classList.add('active-resource', 'bg-blue-50', 'border-blue-300');
            clickedResource.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
        
        // Load resource in dashboard (implementation depends on your dashboard structure)
        this.loadResourceInDashboard(resourceId, fileType, title, fileUrl, resourceType, description);
    },
    
    loadResourceInDashboard(resourceId, fileType, title, fileUrl, resourceType, description) {
        // This should be implemented based on your dashboard structure
        console.log('Loading resource:', { resourceId, title, fileUrl });
        // Add your dashboard loading logic here
    }
};

// Tab Management
function switchTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
        tab.classList.add('hidden');
    });
    
    // Remove active class from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab content
    const tabContent = document.getElementById(`${tabName}Content`);
    if (tabContent) {
        tabContent.classList.add('active');
        tabContent.classList.remove('hidden');
    }
    
    // Activate selected tab button
    const tabButton = document.getElementById(`${tabName}Tab`);
    if (tabButton) {
        tabButton.classList.add('active');
    }
}

// Event Handlers
function handleModuleAction(button) {
    ModuleManager.handleAction(button);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Auto-expand module from URL
    ModuleManager.autoExpandFromURL();
    
    // Initialize first tab
    switchTab('resources');
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        if (!event.target.closest('.module-item')) {
            // Optional: Close all modules when clicking outside
            // ModuleManager.closeAllOtherModules(null);
        }
    });
});

// Expand module function
function expandModule(moduleId, moduleIndex) {
    const resources = document.getElementById('moduleResources-' + moduleId);
    
    // Close all other module resources first
    closeAllModuleResources(moduleId);
    
    // Expand this module
    if (resources) {
        resources.classList.remove('hidden');
        resources.style.maxHeight = resources.scrollHeight + 'px';
        
        // Add active class to module
        const moduleElement = document.getElementById('module-' + moduleId);
        if (moduleElement) {
            moduleElement.classList.add('active-module', 'bg-indigo-50', 'border-indigo-300');
        }
    }
}

// Close all module resources except the specified one
function closeAllModuleResources(currentModuleId) {
    document.querySelectorAll('.module-item').forEach(module => {
        const moduleId = module.getAttribute('data-module-id');
        if (moduleId != currentModuleId) {
            const resources = document.getElementById('moduleResources-' + moduleId);
            if (resources && !resources.classList.contains('hidden')) {
                resources.classList.add('hidden');
                resources.style.maxHeight = '0';
                
                // Remove active class
                module.classList.remove('active-module', 'bg-indigo-50', 'border-indigo-300');
            }
        }
    });
}

// Open resource in dashboard
function openResourceInDashboard(resourceId, fileType, title, fileUrl, resourceType, description) {
    // Remove active class from all resources
    document.querySelectorAll('.resource-item').forEach(item => {
        item.classList.remove('active-resource', 'bg-blue-50', 'border-blue-300');
    });
    
    // Add active class to clicked resource
    const clickedResource = document.querySelector(`[data-resource-id="${resourceId}"]`);
    if (clickedResource) {
        clickedResource.classList.add('active-resource', 'bg-blue-50', 'border-blue-300');
        
        // Scroll the resource into view
        clickedResource.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }
    
    // Load the resource in the main dashboard area
    loadResourceInDashboard(resourceId, fileType, title, fileUrl, resourceType, description);
    
    // Update URL without page reload
    const newUrl = `${window.location.pathname}?resource=${resourceId}`;
    window.history.pushState({ resourceId }, title, newUrl);
}

function loadResourceInDashboard(resourceId, fileType, title, fileUrl, resourceType, description) {
    const mainContent = document.getElementById('dashboardMainContent') || document.querySelector('.bg-white.rounded-lg.shadow-md.p-6');
    
    if (!mainContent) {
        console.error('Main content area not found');
        return;
    }
    
    let resourceViewerHTML = '';
    
    if (resourceType === 'external_video') {
        resourceViewerHTML = `
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4">
                    <a href="{{ route('userdashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm mb-4">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">${title}</h2>
                    ${description ? `<p class="text-gray-600 mt-2">${description}</p>` : ''}
                </div>
                <div class="bg-black rounded-lg overflow-hidden">
                    <iframe src="${fileUrl}" class="w-full h-96" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        `;
    } else if (fileType === 'pdf') {
        resourceViewerHTML = `
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4">
                    <a href="{{ route('userdashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm mb-4">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">${title}</h2>
                    ${description ? `<p class="text-gray-600 mt-2">${description}</p>` : ''}
                </div>
                <div class="h-96">
                    <iframe src="${fileUrl}" class="w-full h-full" frameborder="0"></iframe>
                </div>
            </div>
        `;
    } else if (['mp4', 'mov', 'avi', 'mkv', 'webm'].includes(fileType)) {
        resourceViewerHTML = `
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4">
                    <a href="{{ route('userdashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm mb-4">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">${title}</h2>
                    ${description ? `<p class="text-gray-600 mt-2">${description}</p>` : ''}
                </div>
                <div class="bg-black rounded-lg overflow-hidden">
                    <video controls class="w-full max-w-4xl mx-auto">
                        <source src="${fileUrl}" type="video/${fileType}">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
        `;
    } else {
        resourceViewerHTML = `
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="mb-4">
                    <a href="{{ route('userdashboard') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 text-sm mb-4">
                        <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                    </a>
                    <h2 class="text-2xl font-bold text-gray-800">${title}</h2>
                    ${description ? `<p class="text-gray-600 mt-2">${description}</p>` : ''}
                </div>
                <div class="text-center py-8">
                    <div class="w-20 h-20 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file text-3xl text-indigo-600"></i>
                    </div>
                    <p class="text-gray-600 mb-4">This resource cannot be previewed directly.</p>
                    <a href="${fileUrl}" download class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download Resource
                    </a>
                </div>
            </div>
        `;
    }
    
    mainContent.innerHTML = resourceViewerHTML;
}

// Auto-expand module with current resource on page load
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const resourceId = urlParams.get('resource');
    
    if (resourceId) {
        const resourceElement = document.querySelector(`[data-resource-id="${resourceId}"]`);
        if (resourceElement) {
            const moduleItem = resourceElement.closest('.module-item');
            if (moduleItem) {
                const moduleId = moduleItem.getAttribute('data-module-id');
                const moduleIndex = Array.from(document.querySelectorAll('.module-item')).indexOf(moduleItem);
                
                // Expand the module
                expandModule(moduleId, moduleIndex);
                
                // Click the resource after a short delay
                setTimeout(() => {
                    resourceElement.click();
                }, 100);
            }
        }
    }
    
    // Initialize the first tab as active
    switchTab('resources');
});

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.module-item')) {
        closeAllModuleResources();
    }
});
</script>
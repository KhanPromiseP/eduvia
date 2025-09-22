<!-- Enhanced Resource Viewer -->
<div id="resourceViewer" class="fixed inset-0 bg-white z-50 hidden flex-col">
    <!-- Viewer Header -->
    <div class="bg-gray-900 text-white p-4 flex justify-between items-center shadow-md">
        <div class="flex items-center flex-1 min-w-0">
            <button onclick="closeResourceViewer()" class="p-2 text-gray-400 hover:text-white mr-3">
                <i class="fas fa-arrow-left text-xl"></i>
            </button>
            <h3 id="viewerTitle" class="text-lg font-semibold truncate"></h3>
        </div>
        
        <!-- Study Tools in Viewer -->
        <div class="flex items-center space-x-2 mr-4">
            <button onclick="takeNotesFromViewer()" class="p-2 text-gray-400 hover:text-white" title="Take Notes">
                <i class="fas fa-sticky-note"></i>
            </button>
            <button onclick="bookmarkCurrentResource()" class="p-2 text-gray-400 hover:text-white" title="Bookmark">
                <i class="fas fa-bookmark"></i>
            </button>
            <button onclick="toggleFullscreen()" class="p-2 text-gray-400 hover:text-white" title="Fullscreen">
                <i class="fas fa-expand"></i>
            </button>
        </div>
        
        <button onclick="closeResourceViewer()" class="p-2 text-gray-400 hover:text-white ml-4">
            <i class="fas fa-times text-xl"></i>
        </button>
    </div>
    
    <!-- Viewer Content -->
    <div class="flex-1 bg-gray-100 relative overflow-auto">
        <div id="viewerContent" class="max-w-4xl mx-auto p-6">
            <!-- Content will be loaded here -->
        </div>
        
        <!-- Loading Indicator -->
        <div id="viewerLoading" class="absolute inset-0 bg-white bg-opacity-90 flex items-center justify-center hidden">
            <div class="text-center">
                <i class="fas fa-spinner fa-spin text-3xl text-indigo-600 mb-3"></i>
                <p class="text-gray-600">Loading content securely...</p>
            </div>
        </div>
    </div>
    
    <!-- Notes Panel (Collapsible) -->
    <div id="notesPanel" class="hidden bg-white border-t border-gray-200">
        <div class="p-4">
            <h4 class="font-semibold text-gray-800 mb-3">My Notes</h4>
            <textarea id="resourceNotes" class="w-full h-32 p-3 border rounded resize-none" 
                      placeholder="Add your notes here..."></textarea>
            <div class="flex justify-end mt-2 space-x-2">
                <button onclick="saveNotes()" class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
                    Save Notes
                </button>
                <button onclick="hideNotesPanel()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
    
    <!-- Viewer Footer -->
    <div class="bg-gray-800 text-white p-3 text-center text-sm border-t border-gray-700">
        <i class="fas fa-shield-alt mr-1"></i> Secure content viewer - Protected intellectual property
    </div>
</div>

<!-- Study Tools Modal -->
<div id="studyToolsModal" class="fixed inset-0 bg-black bg-opacity-50 z-60 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl mx-4">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800" id="studyToolsTitle">Study Tools</h3>
        </div>
        <div class="p-4" id="studyToolsContent">
            <!-- Content will be loaded dynamically -->
        </div>
        <div class="p-4 border-t flex justify-end">
            <button onclick="closeStudyTools()" class="bg-gray-300 text-gray-700 px-4 py-2 rounded">
                Close
            </button>
        </div>
    </div>
</div>
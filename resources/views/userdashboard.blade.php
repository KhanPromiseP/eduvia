@extends('layouts.learning')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Dashboard - Secure Platform</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <script src="https://unpkg.com/docx-preview@0.1.7/dist/docx-preview.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf_viewer.min.css">
    <style>
        .progress-ring {
            transform: rotate(-90deg);
        }
        .attachment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        .video-thumbnail {
            position: relative;
            cursor: pointer;
        }
        .video-thumbnail::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
        }
        .video-thumbnail:hover::after {
            opacity: 0.9;
        }
        .module-content {
            transition: max-height 0.3s ease-out;
            overflow: hidden;
        }
        .collapsed {
            max-height: 0;
        }
        .expanded {
            max-height: 5000px;
        }
        .purchase-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .purchase-overlay:hover {
            opacity: 1;
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        /* Security measures */
        .secure-content {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }
        /* Animation for transitions */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }

        /* Sidebar styling */
.module-item.active-module {
    background-color: #f0f4ff;
    border-left: 4px solid #4f46e5;
}

.attachment-item.active-attachment {
    background-color: #f0f4ff;
}

.attachment-item.completed-attachment .truncate {
    color: #10b981;
}

.attachment-item.completed-attachment i {
    color: #10b981;
}

/* Mobile sidebar overlay */
.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 40;
    display: none;
}

.sidebar-overlay.active {
    display: block;
}

/* Responsive design */
@media (max-width: 1024px) {
    .sidebar-mobile {
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        width: 85%;
        max-width: 320px;
        z-index: 50;
        transform: translateX(-100%);
        transition: transform 0.3s ease;
    }
    
    .sidebar-mobile.active {
        transform: translateX(0);
    }

    /* Completion styling */
.attachment-item.completed-attachment {
    background-color: #f0f9ff;
}

.attachment-item.completed-attachment .truncate {
    color: #059669;
}

.attachment-item.active-attachment {
    background-color: #eef2ff;
    border-left: 3px solid #4f46e5;
}

.module-item.active-module {
    background-color: #f0f4ff;
    border-left: 4px solid #4f46e5;
}

/* Main content area styling */
#activeContent {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Ensure hidden class works */
.hidden {
    display: none !important;
}
}
    </style>
</head>
<body class="bg-gray-100">
    @if(session('success') || session('info'))
        <div 
            x-data="{ show: true }" 
            x-show="show" 
            x-init="setTimeout(() => show = false, 5000)" 
            class="max-w-3xl mx-auto mb-6 px-6 py-4 rounded-lg shadow-lg 
                {{ session('success') ? 'bg-green-100 text-green-800 border border-green-300' : 'bg-blue-100 text-blue-800 border border-blue-300' }}">
            
            <div class="flex items-center">
                <i class="fas {{ session('success') ? 'fa-check-circle text-green-500' : 'fa-info-circle text-blue-500' }} mr-3 text-xl"></i>
                <span class="font-medium text-lg">
                    {{ session('success') ?? session('info') }}
                </span>
            </div>
        </div>
    @endif


    @include('components.userslearning-dashboard')
    </div>

    <!-- Enhanced Resource Viewer JavaScript -->
    <script>
    // File type support configuration
// File type support configuration
const SUPPORTED_FILE_TYPES = {
    // Video formats
    'mp4': { supported: true, type: 'video', player: 'html5' },
    'mov': { supported: true, type: 'video', player: 'html5' },
    'avi': { supported: true, type: 'video', player: 'html5' },
    'mkv': { supported: true, type: 'video', player: 'html5' },
    'webm': { supported: true, type: 'video', player: 'html5' },
    'wmv': { supported: true, type: 'video', player: 'html5' },
    
    // Document formats
    'pdf': { supported: true, type: 'document', viewer: 'pdfjs' },
    'doc': { supported: true, type: 'document', viewer: 'docx' },
    'docx': { supported: true, type: 'document', viewer: 'docx' },
    'txt': { supported: true, type: 'document', viewer: 'text' },
    
    // Image formats
    'jpg': { supported: true, type: 'image' },
    'jpeg': { supported: true, type: 'image' },
    'png': { supported: true, type: 'image' },
    'gif': { supported: true, type: 'image' },
    'bmp': { supported: true, type: 'image' },
    'webp': { supported: true, type: 'image' },
    'svg': { supported: true, type: 'image' },
    
    // Audio formats
    'mp3': { supported: true, type: 'audio', player: 'html5' },
    'wav': { supported: true, type: 'audio', player: 'html5' },
    'ogg': { supported: true, type: 'audio', player: 'html5' },
    
    // Archive formats (show info but don't preview)
    'zip': { supported: false, type: 'archive' },
    'rar': { supported: false, type: 'archive' },
    '7z': { supported: false, type: 'archive' }
};

// Enhanced completion tracking using localStorage
const COMPLETION_STORAGE_KEY = 'course_completions';

// Study Tools
const STUDY_TOOLS = {
    notes: {},
    bookmarks: {},
    flashcards: {}
};

// Initialize everything when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Learning Dashboard...');
    initCompletionTracking();
    initStudyTools();
    
    // Auto-expand the current module if one is selected
    @if(isset($selectedModule))
        setTimeout(() => {
            toggleModule({{ $selectedModule->id }});
        }, 100);
    @endif
    
    // Add security event listeners
    document.addEventListener('contextmenu', function(e) {
        if (e.target.closest('#resourceViewer') || e.target.closest('.secure-content')) {
            e.preventDefault();
            showSecurityToast('Downloading content is disabled to protect intellectual property.');
        }
    });

    document.addEventListener('dragstart', function(e) {
        if (e.target.closest('#resourceViewer') || e.target.closest('.secure-content')) {
            e.preventDefault();
        }
    });
    
    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            const notesPanel = document.getElementById('notesPanel');
            if (notesPanel && !notesPanel.classList.contains('hidden')) {
                saveNotes();
            }
        }
        
        if (e.key === 'Escape') {
            const viewer = document.getElementById('resourceViewer');
            if (viewer && !viewer.classList.contains('hidden')) {
                closeResourceViewer();
            }
            
            const studyModal = document.getElementById('studyToolsModal');
            if (studyModal && !studyModal.classList.contains('hidden')) {
                closeStudyTools();
            }
        }
    });
    
    console.log('Learning Dashboard initialized successfully');
});

// ===== COMPLETION TRACKING =====
function initCompletionTracking() {
    if (!localStorage.getItem(COMPLETION_STORAGE_KEY)) {
        localStorage.setItem(COMPLETION_STORAGE_KEY, JSON.stringify({}));
    }
    updateCompletionUI();
}

function markAttachmentAsCompleted(attachmentId) {
    const completions = JSON.parse(localStorage.getItem(COMPLETION_STORAGE_KEY) || '{}');
    completions[attachmentId] = true;
    localStorage.setItem(COMPLETION_STORAGE_KEY, JSON.stringify(completions));
    updateAttachmentUI(attachmentId);
}

function isAttachmentCompleted(attachmentId) {
    const completions = JSON.parse(localStorage.getItem(COMPLETION_STORAGE_KEY) || '{}');
    return completions[attachmentId] || false;
}

function updateAttachmentUI(attachmentId) {
    const attachmentElement = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
    if (attachmentElement && isAttachmentCompleted(attachmentId)) {
        attachmentElement.classList.add('completed-attachment');
        if (!attachmentElement.querySelector('.viewed-indicator')) {
            const checkmark = document.createElement('span');
            checkmark.className = 'viewed-indicator ml-2 text-xs text-green-600';
            checkmark.innerHTML = '<i class="fas fa-check-circle"></i>';
            attachmentElement.appendChild(checkmark);
        }
    }
}

function updateCompletionUI() {
    const completions = JSON.parse(localStorage.getItem(COMPLETION_STORAGE_KEY) || '{}');
    Object.keys(completions).forEach(attachmentId => {
        updateAttachmentUI(attachmentId);
    });
}

// ===== MAIN ATTACHMENT HANDLING =====
function openAttachmentInDashboard(attachmentId, fileType, title, fileUrl, resourceType, description = '') {
    console.log('Opening attachment in dashboard:', attachmentId, title);
    
    // Mark as completed
    markAttachmentAsCompleted(attachmentId);
    
    // Update active states in sidebar
    document.querySelectorAll('.attachment-item').forEach(el => {
        el.classList.remove('active-attachment');
    });
    
    const activeElement = document.querySelector(`[data-attachment-id="${attachmentId}"]`);
    if (activeElement) {
        activeElement.classList.add('active-attachment');
    }
    
    // Hide default content and show active content
    const defaultContent = document.getElementById('defaultContent');
    const activeContent = document.getElementById('activeContent');
    
    if (defaultContent && activeContent) {
        defaultContent.classList.add('hidden');
        activeContent.classList.remove('hidden');
        
        // Display the content in the main area
        displayContentInMainArea(attachmentId, fileType, title, fileUrl, resourceType, description);
    } else {
        console.error('Content elements not found - falling back to resource viewer');
        // Fallback to resource viewer
        openResourceViewer(attachmentId, fileType, title, fileUrl, resourceType);
    }
}

function displayContentInMainArea(attachmentId, fileType, title, fileUrl, resourceType, description) {
    const activeContent = document.getElementById('activeContent');
    if (!activeContent) {
        console.error('Active content element not found');
        return;
    }
    
    console.log('Displaying content:', fileType, resourceType);
    
    if (resourceType === 'external_video' || isVideoFile(fileType)) {
        activeContent.innerHTML = createVideoContent(title, fileUrl, resourceType, fileType, description);
    } else if (isImageFile(fileType)) {
        activeContent.innerHTML = createImageContent(title, fileUrl, description);
    } else if (isDocumentFile(fileType)) {
        activeContent.innerHTML = createDocumentContent(title, fileUrl, fileType, description);
    } else {
        activeContent.innerHTML = createGenericContent(title, fileUrl, fileType, description);
    }
}

// ===== CONTENT CREATION FUNCTIONS =====
function createVideoContent(title, fileUrl, resourceType, fileType, description) {
    if (resourceType === 'external_video') {
        const youtubeId = extractYouTubeId(fileUrl);
        if (youtubeId) {
            return `
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="bg-black rounded-t-lg">
                        <iframe 
                            src="https://www.youtube.com/embed/${youtubeId}?rel=0&modestbranding=1&autoplay=1" 
                            class="w-full h-96" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <div class="p-6">
                        <h3 class="text-2xl font-bold text-gray-800 mb-3">${title}</h3>
                        ${description ? `<p class="text-gray-600 mb-4">${description}</p>` : ''}
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fab fa-youtube text-red-500 mr-2"></i>
                            <span>YouTube Video</span>
                        </div>
                    </div>
                </div>
            `;
        }
    }
    
    return `
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-black rounded-t-lg">
                <video controls controlsList="nodownload" class="w-full h-96" autoplay>
                    <source src="${fileUrl}" type="${getVideoMimeType(fileType)}">
                    Your browser does not support the video tag.
                </video>
            </div>
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-3">${title}</h3>
                ${description ? `<p class="text-gray-600 mb-4">${description}</p>` : ''}
                <div class="flex items-center text-sm text-gray-500">
                    <i class="fas fa-video text-purple-500 mr-2"></i>
                    <span>Video Content</span>
                </div>
            </div>
        </div>
    `;
}

function createImageContent(title, fileUrl, description) {
    return `
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-3">${title}</h3>
                ${description ? `<p class="text-gray-600 mb-4">${description}</p>` : ''}
                <div class="flex justify-center">
                    <img src="${fileUrl}" alt="${title}" class="max-w-full max-h-96 rounded-lg shadow-md" 
                         oncontextmenu="return false;" loading="lazy">
                </div>
                <div class="mt-4 flex items-center text-sm text-gray-500">
                    <i class="fas fa-image text-green-500 mr-2"></i>
                    <span>Image Content</span>
                </div>
            </div>
        </div>
    `;
}

function createDocumentContent(title, fileUrl, fileType, description) {
    const fileExtension = fileType.toLowerCase();
    
    return `
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-800">${title}</h3>
                    <div class="flex space-x-2">
                        <button onclick="openInFullscreen('${fileUrl}', '${fileType}')" 
                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition flex items-center">
                            <i class="fas fa-expand mr-2"></i> Fullscreen
                        </button>
                        <a href="${fileUrl}" download 
                           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg transition flex items-center">
                            <i class="fas fa-download mr-2"></i> Download
                        </a>
                    </div>
                </div>
                
                ${description ? `<p class="text-gray-600 mb-6">${description}</p>` : ''}
                
                <div class="bg-gray-50 rounded-lg border">
                    <!-- Document Viewer Header -->
                    <div class="bg-white border-b px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center text-gray-600">
                                <i class="fas ${getDocumentIcon(fileType)} text-indigo-600 mr-2"></i>
                                <span class="font-medium">${fileExtension.toUpperCase()} Document</span>
                            </div>
                            <div class="text-sm text-gray-500">
                                ${getDocumentTypeDescription(fileType)}
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            <button onclick="zoomDocument('out')" class="p-2 hover:bg-gray-100 rounded">
                                <i class="fas fa-search-minus"></i>
                            </button>
                            <span id="zoomLevel" class="text-sm text-gray-600 w-12 text-center">100%</span>
                            <button onclick="zoomDocument('in')" class="p-2 hover:bg-gray-100 rounded">
                                <i class="fas fa-search-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Document Viewer Content -->
                    <div class="p-4" style="height: 70vh;">
                        ${createDocumentViewerContent(fileUrl, fileType, title)}
                    </div>
                    
                    <!-- Document Controls Footer -->
                    <div class="bg-white border-t px-4 py-3 flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <button onclick="previousPage()" class="flex items-center text-gray-600 hover:text-gray-800">
                                <i class="fas fa-chevron-left mr-1"></i> Previous
                            </button>
                            <span id="pageInfo" class="text-sm text-gray-600">Page 1 of 1</span>
                            <button onclick="nextPage()" class="flex items-center text-gray-600 hover:text-gray-800">
                                Next <i class="fas fa-chevron-right ml-1"></i>
                            </button>
                        </div>
                        <div class="flex items-center space-x-3">
                            <button onclick="toggleStudyTools()" class="flex items-center text-gray-600 hover:text-indigo-600">
                                <i class="fas fa-highlighter mr-1"></i> Study Tools
                            </button>
                            <button onclick="printDocument('${fileUrl}')" class="flex items-center text-gray-600 hover:text-indigo-600">
                                <i class="fas fa-print mr-1"></i> Print
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Study Tools Panel -->
                <div id="studyToolsPanel" class="hidden mt-4 bg-white border rounded-lg p-4">
                    <div class="flex justify-between items-center mb-4">
                        <h4 class="font-semibold text-gray-800">Study Tools</h4>
                        <button onclick="toggleStudyTools()" class="text-gray-500 hover:text-gray-700">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="text-center">
                            <button onclick="addBookmark()" class="w-full bg-blue-50 hover:bg-blue-100 text-blue-600 p-3 rounded-lg">
                                <i class="fas fa-bookmark text-lg mb-2"></i>
                                <div class="text-sm font-medium">Add Bookmark</div>
                            </button>
                        </div>
                        <div class="text-center">
                            <button onclick="openNotes()" class="w-full bg-green-50 hover:bg-green-100 text-green-600 p-3 rounded-lg">
                                <i class="fas fa-edit text-lg mb-2"></i>
                                <div class="text-sm font-medium">Take Notes</div>
                            </button>
                        </div>
                        <div class="text-center">
                            <button onclick="createFlashcard()" class="w-full bg-purple-50 hover:bg-purple-100 text-purple-600 p-3 rounded-lg">
                                <i class="fas fa-layer-group text-lg mb-2"></i>
                                <div class="text-sm font-medium">Create Flashcard</div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function createGenericContent(title, fileUrl, fileType, description) {
    // For generic files, try to use the document viewer if it makes sense
    if (['ppt', 'pptx', 'xls', 'xlsx'].includes(fileType.toLowerCase())) {
        return createDocumentContent(title, fileUrl, fileType, description);
    }
    
    return `
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="p-6">
                <h3 class="text-2xl font-bold text-gray-800 mb-3">${title}</h3>
                ${description ? `<p class="text-gray-600 mb-4">${description}</p>` : ''}
                <div class="bg-gray-50 rounded-lg p-8 text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-download text-gray-600 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Download Required</h4>
                    <p class="text-gray-600 mb-4">This ${fileType.toUpperCase()} file needs to be downloaded to view its contents.</p>
                    <a href="${fileUrl}" download 
                       class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition inline-flex items-center text-lg">
                        <i class="fas fa-download mr-3"></i> Download ${fileType.toUpperCase()} File
                    </a>
                    <p class="text-sm text-gray-500 mt-4">File will be saved to your downloads folder</p>
                </div>
            </div>
        </div>
    `;
}

// ===== RESOURCE VIEWER FUNCTIONS =====
function openResourceViewer(attachmentId, fileType, title, fileUrl, resourceType = 'file') {
    const viewer = document.getElementById('resourceViewer');
    const viewerContent = document.getElementById('viewerContent');
    const viewerTitle = document.getElementById('viewerTitle');
    const viewerLoading = document.getElementById('viewerLoading');
    
    if (!viewer || !viewerContent || !viewerTitle || !viewerLoading) {
        console.error('Resource viewer elements not found');
        showToast('Resource viewer not available. Please refresh the page.', 'error');
        return;
    }
    
    // Show loading
    viewerLoading.classList.remove('hidden');
    viewerContent.innerHTML = '';
    
    // Set title
    viewerTitle.textContent = title;
    viewer.dataset.attachmentId = attachmentId;
    viewer.dataset.resourceType = resourceType;
    
    // Show viewer
    viewer.classList.remove('hidden');
    document.body.style.overflow = 'hidden';
    
    // Load appropriate content
    if (resourceType === 'external_video') {
        loadExternalVideo(fileUrl, title);
    } else {
        const fileConfig = SUPPORTED_FILE_TYPES[fileType.toLowerCase()];
        if (!fileConfig || !fileConfig.supported) {
            showUnsupportedFileType(fileType, title);
            viewerLoading.classList.add('hidden');
        } else {
            loadFileContent(attachmentId, fileType, fileConfig, title, fileUrl);
        }
    }
    
    // Load existing notes if any
    loadExistingNotes(attachmentId);
}

// function loadExternalVideo(videoUrl, title) {
//     const viewerContent = document.getElementById('viewerContent');
//     const viewerLoading = document.getElementById('viewerLoading');
    
//     try {
//         if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
//             const videoId = extractYouTubeId(videoUrl);
//             if (videoId) {
//                 viewerContent.innerHTML = `
//                     <div class="bg-black rounded-lg overflow-hidden shadow-lg">
//                         <iframe 
//                             src="https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1" 
//                             class="w-full h-96" 
//                             frameborder="0" 
//                             allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
//                             allowfullscreen>
//                         </iframe>
//                     </div>
//                     <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
//                         <h4 class="font-semibold text-gray-800 mb-2">${title}</h4>
//                         <p class="text-sm text-gray-600">YouTube video - use player controls to navigate</p>
//                     </div>
//                 `;
//             } else {
//                 throw new Error('Invalid YouTube URL');
//             }
//         } else {
//             viewerContent.innerHTML = `
//                 <div class="bg-white rounded-lg p-6 text-center">
//                     <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
//                         <i class="fas fa-external-link-alt text-blue-600 text-2xl"></i>
//                     </div>
//                     <h3 class="text-xl font-semibold text-gray-800 mb-2">External Video</h3>
//                     <p class="text-gray-600 mb-4">This video is hosted externally.</p>
//                     <a href="${videoUrl}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
//                         Watch on External Site
//                     </a>
//                 </div>
//             `;
//         }
//     } catch (error) {
//         console.error('Error loading external video:', error);
//         showErrorViewer('Failed to load the external video.');
//     } finally {
//         viewerLoading.classList.add('hidden');
//     }
// }

async function loadFileContent(attachmentId, fileType, fileConfig, title, fileUrl) {
    const viewerContent = document.getElementById('viewerContent');
    const viewerLoading = document.getElementById('viewerLoading');
    
    if (!viewerContent || !viewerLoading) return;
    
    try {
        switch (fileConfig.type) {
            case 'video':
                viewerContent.innerHTML = createVideoPlayer(fileUrl, fileType, title);
                break;
            case 'audio':
                viewerContent.innerHTML = createAudioPlayer(fileUrl, fileType, title);
                break;
            case 'image':
                viewerContent.innerHTML = createImageViewer(fileUrl, title);
                break;
            case 'document':
                if (fileConfig.viewer === 'pdfjs' && fileType.toLowerCase() === 'pdf') {
                    viewerContent.innerHTML = createPDFViewer();
                    await loadPDFDocument(fileUrl, viewerContent);
                } else if (fileConfig.viewer === 'docx' && (fileType.toLowerCase() === 'doc' || fileType.toLowerCase() === 'docx')) {
                    await loadDocxDocument(fileUrl, viewerContent);
                } else if (fileConfig.viewer === 'text') {
                    await loadTextFile(fileUrl, viewerContent);
                } else {
                    viewerContent.innerHTML = createDocumentViewer(fileUrl, title, fileType);
                }
                break;
            default:
                showUnsupportedFileType(fileType, title);
        }
    } catch (error) {
        console.error('Error loading file:', error);
        showErrorViewer('Failed to load the file. Please try again later.');
    } finally {
        viewerLoading.classList.add('hidden');
    }
}

function createVideoPlayer(url, fileType, title) {
    return `
        <div class="bg-black rounded-lg overflow-hidden shadow-lg">
            <video controls controlsList="nodownload" class="w-full" style="max-height: 70vh;">
                <source src="${url}" type="${getVideoMimeType(fileType)}">
                Your browser does not support the video tag.
            </video>
        </div>
        <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
            <h4 class="font-semibold text-gray-800 mb-2">${title}</h4>
            <p class="text-sm text-gray-600">Video content - use player controls to navigate</p>
        </div>
    `;
}

function createAudioPlayer(url, fileType, title) {
    return `
        <div class="bg-white rounded-lg p-6 shadow-lg">
            <div class="text-center mb-4">
                <i class="fas fa-music text-4xl text-indigo-600 mb-3"></i>
                <h4 class="font-semibold text-gray-800">${title}</h4>
            </div>
            <audio controls controlsList="nodownload" class="w-full">
                <source src="${url}" type="${getAudioMimeType(fileType)}">
                Your browser does not support the audio element.
            </audio>
        </div>
    `;
}

function createImageViewer(url, title) {
    return `
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="text-center">
                <img src="${url}" alt="${title}" class="max-w-full max-h-96 mx-auto object-contain rounded-lg shadow-md" 
                     oncontextmenu="return false;" loading="lazy">
            </div>
            <div class="mt-4 text-center">
                <h4 class="font-semibold text-gray-800">${title}</h4>
                <p class="text-sm text-gray-600">Image preview</p>
            </div>
        </div>
    `;
}

function createPDFViewer() {
    return `
        <div class="bg-white rounded-lg shadow-sm">
            <div class="bg-gray-50 p-4 border-b">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <button id="prevPage" class="bg-gray-200 text-gray-700 px-3 py-1 rounded mr-2">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <span id="pageInfo" class="text-sm text-gray-600">Page: <span id="currentPage">1</span> / <span id="totalPages">0</span></span>
                        <button id="nextPage" class="bg-gray-200 text-gray-700 px-3 py-1 rounded ml-2">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                    <div class="flex items-center">
                        <span class="text-sm text-gray-600 mr-2">Zoom:</span>
                        <button id="zoomOut" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-l">-</button>
                        <span id="zoomLevel" class="bg-gray-100 px-3 py-1 text-sm">100%</span>
                        <button id="zoomIn" class="bg-gray-200 text-gray-700 px-3 py-1 rounded-r">+</button>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div id="pdfViewer" class="border rounded overflow-auto" style="height: 70vh;"></div>
            </div>
        </div>
    `;
}

function createDocumentViewer(url, title, fileType) {
    return `
        <div class="bg-white rounded-lg shadow-sm p-6 text-center">
            <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-file text-blue-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">${title}</h3>
            <p class="text-gray-600 mb-4">This document (${fileType.toUpperCase()}) can be viewed in your browser.</p>
            <a href="${url}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                Open Document
            </a>
            <p class="text-sm text-gray-500 mt-3">The document will open in a new tab</p>
        </div>
    `;
}

async function loadPDFDocument(url, container) {
    try {
        const pdfViewer = container.querySelector('#pdfViewer');
        const pdfDocument = await pdfjsLib.getDocument(url).promise;
        const totalPages = pdfDocument.numPages;
        
        container.querySelector('#totalPages').textContent = totalPages;
        
        let currentPage = 1;
        let currentScale = 1.0;
        
        const renderPage = async (pageNumber) => {
            const page = await pdfDocument.getPage(pageNumber);
            const viewport = page.getViewport({ scale: currentScale });
            
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            canvas.height = viewport.height;
            canvas.width = viewport.width;
            
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            
            await page.render(renderContext).promise;
            
            pdfViewer.innerHTML = '';
            pdfViewer.appendChild(canvas);
            container.querySelector('#currentPage').textContent = pageNumber;
            container.querySelector('#zoomLevel').textContent = Math.round(currentScale * 100) + '%';
        };
        
        await renderPage(currentPage);
        
        container.querySelector('#prevPage').addEventListener('click', async () => {
            if (currentPage > 1) {
                currentPage--;
                await renderPage(currentPage);
            }
        });
        
        container.querySelector('#nextPage').addEventListener('click', async () => {
            if (currentPage < totalPages) {
                currentPage++;
                await renderPage(currentPage);
            }
        });
        
        container.querySelector('#zoomIn').addEventListener('click', async () => {
            currentScale += 0.25;
            await renderPage(currentPage);
        });
        
        container.querySelector('#zoomOut').addEventListener('click', async () => {
            if (currentScale > 0.5) {
                currentScale -= 0.25;
                await renderPage(currentPage);
            }
        });
        
    } catch (error) {
        console.error('Error loading PDF:', error);
        showErrorViewer('Failed to load the PDF document.');
    }
}

async function loadDocxDocument(url, container) {
    try {
        const response = await fetch(url);
        const blob = await response.blob();
        
        container.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="bg-gray-50 p-3 rounded mb-4">
                    <h4 class="font-semibold text-gray-800">Document Preview</h4>
                </div>
                <div id="docx-container" class="bg-white p-4 border rounded overflow-auto" style="max-height: 70vh;"></div>
            </div>
        `;
        
        docx.renderAsync(blob, container.querySelector('#docx-container'));
    } catch (error) {
        console.error('Error loading DOCX:', error);
        showErrorViewer('Failed to load the document.');
    }
}

async function loadTextFile(url, container) {
    try {
        const response = await fetch(url);
        const text = await response.text();
        
        container.innerHTML = `
            <div class="bg-white rounded-lg shadow-sm p-4">
                <div class="bg-gray-50 p-3 rounded mb-4">
                    <h4 class="font-semibold text-gray-800">Text Content</h4>
                </div>
                <pre class="whitespace-pre-wrap font-mono text-sm bg-gray-50 p-4 rounded overflow-auto" style="max-height: 70vh;">${escapeHtml(text)}</pre>
            </div>
        `;
    } catch (error) {
        console.error('Error loading text file:', error);
        showErrorViewer('Failed to load the text file.');
    }
}

function showUnsupportedFileType(fileType, title) {
    const viewerContent = document.getElementById('viewerContent');
    
    viewerContent.innerHTML = `
        <div class="bg-white rounded-lg shadow-sm p-8 text-center max-w-md mx-auto">
            <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">File Type Not Supported</h3>
            <p class="text-gray-600 mb-4">${fileType.toUpperCase()} files cannot be previewed in the browser.</p>
            <div class="bg-gray-50 rounded-lg p-4 text-left">
                <p class="text-sm text-gray-600 mb-2">File: <strong>${title}</strong></p>
                <p class="text-sm text-gray-600 mb-2">Type: <strong>${fileType.toUpperCase()}</strong></p>
                <p class="text-sm text-gray-600">Please contact support if you need assistance with this file type.</p>
            </div>
        </div>
    `;
}

// ===== ENHANCED DOCUMENT VIEWER FUNCTIONS =====
function getDocumentIcon(fileType) {
    const icons = {
        'pdf': 'fa-file-pdf',
        'doc': 'fa-file-word',
        'docx': 'fa-file-word',
        'txt': 'fa-file-alt',
        'ppt': 'fa-file-powerpoint',
        'pptx': 'fa-file-powerpoint',
        'xls': 'fa-file-excel',
        'xlsx': 'fa-file-excel'
    };
    return icons[fileType.toLowerCase()] || 'fa-file';
}

function getDocumentTypeDescription(fileType) {
    const descriptions = {
        'pdf': 'Portable Document Format',
        'doc': 'Microsoft Word Document',
        'docx': 'Microsoft Word Document',
        'txt': 'Plain Text File',
        'ppt': 'Microsoft PowerPoint',
        'pptx': 'Microsoft PowerPoint',
        'xls': 'Microsoft Excel',
        'xlsx': 'Microsoft Excel'
    };
    return descriptions[fileType.toLowerCase()] || 'Document File';
}

function createDocumentViewerContent(fileUrl, fileType, title) {
    const fileExtension = fileType.toLowerCase();
    
    if (fileExtension === 'pdf') {
        return `
            <div class="h-full bg-white">
                <iframe src="${fileUrl}#toolbar=0&navpanes=0" 
                        class="w-full h-full border rounded" 
                        frameborder="0"
                        oncontextmenu="return false;"
                        onload="documentLoaded()">
                </iframe>
            </div>
        `;
    } else if (['doc', 'docx'].includes(fileExtension)) {
        return `
            <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                <div class="text-center">
                    <div class="bg-blue-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file-word text-blue-600 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">Word Document</h4>
                    <p class="text-gray-600 mb-4">Preview not available in browser</p>
                    <a href="${fileUrl}" 
                       class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download to View
                    </a>
                </div>
            </div>
        `;
    } else if (fileExtension === 'txt') {
        return `
            <div class="h-full bg-white border rounded">
                <iframe src="${fileUrl}" 
                        class="w-full h-full font-mono text-sm p-4"
                        frameborder="0"
                        oncontextmenu="return false;">
                </iframe>
            </div>
        `;
    } else {
        return `
            <div class="h-full flex items-center justify-center bg-gray-50 rounded">
                <div class="text-center">
                    <div class="bg-gray-100 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-file text-gray-600 text-3xl"></i>
                    </div>
                    <h4 class="text-lg font-semibold text-gray-800 mb-2">${fileExtension.toUpperCase()} Document</h4>
                    <p class="text-gray-600 mb-4">This document type is best viewed by downloading</p>
                    <a href="${fileUrl}" download
                       class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download Document
                    </a>
                </div>
            </div>
        `;
    }
}

// ===== DOCUMENT CONTROLS =====
function zoomDocument(direction) {
    const zoomElement = document.getElementById('zoomLevel');
    let currentZoom = parseInt(zoomElement.textContent);
    
    if (direction === 'in' && currentZoom < 200) {
        currentZoom += 25;
    } else if (direction === 'out' && currentZoom > 50) {
        currentZoom -= 25;
    }
    
    zoomElement.textContent = currentZoom + '%';
    
    // Apply zoom to iframe or content
    const iframe = document.querySelector('#activeContent iframe');
    if (iframe) {
        iframe.style.transform = `scale(${currentZoom / 100})`;
        iframe.style.transformOrigin = 'top left';
    }
}

function previousPage() {
    const iframe = document.querySelector('#activeContent iframe');
    if (iframe && iframe.contentWindow) {
        try {
            iframe.contentWindow.history.back();
        } catch (e) {
            console.log('Cannot navigate pages in this document');
        }
    }
}

function nextPage() {
    const iframe = document.querySelector('#activeContent iframe');
    if (iframe && iframe.contentWindow) {
        try {
            iframe.contentWindow.history.forward();
        } catch (e) {
            console.log('Cannot navigate pages in this document');
        }
    }
}

function toggleStudyTools() {
    const panel = document.getElementById('studyToolsPanel');
    if (panel) {
        panel.classList.toggle('hidden');
    }
}

function openInFullscreen(fileUrl, fileType) {
    const fullscreenHtml = `
        <!DOCTYPE html>
        <html>
        <head>
            <title>${document.title}</title>
            <style>
                body { margin: 0; padding: 20px; background: #f5f5f5; }
                .toolbar { 
                    position: fixed; 
                    top: 10px; 
                    right: 10px; 
                    background: white; 
                    padding: 10px; 
                    border-radius: 8px; 
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    z-index: 1000;
                }
                iframe { 
                    width: 100%; 
                    height: 100vh; 
                    border: none; 
                    background: white;
                }
            </style>
        </head>
        <body>
            <div class="toolbar">
                <button onclick="window.close()" style="background: #ef4444; color: white; border: none; padding: 8px 16px; border-radius: 4px; cursor: pointer;">
                    Close
                </button>
            </div>
            <iframe src="${fileUrl}"></iframe>
        </body>
        </html>
    `;
    
    const fullscreenWindow = window.open('', '_blank', 'fullscreen=yes');
    fullscreenWindow.document.write(fullscreenHtml);
    fullscreenWindow.document.close();
}

function printDocument(fileUrl) {
    const printWindow = window.open(fileUrl, '_blank');
    printWindow.onload = function() {
        printWindow.print();
    };
}

function addBookmark() {
    const activeContent = document.getElementById('activeContent');
    const title = activeContent.querySelector('h3')?.textContent || 'Document';
    
    STUDY_TOOLS.bookmarks[`doc_${Date.now()}`] = {
        title: title,
        type: 'document',
        timestamp: new Date().toISOString(),
        url: window.location.href
    };
    
    saveStudyTools();
    showToast('Document bookmarked!', 'success');
}

function openNotes() {
    const modal = document.getElementById('studyToolsModal');
    const title = document.getElementById('studyToolsTitle');
    const content = document.getElementById('studyToolsContent');
    
    if (modal && title && content) {
        const docTitle = document.querySelector('#activeContent h3')?.textContent || 'Current Document';
        title.textContent = `Notes for ${docTitle}`;
        
        const noteId = `doc_notes_${btoa(docTitle)}`;
        content.innerHTML = `
            <textarea class="w-full h-64 p-4 border rounded-lg resize-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent" 
                      placeholder="Write your notes about this document...">${STUDY_TOOLS.notes[noteId] || ''}</textarea>
            <div class="flex justify-between mt-4">
                <button onclick="saveDocumentNotes('${noteId}')" class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Save Notes
                </button>
                <button onclick="closeStudyTools()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400 transition">
                    Cancel
                </button>
            </div>
        `;
        
        modal.classList.remove('hidden');
    }
}

function saveDocumentNotes(noteId) {
    const textarea = document.querySelector('#studyToolsContent textarea');
    if (textarea) {
        STUDY_TOOLS.notes[noteId] = textarea.value;
        saveStudyTools();
        showToast('Notes saved successfully!', 'success');
        closeStudyTools();
    }
}

function createFlashcard() {
    showToast('Create a flashcard from this document content', 'info');
    // Implementation for flashcard creation would go here
}

function documentLoaded() {
    console.log('Document loaded successfully');
    // Additional initialization when document is loaded
}

function showErrorViewer(message) {
    const viewerContent = document.getElementById('viewerContent');
    
    viewerContent.innerHTML = `
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-800 mb-2">Error Loading Content</h3>
            <p class="text-gray-600 mb-4">${message}</p>
            <button onclick="closeResourceViewer()" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                Close Viewer
            </button>
        </div>
    `;
}

function closeResourceViewer() {
    const viewer = document.getElementById('resourceViewer');
    if (viewer) {
        viewer.classList.add('hidden');
        document.body.style.overflow = 'auto';
        
        // Clean up
        const videos = viewer.querySelectorAll('video');
        videos.forEach(video => {
            video.pause();
            video.src = '';
        });
        
        const audios = viewer.querySelectorAll('audio');
        audios.forEach(audio => {
            audio.pause();
            audio.src = '';
        });
        
        const iframes = viewer.querySelectorAll('iframe');
        iframes.forEach(iframe => {
            iframe.src = '';
        });
    }
}

// ===== STUDY TOOLS FUNCTIONS =====
function initStudyTools() {
    try {
        const savedNotes = localStorage.getItem('study_notes');
        const savedBookmarks = localStorage.getItem('study_bookmarks');
        const savedFlashcards = localStorage.getItem('study_flashcards');
        
        if (savedNotes) STUDY_TOOLS.notes = JSON.parse(savedNotes);
        if (savedBookmarks) STUDY_TOOLS.bookmarks = JSON.parse(savedBookmarks);
        if (savedFlashcards) STUDY_TOOLS.flashcards = JSON.parse(savedFlashcards);
    } catch (error) {
        console.error('Error loading study tools:', error);
        STUDY_TOOLS.notes = {};
        STUDY_TOOLS.bookmarks = {};
        STUDY_TOOLS.flashcards = {};
        saveStudyTools();
    }
}

function saveStudyTools() {
    try {
        localStorage.setItem('study_notes', JSON.stringify(STUDY_TOOLS.notes));
        localStorage.setItem('study_bookmarks', JSON.stringify(STUDY_TOOLS.bookmarks));
        localStorage.setItem('study_flashcards', JSON.stringify(STUDY_TOOLS.flashcards));
    } catch (error) {
        console.error('Error saving study tools:', error);
    }
}

function takeNotes(moduleId) {
    const modal = document.getElementById('studyToolsModal');
    const title = document.getElementById('studyToolsTitle');
    const content = document.getElementById('studyToolsContent');
    
    if (!modal || !title || !content) {
        showToast('Study tools not available. Please refresh the page.', 'error');
        return;
    }
    
    title.textContent = 'Notes for Module';
    content.innerHTML = `
        <textarea class="w-full h-48 p-3 border rounded resize-none" 
                  placeholder="Write your notes here...">${STUDY_TOOLS.notes[moduleId] || ''}</textarea>
        <div class="flex justify-end mt-3">
            <button onclick="saveModuleNotes('${moduleId}')" class="bg-indigo-600 text-white px-4 py-2 rounded">
                Save Notes
            </button>
        </div>
    `;
    
    modal.classList.remove('hidden');
}

function saveModuleNotes(moduleId) {
    const textarea = document.querySelector('#studyToolsContent textarea');
    if (textarea) {
        STUDY_TOOLS.notes[moduleId] = textarea.value;
        saveStudyTools();
        showToast('Notes saved successfully!', 'success');
        closeStudyTools();
    }
}

function takeNotesFromViewer() {
    const notesPanel = document.getElementById('notesPanel');
    if (notesPanel) {
        notesPanel.classList.toggle('hidden');
    }
}

function saveNotes() {
    const viewer = document.getElementById('resourceViewer');
    const notesTextarea = document.getElementById('resourceNotes');
    
    if (viewer && notesTextarea) {
        const attachmentId = viewer.dataset.attachmentId;
        const notesText = notesTextarea.value;
        
        STUDY_TOOLS.notes[attachmentId] = notesText;
        saveStudyTools();
        showToast('Notes saved successfully!', 'success');
    }
}

function loadExistingNotes(attachmentId) {
    const notesTextarea = document.getElementById('resourceNotes');
    if (notesTextarea) {
        notesTextarea.value = STUDY_TOOLS.notes[attachmentId] || '';
    }
}

function bookmarkCurrentResource() {
    const viewer = document.getElementById('resourceViewer');
    if (viewer) {
        const attachmentId = viewer.dataset.attachmentId;
        const resourceType = viewer.dataset.resourceType;
        const title = document.getElementById('viewerTitle').textContent;
        
        STUDY_TOOLS.bookmarks[attachmentId] = {
            title: title,
            type: resourceType,
            timestamp: new Date().toISOString()
        };
        
        saveStudyTools();
        showToast('Bookmark added!', 'success');
    }
}

// ===== UTILITY FUNCTIONS =====
function toggleModule(moduleId) {
    const moduleAttachments = document.getElementById(`moduleAttachments-${moduleId}`);
    const moduleIcon = document.getElementById(`moduleIcon-${moduleId}`);
    
    if (moduleAttachments && moduleIcon) {
        if (moduleAttachments.classList.contains('hidden')) {
            moduleAttachments.classList.remove('hidden');
            moduleIcon.classList.remove('fa-chevron-down');
            moduleIcon.classList.add('fa-chevron-up');
        } else {
            moduleAttachments.classList.add('hidden');
            moduleIcon.classList.remove('fa-chevron-up');
            moduleIcon.classList.add('fa-chevron-down');
        }
    }
}

function toggleResourceView() {
    const gridView = document.getElementById('resourceGrid');
    const listView = document.getElementById('resourceList');
    
    if (gridView && listView) {
        if (gridView.classList.contains('hidden')) {
            gridView.classList.remove('hidden');
            listView.classList.add('hidden');
            showToast('Grid view enabled', 'info');
        } else {
            gridView.classList.add('hidden');
            listView.classList.remove('hidden');
            showToast('List view enabled', 'info');
        }
    }
}

function isVideoFile(fileType) {
    const videoTypes = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv'];
    return videoTypes.includes(fileType.toLowerCase());
}

function isImageFile(fileType) {
    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'];
    return imageTypes.includes(fileType.toLowerCase());
}

function isDocumentFile(fileType) {
    const documentTypes = ['pdf', 'doc', 'docx', 'txt'];
    return documentTypes.includes(fileType.toLowerCase());
}

function extractYouTubeId(url) {
    const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[7].length === 11) ? match[7] : null;
}

function getVideoMimeType(extension) {
    const types = {
        'mp4': 'video/mp4',
        'mov': 'video/quicktime',
        'avi': 'video/x-msvideo',
        'mkv': 'video/x-matroska',
        'webm': 'video/webm',
        'wmv': 'video/x-ms-wmv'
    };
    return types[extension.toLowerCase()] || 'video/mp4';
}

function getAudioMimeType(extension) {
    const types = {
        'mp3': 'audio/mpeg',
        'wav': 'audio/wav',
        'ogg': 'audio/ogg',
        'm4a': 'audio/mp4'
    };
    return types[extension.toLowerCase()] || 'audio/mpeg';
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// ===== TOAST AND MODAL FUNCTIONS =====
function showToast(message, type = 'info') {
    document.querySelectorAll('[id^="toast-"]').forEach(toast => toast.remove());
    
    const toastId = 'toast-' + Date.now();
    const toast = document.createElement('div');
    toast.id = toastId;
    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-60 transform transition-transform duration-300 translate-x-full ${getToastColor(type)}`;
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="${getToastIcon(type)} mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (document.getElementById(toastId)) {
                document.getElementById(toastId).remove();
            }
        }, 300);
    }, 3000);
}

function getToastColor(type) {
    const colors = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    return colors[type] || colors.info;
}

function getToastIcon(type) {
    const icons = {
        success: 'fas fa-check-circle',
        error: 'fas fa-exclamation-circle',
        warning: 'fas fa-exclamation-triangle',
        info: 'fas fa-info-circle'
    };
    return icons[type] || icons.info;
}

function closeStudyTools() {
    const modal = document.getElementById('studyToolsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

function hideNotesPanel() {
    const notesPanel = document.getElementById('notesPanel');
    if (notesPanel) {
        notesPanel.classList.add('hidden');
    }
}

function showSecurityToast(message) {
    const existingToast = document.getElementById('securityToast');
    if (existingToast) existingToast.remove();
    
    const toast = document.createElement('div');
    toast.id = 'securityToast';
    toast.className = 'fixed top-4 right-4 bg-red-500 text-white px-4 py-2 rounded-lg shadow-lg z-60 transform transition-transform duration-300 translate-x-full';
    toast.innerHTML = `
        <div class="flex items-center">
            <i class="fas fa-shield-alt mr-2"></i>
            <span>${message}</span>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('translate-x-0');
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// ===== PLACEHOLDER FUNCTIONS =====
function openAIAssistant() {
    showToast('AI Assistant feature coming soon!', 'info');
}

function takeCourseNotes() {
    showToast('Course Notes feature coming soon!', 'info');
}

function openCourseBookmarks() {
    showToast('Course Bookmarks feature coming soon!', 'info');
}

function openCourseFlashcards() {
    showToast('Course Flashcards feature coming soon!', 'info');
}

function downloadCourseResources() {
    showToast('Download Resources feature coming soon!', 'info');
}

function bookmarkCourse(courseId) {
    STUDY_TOOLS.bookmarks[`course_${courseId}`] = {
        title: 'Course Bookmark',
        type: 'course',
        timestamp: new Date().toISOString()
    };
    saveStudyTools();
    showToast('Course bookmarked!', 'success');
}

function shareCourse(courseId) {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this course',
            text: 'I found this amazing course on our learning platform!',
            url: window.location.origin + '/courses/' + courseId
        }).catch(error => {
            console.log('Error sharing:', error);
            showToast('Sharing failed. Please try again.', 'error');
        });
    } else {
        const shareUrl = window.location.origin + '/courses/' + courseId;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shareUrl)
                .then(() => showToast('Course link copied to clipboard!', 'success'))
                .catch(err => showToast('Failed to copy link. Please try again.', 'error'));
        } else {
            const tempInput = document.createElement('input');
            tempInput.value = shareUrl;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            showToast('Course link copied to clipboard!', 'success');
        }
    }
}

function bookmarkModule(moduleId) {
    STUDY_TOOLS.bookmarks[`module_${moduleId}`] = {
        title: 'Module Bookmark',
        type: 'module',
        timestamp: new Date().toISOString()
    };
    saveStudyTools();
    showToast('Module bookmarked!', 'success');
}

function createFlashcards(moduleId) {
    showToast('Flashcards feature coming soon!', 'info');
}
    </script>
</body>
@endsection
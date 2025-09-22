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

// Enhanced Resource Viewer with Study Tools
const STUDY_TOOLS = {
    notes: {},
    bookmarks: {},
    flashcards: {}
};

// Initialize study tools from localStorage
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
        // Reset if corrupted
        STUDY_TOOLS.notes = {};
        STUDY_TOOLS.bookmarks = {};
        STUDY_TOOLS.flashcards = {};
        saveStudyTools();
    }
}

// Save study tools to localStorage
function saveStudyTools() {
    try {
        localStorage.setItem('study_notes', JSON.stringify(STUDY_TOOLS.notes));
        localStorage.setItem('study_bookmarks', JSON.stringify(STUDY_TOOLS.bookmarks));
        localStorage.setItem('study_flashcards', JSON.stringify(STUDY_TOOLS.flashcards));
    } catch (error) {
        console.error('Error saving study tools:', error);
    }
}

// Extract YouTube ID from URL
function extractYouTubeId(url) {
    const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
    const match = url.match(regExp);
    return (match && match[7].length === 11) ? match[7] : null;
}

// Enhanced resource viewer with external video support
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

function loadExternalVideo(videoUrl, title) {
    const viewerContent = document.getElementById('viewerContent');
    const viewerLoading = document.getElementById('viewerLoading');
    
    try {
        // Check if it's YouTube
        if (videoUrl.includes('youtube.com') || videoUrl.includes('youtu.be')) {
            const videoId = extractYouTubeId(videoUrl);
            if (videoId) {
                viewerContent.innerHTML = `
                    <div class="bg-black rounded-lg overflow-hidden shadow-lg">
                        <iframe 
                            src="https://www.youtube.com/embed/${videoId}?rel=0&modestbranding=1" 
                            class="w-full h-96" 
                            frameborder="0" 
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                            allowfullscreen>
                        </iframe>
                    </div>
                    <div class="mt-4 bg-white rounded-lg p-4 shadow-sm">
                        <h4 class="font-semibold text-gray-800 mb-2">${title}</h4>
                        <p class="text-sm text-gray-600">YouTube video - use player controls to navigate</p>
                    </div>
                `;
            } else {
                throw new Error('Invalid YouTube URL');
            }
        } else {
            // Generic video embed
            viewerContent.innerHTML = `
                <div class="bg-white rounded-lg p-6 text-center">
                    <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-external-link-alt text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">External Video</h3>
                    <p class="text-gray-600 mb-4">This video is hosted externally.</p>
                    <a href="${videoUrl}" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                        Watch on External Site
                    </a>
                </div>
            `;
        }
    } catch (error) {
        console.error('Error loading external video:', error);
        showErrorViewer('Failed to load the external video.');
    } finally {
        viewerLoading.classList.add('hidden');
    }
}

async function loadFileContent(attachmentId, fileType, fileConfig, title, fileUrl) {
    const viewerContent = document.getElementById('viewerContent');
    const viewerLoading = document.getElementById('viewerLoading');
    
    if (!viewerContent || !viewerLoading) {
        console.error('Viewer elements not found');
        return;
    }
    
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
        
        // Render first page
        await renderPage(currentPage);
        
        // Add event listeners
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
        
        // Render DOCX using docx-preview library
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

// Study Tools Functions
function takeNotes(moduleId) {
    const modal = document.getElementById('studyToolsModal');
    const title = document.getElementById('studyToolsTitle');
    const content = document.getElementById('studyToolsContent');
    
    if (!modal || !title || !content) {
        console.error('Study tools modal elements not found');
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

// Toggle between grid and list view for resources
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

// Bookmark course
function bookmarkCourse(courseId) {
    STUDY_TOOLS.bookmarks[`course_${courseId}`] = {
        title: 'Course Bookmark',
        type: 'course',
        timestamp: new Date().toISOString()
    };
    saveStudyTools();
    showToast('Course bookmarked!', 'success');
}

// Share course
function shareCourse(courseId) {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this course',
            text: 'I found this amazing course on our learning platform!',
            url: window.location.origin + '/courses/' + courseId
        })
        .catch(error => {
            console.log('Error sharing:', error);
            showToast('Sharing failed. Please try again.', 'error');
        });
    } else {
        // Fallback for browsers that don't support Web Share API
        const shareUrl = window.location.origin + '/courses/' + courseId;
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(shareUrl)
                .then(() => {
                    showToast('Course link copied to clipboard!', 'success');
                })
                .catch(err => {
                    console.error('Failed to copy: ', err);
                    showToast('Failed to copy link. Please try again.', 'error');
                });
        } else {
            // Fallback for older browsers
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

// Show toast notifications
function showToast(message, type = 'info') {
    // Remove existing toasts
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
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 10);
    
    // Remove after 3 seconds
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

// Close study tools modal
function closeStudyTools() {
    const modal = document.getElementById('studyToolsModal');
    if (modal) {
        modal.classList.add('hidden');
    }
}

// Hide notes panel
function hideNotesPanel() {
    const notesPanel = document.getElementById('notesPanel');
    if (notesPanel) {
        notesPanel.classList.add('hidden');
    }
}

// Security measures
function showSecurityToast(message) {
    // Remove existing toast if any
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
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
        toast.classList.add('translate-x-0');
    }, 10);
    
    // Remove after 3 seconds
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

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing Learning Dashboard...');
    initStudyTools();
    
    // Add event listeners for security
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
    
    // Initialize any collapsed modules
    const firstModule = document.querySelector('.module-content');
    if (firstModule) {
        const firstModuleId = firstModule.id.split('-')[1];
        toggleModule(firstModuleId);
    }
    
    console.log('Learning Dashboard initialized successfully');
});

// Module toggle function (if not already defined)
function toggleModule(moduleId) {
    const moduleContent = document.getElementById(`module-${moduleId}`);
    const icon = document.getElementById(`icon-${moduleId}`);
    
    if (moduleContent && icon) {
        if (moduleContent.classList.contains('collapsed')) {
            moduleContent.classList.remove('collapsed');
            moduleContent.classList.add('expanded');
            icon.classList.remove('fa-caret-down');
            icon.classList.add('fa-caret-up');
        } else {
            moduleContent.classList.remove('expanded');
            moduleContent.classList.add('collapsed');
            icon.classList.remove('fa-caret-up');
            icon.classList.add('fa-caret-down');
        }
    }
}

// Additional study tool functions
function openStudyTools() {
    const modal = document.getElementById('studyToolsModal');
    const title = document.getElementById('studyToolsTitle');
    const content = document.getElementById('studyToolsContent');
    
    if (modal && title && content) {
        title.textContent = 'Study Tools';
        content.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-blue-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-blue-800 mb-2">Notes</h4>
                    <p class="text-blue-600 text-sm">Access and manage your study notes</p>
                    <button onclick="showAllNotes()" class="mt-2 bg-blue-600 text-white px-3 py-1 rounded text-sm">
                        View All Notes
                    </button>
                </div>
                <div class="bg-green-50 p-4 rounded-lg">
                    <h4 class="font-semibold text-green-800 mb-2">Bookmarks</h4>
                    <p class="text-green-600 text-sm">Access your saved bookmarks</p>
                    <button onclick="showAllBookmarks()" class="mt-2 bg-green-600 text-white px-3 py-1 rounded text-sm">
                        View Bookmarks
                    </button>
                </div>
            </div>
        `;
        modal.classList.remove('hidden');
    }
}

function showAllNotes() {
    const modal = document.getElementById('studyToolsModal');
    const title = document.getElementById('studyToolsTitle');
    const content = document.getElementById('studyToolsContent');
    
    if (modal && title && content) {
        title.textContent = 'All Notes';
        let notesHtml = '<div class="space-y-3 max-h-96 overflow-y-auto">';
        
        if (Object.keys(STUDY_TOOLS.notes).length === 0) {
            notesHtml += '<p class="text-gray-500 text-center py-4">No notes yet. Start taking notes on resources!</p>';
        } else {
            for (const [id, note] of Object.entries(STUDY_TOOLS.notes)) {
                if (note.trim()) {
                    notesHtml += `
                        <div class="bg-white border rounded p-3">
                            <p class="text-sm text-gray-700">${note}</p>
                            <div class="flex justify-between items-center mt-2">
                                <span class="text-xs text-gray-500">ID: ${id}</span>
                                <button onclick="deleteNote('${id}')" class="text-red-600 text-xs">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                }
            }
        }
        
        notesHtml += '</div>';
        content.innerHTML = notesHtml;
    }
}

function deleteNote(noteId) {
    delete STUDY_TOOLS.notes[noteId];
    saveStudyTools();
    showAllNotes();
    showToast('Note deleted', 'success');
}

function showAllBookmarks() {
    const modal = document.getElementById('studyToolsModal');
    const title = document.getElementById('studyToolsTitle');
    const content = document.getElementById('studyToolsContent');
    
    if (modal && title && content) {
        title.textContent = 'All Bookmarks';
        let bookmarksHtml = '<div class="space-y-3 max-h-96 overflow-y-auto">';
        
        if (Object.keys(STUDY_TOOLS.bookmarks).length === 0) {
            bookmarksHtml += '<p class="text-gray-500 text-center py-4">No bookmarks yet. Start bookmarking resources!</p>';
        } else {
            for (const [id, bookmark] of Object.entries(STUDY_TOOLS.bookmarks)) {
                bookmarksHtml += `
                    <div class="bg-white border rounded p-3">
                        <h4 class="font-semibold text-sm">${bookmark.title}</h4>
                        <p class="text-xs text-gray-500">Type: ${bookmark.type}</p>
                        <p class="text-xs text-gray-500">Saved: ${new Date(bookmark.timestamp).toLocaleDateString()}</p>
                        <div class="flex justify-end mt-2">
                            <button onclick="deleteBookmark('${id}')" class="text-red-600 text-xs">
                                <i class="fas fa-trash"></i> Remove
                            </button>
                        </div>
                    </div>
                `;
            }
        }
        
        bookmarksHtml += '</div>';
        content.innerHTML = bookmarksHtml;
    }
}

function deleteBookmark(bookmarkId) {
    delete STUDY_TOOLS.bookmarks[bookmarkId];
    saveStudyTools();
    showAllBookmarks();
    showToast('Bookmark removed', 'success');
}
    </script>
</body>
@endsection
@extends('layouts.app')

@section('content')
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $course->title }} - Course Preview</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    <script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>

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
    </style>
</head>
<body class="bg-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Course Header -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-6">
                <div class="md:w-1/3">
                    <div class="h-48 bg-gray-200 rounded-lg overflow-hidden">
                        @if($course->image)
                            <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                                <i class="fas fa-book text-4xl text-indigo-600"></i>
                            </div>
                        @endif
                    </div>
                    
                    <div class="mt-4">
                        <p class="text-2xl font-bold text-indigo-600">${{ number_format($course->price, 2) }}</p>
                        
                        @if($userHasPurchased)
                            <div class="mt-4 bg-green-100 text-green-800 p-3 rounded-lg">
                                <i class="fas fa-check-circle mr-2"></i> You already own this course
                            </div>
                            <a href="{{ route('userdashboard') }}" class="block mt-4 mb-8 text-center bg-indigo-600 text-white py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                Access Course
                            </a>
                        @else
                            <form action="{{ route('payment.initiate', $course) }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full bg-indigo-600 mb-8 text-white py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                    Purchase Course - ${{ number_format($course->price, 2) }}
                                </button>
                            </form>
                        @endif

                           <!-- Quick Stats -->
              
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Status:</span>
                        <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $course->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Price:</span>
                        <span class="font-semibold">${{ number_format($course->price, 2) }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Level:</span>
                        <span class="font-semibold">
                            @if($course->level == 1) Beginner
                            @elseif($course->level == 2) Intermediate
                            @else Advanced
                            @endif
                        </span>
                    </div>
                    @if($course->duration)
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Duration:</span>
                        <span class="font-semibold">{{ $course->duration }} hours</span>
                    </div>
                    @endif
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Modules:</span>
                        <span class="font-semibold">{{ $course->modules->count() }}</span>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Created:</span>
                        <span class="font-semibold">{{ $course->created_at->format('M d, Y') }}</span>
                    </div>
                     <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-gray-600">Updated:</span>
                        <span class="font-semibold">{{ $course->updated_at->format('M d, Y') }}</span>
                    </div>
               
           

                        <!-- Development Testing Section (Remove in production) -->
                        @if(auth()->check() && !$userHasPurchased)
                            <div class="mt-4 p-4 bg-yellow-100 border border-yellow-400 rounded-lg">
                                <h3 class="font-semibold text-yellow-800">Development Testing</h3>
                                <p class="text-yellow-700 text-sm mb-2">Use these buttons to test purchase functionality:</p>
                                
                                <div class="flex flex-col space-y-2">
                                    <form action="{{ route('purchase.course', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full bg-green-600 text-white px-3 py-2 rounded text-sm">
                                            Simulate Purchase (${{ number_format($course->price, 2) }})
                                        </button>
                                    </form>
                                    
                                    <a href="{{ route('quick.purchase', $course) }}" 
                                    class="text-center bg-blue-600 text-white px-3 py-2 rounded text-sm">
                                        Quick Free Purchase (Test)
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                
                <div class="md:w-2/3">
                    <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $course->title }}</h1>
                    <p class="text-gray-600 mb-6">{{ $course->description }}</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        @if($course->duration)
                        <div class="flex items-center">
                            <i class="fas fa-clock text-indigo-600 mr-2"></i>
                            <span>{{ $course->duration }} hours of content</span>
                        </div>
                        @endif
                        
                        <div class="flex items-center">
                            <i class="fas fa-layer-group text-indigo-600 mr-2"></i>
                            <span>{{ $course->modules->count() }} modules</span>
                        </div>
                        
                        @if($course->level)
                        <div class="flex items-center">
                            <i class="fas fa-signal text-indigo-600 mr-2"></i>
                            <span>
                                @if($course->level == 1) Beginner
                                @elseif($course->level == 2) Intermediate
                                @else Advanced
                                @endif
                            </span>
                        </div>
                        @endif
                    </div>
                    

                        <!-- Course Metadata -->
                    @if($course->objectives)
                    <div class="mb-8">
                        <h3 class="text-xl font-semibold mb-2">What you'll learn</h3>
                        <div class="prose">
                            {!! nl2br(e($course->objectives)) !!}
                        </div>
                    </div>
                    @endif

                 

                    
                    @if($course->target_audience)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Target Audience</h3>
                        <div class="prose max-w-none">
                            {!! nl2br(e($course->target_audience)) !!}
                        </div>
                    </div>
                    @endif
                    
                    @if($course->requirements)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">Requirements</h3>
                        <div class="prose max-w-none">
                            {!! nl2br(e($course->requirements)) !!}
                        </div>
                    </div>
                    @endif
               
                </div>
            </div>
        </div>
        
        <!-- Course Curriculum -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Course Content Aranged in Modules</h2>
            
            <div class="flex items-center text-sm text-gray-600 mb-4">
                <span class="mr-4">{{ $course->modules->count() }} sections • {{ $course->modules->sum(fn($module) => $module->attachments->count()) }} lectures • {{ $course->duration ?? 'All' }} total length</span>
            </div>


            <?php

// Calculate module duration based on attachments
function calculateModuleDuration($module) {
    $totalMinutes = 0;
    
    foreach ($module->attachments as $attachment) {
        $totalMinutes += getAttachmentDurationInMinutes($attachment);
    }
    
    return $totalMinutes;
}

// Get file duration for display
function getFileDuration($attachment) {
    if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv'])) {
        $duration = getAttachmentDurationInMinutes($attachment);
        $minutes = floor($duration);
        $seconds = round(($duration - $minutes) * 60);
        return sprintf('%d:%02d', $minutes, $seconds);
    } elseif ($attachment->file_type === 'pdf') {
        $pageCount = $attachment->metadata['page_count'] ?? 1;
        return $pageCount . ' page' . ($pageCount != 1 ? 's' : '');
    } else {
        return '5:00'; // Default duration for other file types
    }
}

// Get attachment duration in minutes (pseudo-implementation)
function getAttachmentDurationInMinutes($attachment) {
    // This would typically read metadata from the file
    // For now, return a random value for demonstration
    if (in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv'])) {
        return rand(5, 30); // Random between 5-30 minutes for videos
    } else {
        return rand(1, 10); // Random between 1-10 minutes for other files
    }
}

?>
            
            <div class="space-y-3">
                @foreach($course->modules->sortBy('order') as $module)
                <div class="border rounded-lg overflow-hidden">
                    <div class="bg-gray-200 px-4 py-3 flex justify-between items-center cursor-pointer" onclick="toggleModule({{ $module->id }})">
                        <div class="flex rounded items-center">
                            <i class="fas fa-caret-down mr-2 text-gray-500" id="icon-{{ $module->id }}"></i>
                            <h3 class="font-semibold text-2xl">
                                <span class="text-blue-500">Module {{ $loop->iteration }}:</span> 
                                <span class="text-black">{{ $module->title }}</span>
                            </h3>
                        </div>


                        <div class="flex items-center">
                            <span class="text-sm text-gray-600 mr-3">{{ $module->attachments->count() }} lectures • {{ calculateModuleDuration($module) }} min</span>
                            @if($module->is_free)
                                <span class="bg-green-300 text-green-800 text-xs px-2 py-1 rounded">Free Preview</span>
                            @elseif(!$userHasPurchased)
                                <span class="bg-gray-100 text-gray-800 text-xs px-2 py-1 rounded"><i class="fas fa-lock mr-1"></i> Locked</span>
                          
                             @elseif($userHasPurchased)
                                <span class="bg-green-400 text-green-800 text-xs px-2 py-1 rounded"> access course</span>
                            @endif
                            
                        </div>
                    </div>
                    
                    <div class="module-content collapsed" id="module-{{ $module->id }}">
                        <div class="p-4 bg-white">
                            @if($module->description)
                                <p class="text-gray-600 mb-3">{{ $module->description }}</p>
                            @endif
                            
                            @if($module->attachments->count() > 0)
                            <div class="mt-3 space-y-3">
                                @foreach($module->attachments->sortBy('order') as $index => $attachment)
                                <div class="flex items-start p-3 rounded-lg border attachment-card transition-all duration-200">
                                    <div class="flex-shrink-0 mr-4 relative">
                                        @if(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                            <div class="video-thumbnail w-32 h-20 bg-gray-800 rounded overflow-hidden" 
                                                 @if($module->is_free || $userHasPurchased) 
                                                 onclick="openVideoModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')"
                                                 @else
                                                 onclick="showPurchaseMessage()"
                                                 @endif>
                                                @if($module->is_free || $userHasPurchased)
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-700">
                                                        <i class="fas fa-play text-white text-xl"></i>
                                                    </div>
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-700">
                                                        <i class="fas fa-lock text-gray-400 text-xl"></i>
                                                    </div>
                                                    <div class="purchase-overlay">
                                                        <i class="fas fa-shopping-cart mb-1"></i>
                                                        <span class="text-xs">Purchase to access</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif($attachment->file_type === 'pdf')
                                            <div class="w-32 h-20 bg-red-100 rounded flex items-center justify-center relative"
                                                 @if(!$module->is_free && !$userHasPurchased) onclick="showPurchaseMessage()" @endif>
                                                <i class="fas fa-file-pdf text-red-600 text-3xl"></i>
                                                @if(!$module->is_free && !$userHasPurchased)
                                                    <div class="purchase-overlay">
                                                        <i class="fas fa-shopping-cart mb-1"></i>
                                                        <span class="text-xs">Purchase to access</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                            <div class="w-32 h-20 bg-gray-200 rounded overflow-hidden relative"
                                                 @if(!$module->is_free && !$userHasPurchased) onclick="showPurchaseMessage()" @endif>
                                                @if($module->is_free || $userHasPurchased)
                                                    <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->title }}" class="w-full h-full object-cover">
                                                @else
                                                    <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                                        <i class="fas fa-lock text-gray-500 text-xl"></i>
                                                    </div>
                                                    <div class="purchase-overlay">
                                                        <i class="fas fa-shopping-cart mb-1"></i>
                                                        <span class="text-xs">Purchase to access</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="w-32 h-20 bg-gray-100 rounded flex items-center justify-center relative"
                                                 @if(!$module->is_free && !$userHasPurchased) onclick="showPurchaseMessage()" @endif>
                                                <i class="fas fa-file text-gray-600 text-3xl"></i>
                                                @if(!$module->is_free && !$userHasPurchased)
                                                    <div class="purchase-overlay">
                                                        <i class="fas fa-shopping-cart mb-1"></i>
                                                        <span class="text-xs">Purchase to access</span>
                                                    </div>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <div class="flex-grow">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <h4 class="font-medium text-gray-800">{{ $index + 1 }}. {{ $attachment->title }}</h4>
                                                <p class="text-sm text-gray-600 mt-1">
                                                    @if(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                                        <i class="fas fa-play-circle mr-1"></i> Video
                                                    @elseif($attachment->file_type === 'pdf')
                                                        <i class="fas fa-file-pdf mr-1"></i> PDF
                                                    @elseif($attachment->file_type === 'doc' || $attachment->file_type === 'docx')
                                                        <i class="fas fa-file-pdf mr-1"></i> DOC
                                                    @elseif($attachment->file_type === 'zip')
                                                        <i class="fas fa-file-archive mr-1"></i> ZIP
                                                    @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                                        <i class="fas fa-image mr-1"></i> Image
                                                    @else
                                                        <i class="fas fa-file mr-1"></i> File
                                                    @endif
                                                    • {{ getFileDuration($attachment) }}
                                                </p>
                                            </div>
                                            
                                            <div class="flex items-center">
                                                @if($module->is_free)
                                                    {{-- Free modules can be accessed directly --}}
                                                    @if(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                                        <button onclick="openVideoModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                                            <i class="fas fa-play-circle text-lg"></i> Play
                                                        </button>
                                                    @else
                                                        <button onclick="openContentModal('{{ $attachment->file_type }}', '{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')" class="text-indigo-600 hover:text-indigo-800 mr-3">
                                                            <i class="fas fa-eye text-lg"></i> View
                                                        </button>
                                                    @endif

                                                @elseif($userHasPurchased)
                                                    {{-- Purchased, but should only be accessed in dashboard --}}
                                                    <a href="{{ route('userdashboard', $course->id) }}" 
                                                    class="text-indigo-600 hover:text-indigo-800 mr-3">
                                                        <i class="fas fa-sign-in-alt text-lg"></i> Access Course
                                                    </a>

                                                @else
                                                    {{-- Locked --}}
                                                    <span class="text-gray-400">
                                                        <i class="fas fa-lock text-lg"></i>
                                                    </span>
                                                @endif
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                @endforeach

                                
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Video Modal -->
        <div id="videoModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg overflow-hidden w-full max-w-4xl mx-4">
                <div class="relative pt-[56.25%]"> <!-- 16:9 aspect ratio -->
                    <video id="modalVideo" controls class="absolute inset-0 w-full h-full bg-black" controlsList="nodownload">
                        Your browser does not support the video tag.
                    </video>
                </div>
                <div class="p-4 flex justify-between items-center">
                    <h3 id="videoTitle" class="text-lg font-semibold"></h3>
                    <button onclick="closeVideoModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Content Modal (for PDFs, images, etc.) -->
        <div id="contentModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
            <div class="bg-white rounded-lg overflow-hidden w-full max-w-4xl mx-4 max-h-screen">
                <div class="p-4 flex justify-between items-center border-b">
                    <h3 id="contentTitle" class="text-lg font-semibold"></h3>
                    <button onclick="closeContentModal()" class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div class="p-4 max-h-[70vh] overflow-auto">
                    <div id="contentContainer"></div>
                </div>
            </div>
        </div>

        <!-- Purchase Message Toast -->
        <div id="purchaseToast" class="fixed bottom-4 right-4 bg-indigo-600 text-white px-6 py-3 rounded-lg shadow-lg transform translate-y-20 transition-transform duration-300 z-50">
            <div class="flex items-center">
                <i class="fas fa-info-circle mr-2"></i>
                <span>Please purchase the course to access this content</span>
            </div>
        </div>
    </div>

    <script>
        function toggleModule(moduleId) {
            const moduleContent = document.getElementById(`module-${moduleId}`);
            const icon = document.getElementById(`icon-${moduleId}`);
            
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

        function openVideoModal(videoSrc, title) {
            const modal = document.getElementById('videoModal');
            const video = document.getElementById('modalVideo');
            const videoTitle = document.getElementById('videoTitle');
            
            video.src = videoSrc;
            videoTitle.textContent = title;
            modal.classList.remove('hidden');
            
            // Play the video when modal opens
            setTimeout(() => {
                video.play();
            }, 300);
            
            // Close modal when pressing Escape key
            document.addEventListener('keydown', function handleEscape(e) {
                if (e.key === 'Escape') {
                    closeVideoModal();
                    document.removeEventListener('keydown', handleEscape);
                }
            });

            // Disable right-click menu to prevent download
            video.addEventListener('contextmenu', function(e) {
                e.preventDefault();
            });
        }

        function openContentModal(fileType, fileSrc, title) {
            const modal = document.getElementById('contentModal');
            const container = document.getElementById('contentContainer');
            const contentTitle = document.getElementById('contentTitle');
            
            contentTitle.textContent = title;
            
            if (fileType === 'pdf') {
                container.innerHTML = `<iframe src="${fileSrc}#toolbar=0" class="w-full h-[100vh]" frameborder="0"></iframe>`;
        //    else if (fileType === 'doc' || fileType === 'docx') {
        //         <div id="contentContainer">
        //         <div id="docx-viewer" style="width:100%; height:80vh; border:1px solid #ccc; display:none;"></div>
        //     </div>

            

            } else if (fileType === 'mp4' || fileType === 'mov' || fileType === 'avi' || fileType === 'mkv') {
                container.innerHTML = `<video src="${fileSrc}" controls class="w-full h-96 bg-black" controlsList="nodownload"></video>`;
            } else if (fileType === 'zip') {
                // For ZIP files, we can show a message or link to download
                container.innerHTML = `<div class="text-center py-8"><i class="fas fa-file-archive text-4xl text-gray-400 mb-2"></i><p>Download the ZIP file to view its contents.</p><a href="${fileSrc}" class="text-indigo-600 hover:text-indigo-800 font-medium">Download ZIP</a></div>`;
            }
            else if (['mp3', 'wav', 'ogg'].includes(fileType)) {
                container.innerHTML = `<audio src="${fileSrc}" controls class="w-full h-24 bg-black" controlsList="nodownload"></audio>`;
          
                container.innerHTML = `<iframe src="${fileSrc}#toolbar=0" class="w-full h-96" frameborder="0"></iframe>`;
            } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
                container.innerHTML = `<img src="${fileSrc}" alt="${title}" class="max-w-full mx-auto">`;
            } else {
                container.innerHTML = `<div class="text-center py-8"><i class="fas fa-file text-4xl text-gray-400 mb-2"></i><p>Preview not available for this file type</p></div>`;
            }
            
            modal.classList.remove('hidden');
            
            // Close modal when pressing Escape key
            document.addEventListener('keydown', function handleEscape(e) {
                if (e.key === 'Escape') {
                    closeContentModal();
                    document.removeEventListener('keydown', handleEscape);
                }
            });
        }

        function closeVideoModal() {
            const modal = document.getElementById('videoModal');
            const video = document.getElementById('modalVideo');
            
            video.pause();
            modal.classList.add('hidden');
        }

        function closeContentModal() {
            const modal = document.getElementById('contentModal');
            modal.classList.add('hidden');
        }

        function showPurchaseMessage() {
            const toast = document.getElementById('purchaseToast');
            toast.classList.remove('translate-y-20');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20');
            }, 3000);
        }

        // Close modals when clicking outside
        document.getElementById('videoModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeVideoModal();
            }
        });

        document.getElementById('contentModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeContentModal();
            }
        });

        // Expand first module by default
        document.addEventListener('DOMContentLoaded', function() {
            const firstModule = document.querySelector('.module-content');
            if (firstModule) {
                const firstModuleId = firstModule.id.split('-')[1];
                toggleModule(firstModuleId);
            }
        });
    </script>
</body>
@endsection
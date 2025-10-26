<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap Icons CDN -->
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" />
        
        <!-- Font Awesome for icons -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">

        {{-- Navigation bar --}}
        <div class="sticky top-0 z-50 bg-white">
            @include('layouts.navigation')
        </div>
        <div class="min-h-screen bg-gray-100">
            {{-- Main Content --}}
            <main class="w-full max-w-[95%] mx-auto py-2 px-2 sm:px-4 lg:px-6 relative z-20">
                {{-- In-content ads (before main content) --}}
                @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->isNotEmpty())
                    <div class="in-content-ad-top mb-6">
                        @foreach($adPlacements['in-content'] as $ad)
                            <x-ad-display :ad="$ad" placement="in-content" :delay="2" />
                        @endforeach
                    </div>
                @endif

                {{-- Page Content --}}
                @yield('content')

                {{-- In-content ads (after main content) --}}
                @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->count() > 1)
                    <div class="in-content-ad-bottom mt-6">
                        @foreach($adPlacements['in-content']->skip(1) as $ad)
                            <x-ad-display :ad="$ad" placement="in-content" :delay="5" />
                        @endforeach
                    </div>
                @endif
            </main>

            <footer class="bg-gray-900 text-gray-300 mt-10 relative">
                <!-- Your footer content remains the same -->
                <div class="max-w-7xl mx-auto px-6 sm:px-8 lg:px-12 py-10 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Brand / About -->
                    <div>
                        <div class="flex items-center space-x-3 mb-4">
                            <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                      d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            <span class="text-lg font-bold text-white">{{ config('app.name', 'Laravel') }}</span>
                        </div>
                        <p class="text-sm leading-relaxed">
                            {{ config('app.name', 'Laravel') }} is your trusted platform for products, blogs, and services. 
                            We connect people with quality and innovation.
                        </p>
                    </div>

                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Quick Links</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="{{ route('dashboard') }}" class="hover:text-indigo-400">Dashboard</a></li>
                            <li><a href="{{ route('products.index') }}" class="hover:text-indigo-400">Products</a></li>
                            <li><a href="{{ route('blog.index') }}" class="hover:text-indigo-400">Blog</a></li>
                            <li><a href="{{ route('service.index') }}" class="hover:text-indigo-400">Services</a></li>
                            <li><a href="{{ route('contact.index') }}" class="hover:text-indigo-400">Contact</a></li>
                        </ul>
                    </div>

                    <!-- Support -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Support</h4>
                        <ul class="space-y-2 text-sm">
                            <li><a href="#" class="hover:text-indigo-400">Help Center</a></li>
                            <li><a href="#" class="hover:text-indigo-400">Terms & Conditions</a></li>
                            <li><a href="#" class="hover:text-indigo-400">Privacy Policy</a></li>
                            <li><a href="#" class="hover:text-indigo-400">Report an Issue</a></li>
                        </ul>
                    </div>

                    <!-- Social -->
                    <div>
                        <h4 class="text-white font-semibold mb-4">Follow Us</h4>
                        <div class="flex space-x-4">
                            <a href="#" class="p-2 rounded-full bg-gray-800 hover:bg-indigo-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M22 12a10 10 0 11-20 0 10 10 0 0120 0zm-6.5-2h-1.8V8.7c0-.5.2-.8.9-.8h.9V6h-1.5c-1.8 0-2.6.9-2.6 2.4V10H10v2h1.4v6h2.3v-6h1.5l.3-2z"/>
                                </svg>
                            </a>
                            <a href="#" class="p-2 rounded-full bg-gray-800 hover:bg-indigo-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.5c-.9.4-1.9.6-3 .8a5.1 5.1 0 002.2-2.8 10.2 10.2 0 01-3.2 1.2A5.1 5.1 0 0016.7 3c-2.9 0-5.2 2.4-5.2 5.3 0 .4 0 .9.1 1.3A14.5 14.5 0 013 4.1a5.3 5.3 0 001.6 7 5 5 0 01-2.3-.6v.1c0 2.5 1.8 4.6 4.2 5.1a5.1 5.1 0 01-2.3.1 5.2 5.2 0 004.9 3.7A10.2 10.2 0 012 19a14.5 14.5 0 007.9 2.3c9.4 0 14.5-7.9 14.5-14.7v-.7c1-.7 1.8-1.6 2.6-2.4z"/>
                                </svg>
                            </a>
                            <a href="#" class="p-2 rounded-full bg-gray-800 hover:bg-indigo-600 transition">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19.6 3H4.4C3.1 3 2 4.1 2 5.4v13.2C2 19.9 3.1 21 4.4 21h15.2c1.3 0 2.4-1.1 2.4-2.4V5.4C22 4.1 20.9 3 19.6 3zm-1.2 14.4H5.6V6.6h12.8v10.8z"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Bottom Bar -->
                <div class="border-t border-gray-700 py-4 text-center text-sm text-gray-400">
                    © {{ date('Y') }} {{ config('app.name', 'Laravel') }}. All rights reserved.
                    <div class="mt-2">
                        <span class="text-gray-500">
                            Made with ❤️ by 
                            <a href="https://promisecreative.netlify.app" target="_blank" class="text-blue-600 hover:underline">
                                PromiseCreative
                            </a>
                        </span>
                    </div>
                </div>
            </footer>

            <!-- SUPER ROBUST Attachment Handling Script -->
            <script>
            // Enhanced AttachmentManager with robust video handling
            class AttachmentManager {
                static async openAttachment(attachmentId, fileType, title = '', fileUrl = null, resourceType = null, description = '') {
                    console.log('Opening attachment:', { attachmentId, fileType, title, fileUrl, resourceType });
                    
                    // Mark as viewed
                    this.trackView(attachmentId);
                    
                    // For ALL attachment types, load in the dashboard frame
                    await this.loadInDashboardFrame(attachmentId, fileType, title, fileUrl, resourceType, description);
                }
                
                static async loadInDashboardFrame(attachmentId, fileType, title, fileUrl = null, resourceType = null, description = '') {
                    // Hide default content and show active content
                    const defaultContent = document.getElementById('defaultContent');
                    const activeContent = document.getElementById('activeContent');
                    
                    if (defaultContent && activeContent) {
                        defaultContent.classList.add('hidden');
                        activeContent.classList.remove('hidden');
                        
                        // Show loading state
                        activeContent.innerHTML = `
                            <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                                <p class="text-gray-600">Loading ${title || 'content'}...</p>
                            </div>
                        `;
                        
                        // Load the content using the correct function based on file type
                        await this.displayContentInMainArea(attachmentId, fileType, title, fileUrl, resourceType, description);
                    } else {
                        console.error('Dashboard content areas not found');
                        // Fallback: open in new tab
                        window.open(`/attachment/${attachmentId}/view`, '_blank');
                    }
                }
                
               
                static async displayContentInMainArea(attachmentId, fileType, title, fileUrl = null, resourceType = null, description = '') {
                    const activeContent = document.getElementById('activeContent');
                    if (!activeContent) return;
                    
                    try {
                        console.log('Displaying content for:', { attachmentId, fileType, resourceType });
                        
                        // Use the correct content creation function based on file type and resource type
                        if (fileType === 'secure_video') {
                            // Handle secure videos with special loading
                            const videoUrl = await this.getVideoUrl(attachmentId, fileUrl);
                            console.log('Secure video URL:', videoUrl);
                            activeContent.innerHTML = this.createSecureVideoContent(title, videoUrl, description);
                        } else if (resourceType === 'external_video') {
                            // Handle external videos (YouTube/Vimeo)
                            activeContent.innerHTML = this.createExternalVideoContent(title, fileUrl, description);
                        } else if (this.isVideoFile(fileType)) {
                            // Handle regular video files - get proper URL first
                            const videoUrl = await this.getVideoUrl(attachmentId, fileUrl);
                            console.log('Final video URL:', videoUrl);
                            activeContent.innerHTML = this.createVideoContent(title, videoUrl, fileType, description);
                        } else if (this.isImageFile(fileType)) {
                            const imageData = await this.getFileUrl(attachmentId, fileUrl);
                            activeContent.innerHTML = this.createImageContent(title, imageData.url, description);
                        } else if (this.isDocumentFile(fileType)) {
                            // For documents, get the signed URL data
                            const docData = await this.getFileUrl(attachmentId, fileUrl);
                            console.log('Document data:', docData);
                            activeContent.innerHTML = this.createDocumentContent(title, docData, fileType, description);
                        } else {
                            const genericData = await this.getFileUrl(attachmentId, fileUrl);
                            activeContent.innerHTML = this.createGenericContent(title, genericData.url, fileType, description);
                        }
                    } catch (error) {
                        console.error('Error displaying content:', error);
                        activeContent.innerHTML = this.createErrorContent(title, 'Failed to load content: ' + error.message);
                    }
                }

                static async getFileUrl(attachmentId, fileUrl = null) {
                    // If we already have a fileUrl, use it (but check if it's a direct storage URL)
                    if (fileUrl && !fileUrl.includes('/storage/')) {
                        return { url: fileUrl };
                    }
                    
                    try {
                        // Always get a signed URL from the server for R2 files
                        const response = await fetch(`/attachment/${attachmentId}/view`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Received file data:', data);
                            
                            return { 
                                url: data.stream_url || data.url,
                                type: data.type || 'file',
                                allow_download: data.allow_download !== false
                            };
                        } else {
                            console.warn('File URL endpoint failed with status:', response.status);
                            throw new Error('Failed to get file URL');
                        }
                    } catch (error) {
                        console.warn('Failed to get file URL from endpoint:', error);
                        
                        // Fallback to direct view URL
                        return { 
                            url: `/attachment/${attachmentId}/view`,
                            type: 'fallback',
                            allow_download: true
                        };
                    }
                }

                static async getVideoData(attachmentId, fileUrl = null) {
                    // If we already have a fileUrl, use it
                    if (fileUrl) return { url: fileUrl, type: 'direct' };
                    
                    try {
                        const response = await fetch(`/attachment/${attachmentId}/view`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            if (data.stream_url || data.url) {
                                return { 
                                    url: data.stream_url || data.url, 
                                    type: data.type || 'stream' 
                                };
                            }
                        }
                    } catch (error) {
                        console.warn('Failed to get video data:', error);
                    }
                    
                    // Fallback
                    return { url: `/attachment/${attachmentId}/view`, type: 'fallback' };
                }
                
                static async getVideoUrl(attachmentId, fileUrl = null) {
                    console.log('Getting video URL for attachment:', attachmentId);
                    
                    try {
                        // First, try to get the signed URL from the view endpoint
                        const response = await fetch(`/attachment/${attachmentId}/view`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json',
                                'Content-Type': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            console.log('Received video data:', data);
                            
                            if (data.stream_url) {
                                console.log('Using stream_url:', data.stream_url);
                                return data.stream_url;
                            }
                            if (data.url) {
                                console.log('Using url:', data.url);
                                return data.url;
                            }
                        } else {
                            console.warn('View endpoint failed with status:', response.status);
                            const errorText = await response.text();
                            console.warn('Error response:', errorText);
                        }
                    } catch (error) {
                        console.warn('Failed to get video URL from view endpoint:', error);
                    }
                    
                    // Fallback: use provided fileUrl or construct URL
                    if (fileUrl) {
                        console.log('Using provided fileUrl:', fileUrl);
                        return fileUrl;
                    }
                    
                    // Ultimate fallback
                    const fallbackUrl = `/attachment/${attachmentId}/view`;
                    console.log('Using fallback URL:', fallbackUrl);
                    return fallbackUrl;
                }
                
                /**
                 * Handle secure video loading in frame - FIXED (NO RECURSION)
                 */
                static async loadSecureVideoInFrame(attachmentId, title) {
                    const activeContent = document.getElementById('activeContent');
                    
                    // Show loading state for secure video
                    activeContent.innerHTML = `
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading secure video...</p>
                            <div class="mt-4 flex justify-center space-x-4">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm flex items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> Secure Video
                                </span>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                                    <i class="fas fa-lock mr-2"></i> Encrypted
                                </span>
                            </div>
                        </div>
                    `;
                    
                    // Fetch secure video stream directly without recursion
                    await this.fetchSecureVideoStream(attachmentId, title);
                }
                
                /**
                 * Fetch secure video stream - FIXED VERSION
                 */
                static async fetchSecureVideoStream(attachmentId, title) {
                    const activeContent = document.getElementById('activeContent');
                    
                    try {
                        // First try the secure video endpoint
                        const response = await fetch(`/secure-video/${attachmentId}`, {
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Accept': 'application/json'
                            }
                        });
                        
                        if (response.ok) {
                            const data = await response.json();
                            
                            if (data.success && data.stream_url) {
                                activeContent.innerHTML = this.createSecureVideoContent(title, data.stream_url, '');
                                return;
                            }
                        }
                        
                        // If secure endpoint fails, fall back to regular video URL
                        console.warn('Secure video endpoint failed, falling back to regular video URL');
                        const videoUrl = await this.getVideoUrl(attachmentId, null);
                        activeContent.innerHTML = this.createSecureVideoContent(title, videoUrl, '');
                        
                    } catch (error) {
                        console.error('Failed to load secure video:', error);
                        activeContent.innerHTML = this.createErrorContent(title, 'Failed to load secure video: ' + error.message);
                    }
                }

                /**
                 * Open secure video modal - FIXED VERSION
                 */
                static async openSecureVideoModal(attachmentId, title) {
                    console.log('Opening secure video modal:', { attachmentId, title });
                    
                    try {
                        // For secure videos, use the displayContentInMainArea directly
                        await this.displayContentInMainArea(attachmentId, 'secure_video', title, null, 'file');
                    } catch (error) {
                        console.error('Failed to open secure video:', error);
                        this.showToast('Failed to load secure video', 'error');
                    }
                }

                /**
                 * Handle secure video loading in frame - FIXED VERSION
                 */
                static async loadSecureVideoInFrame(attachmentId, title) {
                    const activeContent = document.getElementById('activeContent');
                    
                    // Show loading state for secure video
                    activeContent.innerHTML = `
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
                            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-indigo-600 mx-auto mb-4"></div>
                            <p class="text-gray-600">Loading secure video...</p>
                            <div class="mt-4 flex justify-center space-x-4">
                                <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm flex items-center">
                                    <i class="fas fa-shield-alt mr-2"></i> Secure Video
                                </span>
                                <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                                    <i class="fas fa-lock mr-2"></i> Encrypted
                                </span>
                            </div>
                        </div>
                    `;
                    
                    // Fetch secure video stream - use regular flow for now
                    await this.displayContentInMainArea(attachmentId, 'secure_video', title, null, 'file', '');
                }

                /**
                 * Open external video - FIXED VERSION
                 */
                static openExternalVideo(videoUrl, title) {
                    console.log('Opening external video:', { videoUrl, title });
                    
                    const activeContent = document.getElementById('activeContent');
                    if (activeContent) {
                        activeContent.innerHTML = this.createExternalVideoContent(title, videoUrl, '');
                    } else {
                        // Fallback: open in new tab
                        window.open(videoUrl, '_blank');
                    }
                }
                
                // CONTENT CREATION FUNCTIONS - ROBUST VERSIONS
                
                static createSecureVideoContent(title, streamUrl, description) {
                    return `
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-2xl font-bold text-gray-800">${this.escapeHtml(title)}</h3>
                                    <div class="flex space-x-2">
                                        <span class="bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm flex items-center">
                                            <i class="fas fa-shield-alt mr-2"></i> Secure Video
                                        </span>
                                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm flex items-center">
                                            <i class="fas fa-lock mr-2"></i> Encrypted
                                        </span>
                                    </div>
                                </div>
                                ${description ? `<p class="text-gray-600 mt-2">${this.escapeHtml(description)}</p>` : ''}
                            </div>
                            <div class="bg-black video-container">
                                <video 
                                    controls 
                                    controlsList="nodownload" 
                                    class="w-full h-auto max-h-96" 
                                    autoplay
                                    playsinline
                                    preload="metadata"
                                    onerror="console.error('Secure video error:', this.error); this.closest('.video-container').innerHTML = AttachmentManager.createSecureVideoFallback('${streamUrl}', '${title}')"
                                >
                                    <source src="${streamUrl}" type="video/mp4">
                                    <source src="${streamUrl}" type="video/webm">
                                    <source src="${streamUrl}" type="video/ogg">
                                    Your browser does not support the video tag.
                                </video>
                            </div>
                            <div class="p-4 bg-gray-50 border-t border-gray-200">
                                <div class="flex items-center justify-between text-sm text-gray-600">
                                    <div class="flex items-center space-x-4">
                                        <span class="flex items-center">
                                            <i class="fas fa-shield-check text-green-500 mr-2"></i>
                                            DRM Protected
                                        </span>
                                        <span class="flex items-center">
                                            <i class="fas fa-ban text-red-500 mr-2"></i>
                                            Download Disabled
                                        </span>
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        Secure streaming • Expires in 2 hours
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                }

                /**
                 * Create secure video fallback
                 */
                static createSecureVideoFallback(streamUrl, title) {
                    return `
                        <div class="p-8 text-center bg-red-50">
                            <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Secure Video Playback Failed</h4>
                            <p class="text-gray-600 mb-4">The secure video could not be loaded. This may be due to DRM restrictions or network issues.</p>
                            <div class="space-y-2">
                                <button onclick="location.reload()" 
                                        class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
                                    <i class="fas fa-redo mr-2"></i> Try Again
                                </button>
                                <p class="text-xs text-gray-500 mt-4">
                                    If the problem persists, please contact support.
                                </p>
                            </div>
                        </div>
                    `;
                }
                
                static createVideoContent(title, videoUrl, fileType, description) {
                    const mimeType = this.getVideoMimeType(fileType);
                    console.log('Creating video content:', { 
                        title, 
                        videoUrl, 
                        fileType, 
                        mimeType,
                        videoUrlLength: videoUrl?.length 
                    });
                    
                    return `
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-2xl font-bold text-gray-800">${this.escapeHtml(title)}</h3>
                                ${description ? `<p class="text-gray-600 mt-2">${this.escapeHtml(description)}</p>` : ''}
                                <div class="text-sm text-gray-500 mt-2">
                                    <p>Loading video from: ${videoUrl ? videoUrl.substring(0, 100) + '...' : 'No URL'}</p>
                                </div>
                            </div>
                            <div class="bg-black video-container">
                                <video 
                                    controls 
                                    controlsList="nodownload" 
                                    class="w-full h-auto max-h-96" 
                                    playsinline
                                    preload="metadata"
                                    onloadstart="console.log('Video loading started')"
                                    oncanplay="console.log('Video can play')"
                                    onerror="console.error('Video error:', this.error); this.closest('.video-container').innerHTML = this.createVideoFallback('${videoUrl}', '${title}')"
                                >
                                    <source src="${videoUrl}" type="${mimeType}">
                                    <source src="${videoUrl}" type="video/mp4">
                                    Your browser does not support the video tag.
                                    <a href="${videoUrl}" download>Download the video</a>
                                </video>
                            </div>
                            <div class="p-4 flex items-center text-sm text-gray-500">
                                <i class="fas fa-video text-purple-500 mr-2"></i>
                                <span>Video Content • ${fileType.toUpperCase()}</span>
                                <a href="${videoUrl}" download class="ml-auto bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition flex items-center">
                                    <i class="fas fa-download mr-2"></i> Download
                                </a>
                            </div>
                        </div>
                    `;
                }
                
                static createExternalVideoContent(title, videoUrl, description) {
                    console.log('Creating external video content:', { videoUrl });
                    
                    const youtubeId = this.extractYouTubeId(videoUrl);
                    const vimeoId = this.extractVimeoId(videoUrl);
                    
                    let embedHtml = '';
                    
                    if (youtubeId) {
                        embedHtml = `
                            <div class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                <iframe 
                                    src="https://www.youtube.com/embed/${youtubeId}?rel=0&modestbranding=1&autoplay=1&enablejsapi=1" 
                                    class="absolute top-0 left-0 w-full h-full border-0" 
                                    frameborder="0" 
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                    allowfullscreen
                                    loading="lazy"
                                ></iframe>
                            </div>
                        `;
                    } else if (vimeoId) {
                        embedHtml = `
                            <div class="relative" style="padding-bottom: 56.25%; height: 0; overflow: hidden;">
                                <iframe 
                                    src="https://player.vimeo.com/video/${vimeoId}?autoplay=1&title=0&byline=0&portrait=0" 
                                    class="absolute top-0 left-0 w-full h-full border-0" 
                                    frameborder="0" 
                                    allow="autoplay; fullscreen; picture-in-picture" 
                                    allowfullscreen
                                    loading="lazy"
                                ></iframe>
                            </div>
                        `;
                    } else {
                        // Fallback for other video URLs
                        embedHtml = this.createExternalVideoFallback(videoUrl, title);
                    }
                    
                    return `
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-4 border-b border-gray-200">
                                <h3 class="text-2xl font-bold text-gray-800">${this.escapeHtml(title)}</h3>
                                ${description ? `<p class="text-gray-600 mt-2">${this.escapeHtml(description)}</p>` : ''}
                            </div>
                            ${embedHtml}
                            <div class="p-4 flex items-center text-sm text-gray-500">
                                <i class="fab fa-youtube text-red-500 mr-2"></i>
                                <span>External Video</span>
                                <a href="${videoUrl}" target="_blank" class="ml-auto bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200 transition flex items-center">
                                    <i class="fas fa-external-link-alt mr-2"></i> Open Original
                                </a>
                            </div>
                        </div>
                    `;
                }
                
                static createVideoFallback(videoUrl, title) {
                    return `
                        <div class="p-8 text-center">
                            <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Video Playback Failed</h4>
                            <p class="text-gray-600 mb-4">The video could not be loaded directly.</p>
                            <div class="space-y-2">
                                <a href="${videoUrl}" target="_blank" 
                                   class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
                                    <i class="fas fa-external-link-alt mr-2"></i> Open Video in New Tab
                                </a>
                                <br>
                                <a href="${videoUrl}" download 
                                   class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition inline-flex items-center">
                                    <i class="fas fa-download mr-2"></i> Download Video
                                </a>
                            </div>
                        </div>
                    `;
                }

                static createImageContent(title, fileUrl, description) {
                    return `
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-6">
                                <h3 class="text-2xl font-bold text-gray-800 mb-3">${this.escapeHtml(title)}</h3>
                                ${description ? `<p class="text-gray-600 mb-4">${this.escapeHtml(description)}</p>` : ''}
                                <div class="flex justify-center">
                                    <img src="${fileUrl}" alt="${this.escapeHtml(title)}" 
                                         class="max-w-full max-h-96 rounded-lg shadow-md" 
                                         oncontextmenu="return false;" 
                                         loading="lazy"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="hidden bg-red-100 border border-red-300 rounded-lg p-4 text-center">
                                        <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                                        Failed to load image
                                    </div>
                                </div>
                                <div class="mt-4 flex items-center text-sm text-gray-500">
                                    <i class="fas fa-image text-green-500 mr-2"></i>
                                    <span>Image Content</span>
                                    <a href="${fileUrl}" download class="ml-auto bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 transition flex items-center">
                                        <i class="fas fa-download mr-2"></i> Download
                                    </a>
                                </div>
                            </div>
                        </div>
                    `;
                }

                static createDocumentContent(title, fileData, fileType, description) {
                    const fileUrl = fileData.url;
                    const allowDownload = fileData.allow_download !== false;
                    
                    console.log('Creating document content:', { title, fileUrl, fileType, allowDownload });
                    
                    return `
                        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                            <div class="p-6">
                                <div class="flex justify-between items-center mb-4">
                                    <h3 class="text-2xl font-bold text-gray-800">${this.escapeHtml(title)}</h3>
                                    <div class="flex space-x-2">
                                        <a href="${fileUrl}" target="_blank"
                                                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-2 rounded-lg transition flex items-center">
                                            <i class="fas fa-expand mr-2"></i> Fullscreen
                                        </a>
                                        ${allowDownload ? `
                                        <a href="${fileUrl}" ${allowDownload ? 'download' : ''} 
                                        class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg transition flex items-center">
                                            <i class="fas fa-download mr-2"></i> Download
                                        </a>
                                        ` : ''}
                                    </div>
                                </div>
                                
                                ${description ? `<p class="text-gray-600 mb-6">${this.escapeHtml(description)}</p>` : ''}
                                
                                <div class="bg-gray-50 rounded-lg border" style="height: 70vh;">
                                    <iframe src="${fileUrl}${fileType === 'pdf' ? '#toolbar=0' : ''}" 
                                            class="w-full h-full border-0" 
                                            onerror="this.style.display='none'; this.parentElement.innerHTML=AttachmentManager.createDocumentFallback('${fileUrl}', '${title}', ${allowDownload})">
                                    </iframe>
                                </div>
                            </div>
                        </div>
                    `;
                }

                /**
                * Create document fallback content
                */
                static createDocumentFallback(fileUrl, title, allowDownload = true) {
                    return `
                        <div class="p-8 text-center">
                            <div class="bg-yellow-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">Document Preview Failed</h4>
                            <p class="text-gray-600 mb-4">The document preview could not be loaded.</p>
                            <div class="space-y-2">
                                <a href="${fileUrl}" target="_blank" 
                                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition inline-flex items-center">
                                    <i class="fas fa-external-link-alt mr-2"></i> Open Document in New Tab
                                </a>
                                ${allowDownload ? `
                                <br>
                                <a href="${fileUrl}" download 
                                class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition inline-flex items-center">
                                    <i class="fas fa-download mr-2"></i> Download Document
                                </a>
                                ` : ''}
                            </div>
                        </div>
                    `;
                }

                static createGenericContent(title, fileUrl, fileType, description) {
                    return `
                        <div class="bg-white rounded-lg shadow-sm p-6">
                            <h3 class="text-2xl font-bold text-gray-800 mb-3">${this.escapeHtml(title)}</h3>
                            ${description ? `<p class="text-gray-600 mb-4">${this.escapeHtml(description)}</p>` : ''}
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
                    `;
                }
                
                static createErrorContent(title, errorMessage) {
                    return `
                        <div class="bg-white rounded-lg shadow-sm p-8 text-center text-red-600">
                            <div class="bg-red-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-exclamation-triangle text-2xl"></i>
                            </div>
                            <h3 class="text-lg font-semibold mb-2">Failed to load "${this.escapeHtml(title)}"</h3>
                            <p class="text-gray-600 mb-4">${this.escapeHtml(errorMessage)}</p>
                            <button onclick="location.reload()" 
                                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 transition">
                                Try Again
                            </button>
                        </div>
                    `;
                }
                
                static createExternalVideoFallback(videoUrl, title) {
                    return `
                        <div class="bg-gray-100 rounded-lg p-8 text-center">
                            <div class="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-external-link-alt text-blue-600 text-2xl"></i>
                            </div>
                            <h4 class="text-lg font-semibold text-gray-800 mb-2">External Video</h4>
                            <p class="text-gray-600 mb-4">This video is hosted externally and needs to be opened in a new window.</p>
                            <a href="${videoUrl}" target="_blank" 
                               class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition inline-flex items-center">
                                <i class="fas fa-external-link-alt mr-2"></i> Watch on External Site
                            </a>
                        </div>
                    `;
                }
                
                // UTILITY FUNCTIONS
                
                static escapeHtml(unsafe) {
                    if (!unsafe) return '';
                    return unsafe
                        .toString()
                        .replace(/&/g, "&amp;")
                        .replace(/</g, "&lt;")
                        .replace(/>/g, "&gt;")
                        .replace(/"/g, "&quot;")
                        .replace(/'/g, "&#039;");
                }
                
                static async downloadAttachment(attachmentId) {
                    try {
                        window.open(`/attachment/${attachmentId}/download`, '_blank');
                    } catch (error) {
                        console.error('Download failed:', error);
                        this.showToast('Download failed. Please try again.', 'error');
                    }
                }
                
                static trackView(attachmentId) {
                    // Send analytics
                    fetch('/api/attachment/view', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify({ attachment_id: attachmentId })
                    }).catch(err => console.warn('Analytics failed:', err));
                }
                
                static showToast(message, type = 'info') {
                    const toast = document.createElement('div');
                    toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg shadow-lg z-50 ${
                        type === 'error' ? 'bg-red-500 text-white' : 
                        type === 'success' ? 'bg-green-500 text-white' : 
                        'bg-blue-500 text-white'
                    }`;
                    toast.textContent = message;
                    document.body.appendChild(toast);
                    
                    setTimeout(() => {
                        toast.remove();
                    }, 3000);
                }
                
                static isVideoFile(fileType) {
                    const videoTypes = ['mp4', 'mov', 'avi', 'mkv', 'webm', 'wmv', 'm4v', '3gp', 'flv', 'ogg'];
                    return videoTypes.includes(fileType?.toLowerCase());
                }
                
                static isImageFile(fileType) {
                    const imageTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg', 'ico'];
                    return imageTypes.includes(fileType?.toLowerCase());
                }
                
                static isDocumentFile(fileType) {
                    const docTypes = ['pdf', 'doc', 'docx', 'txt', 'ppt', 'pptx', 'xls', 'xlsx'];
                    return docTypes.includes(fileType?.toLowerCase());
                }
                
                static extractYouTubeId(url) {
                    if (!url) return null;
                    const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
                    const match = url.match(regExp);
                    return (match && match[7].length === 11) ? match[7] : null;
                }
                
                static extractVimeoId(url) {
                    if (!url) return null;
                    const regExp = /(?:vimeo\.com\/(?:.*\/)?(\d+))/;
                    const match = url.match(regExp);
                    return match ? match[1] : null;
                }
                
                static getVideoMimeType(extension) {
                    const types = {
                        'mp4': 'video/mp4',
                        'mov': 'video/quicktime',
                        'avi': 'video/x-msvideo',
                        'mkv': 'video/x-matroska',
                        'webm': 'video/webm',
                        'wmv': 'video/x-ms-wmv',
                        'm4v': 'video/x-m4v',
                        '3gp': 'video/3gpp',
                        'flv': 'video/x-flv',
                        'ogg': 'video/ogg'
                    };
                    return types[extension.toLowerCase()] || 'video/mp4';
                }
            }

            // Global functions for onclick handlers
            async function openAttachment(attachmentId, fileType, title = '', fileUrl = null, resourceType = null, description = '') {
                await AttachmentManager.openAttachment(attachmentId, fileType, title, fileUrl, resourceType, description);
            }

            function openSecureVideoModal(attachmentId, title) {
                AttachmentManager.openSecureVideoModal(attachmentId, title);
            }

            function downloadAttachment(attachmentId) {
                AttachmentManager.downloadAttachment(attachmentId);
            }

            function openExternalVideo(videoUrl, title) {
                AttachmentManager.openExternalVideo(videoUrl, title);
            }

           // Backward compatibility
            async function openAttachmentInDashboard(attachmentId, fileType, title, fileUrl, resourceType, description = '') {
                console.log('Opening attachment in dashboard:', { attachmentId, title, resourceType });
                
                if (resourceType === 'external_video') {
                    AttachmentManager.openExternalVideo(fileUrl, title);
                } else if (fileType === 'secure_video') {
                    // Use the display method directly to avoid recursion
                    await AttachmentManager.displayContentInMainArea(attachmentId, fileType, title, fileUrl, resourceType, description);
                } else {
                    await AttachmentManager.openAttachment(attachmentId, fileType, title, fileUrl, resourceType, description);
                }
            }

            // Initialize attachment handlers
            document.addEventListener('DOMContentLoaded', function() {
                initializeAttachmentHandlers();
            });

            function initializeAttachmentHandlers() {
                document.addEventListener('click', function(e) {
                    if (e.target.closest('[data-attachment-action]')) {
                        e.preventDefault();
                        const button = e.target.closest('[data-attachment-action]');
                        const action = button.dataset.attachmentAction;
                        const attachmentId = button.dataset.attachmentId;
                        const fileType = button.dataset.fileType;
                        const title = button.dataset.title;
                        const fileUrl = button.dataset.fileUrl;
                        const resourceType = button.dataset.resourceType;
                        const description = button.dataset.description;
                        
                        switch (action) {
                            case 'view':
                                AttachmentManager.openAttachment(attachmentId, fileType, title, fileUrl, resourceType, description);
                                break;
                            case 'view-secure-video':
                                AttachmentManager.openSecureVideoModal(attachmentId, title);
                                break;
                            case 'download':
                                AttachmentManager.downloadAttachment(attachmentId);
                                break;
                            case 'external-video':
                                AttachmentManager.openExternalVideo(fileUrl, title);
                                break;
                        }
                    }
                    
                    // Legacy handlers
                    if (e.target.closest('[onclick*="openAttachmentInDashboard"]')) {
                        e.preventDefault();
                        const button = e.target.closest('[onclick*="openAttachmentInDashboard"]');
                        const onclick = button.getAttribute('onclick');
                        const matches = onclick.match(/openAttachmentInDashboard\(([^)]+)\)/);
                        if (matches && matches[1]) {
                            const args = matches[1].split(',').map(arg => arg.trim().replace(/'/g, ''));
                            if (args.length >= 2) {
                                const attachmentId = args[0];
                                const fileType = args[1];
                                const title = args[2] || '';
                                const fileUrl = args[3] || null;
                                const resourceType = args[4] || null;
                                const description = args[5] || '';
                                AttachmentManager.openAttachment(attachmentId, fileType, title, fileUrl, resourceType, description);
                            }
                        }
                    }
                });
            }
            </script>

            <style>
                .animate-spin {
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                
                /* Video container responsive styles */
                .video-container {
                    position: relative;
                    width: 100%;
                }
                
                .video-container video {
                    width: 100%;
                    height: auto;
                    max-height: 70vh;
                }
            </style>
        </div>
    </body>
</html>
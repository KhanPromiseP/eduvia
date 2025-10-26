<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="csrf-token" content="{{ csrf_token() }}" />

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Bootstrap Icons CDN -->
        <link
            rel="stylesheet"
            href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
        />

        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
        <script src="https://unpkg.com/docx-preview/dist/docx-preview.js"></script>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

        {{-- <script src="https://cdn.tailwindcss.com"></script> --}}

        <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />

        {{-- for lazy loading images --}}
        <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js" async></script>


        <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js"></script>

        {{-- alpinejs --}}
        <script
            src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"
            defer
        ></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">


{{-- Header Ad Placement --}}
@if(isset($adPlacements['header']) && $adPlacements['header']->isNotEmpty())
<div class="header-ad-container fixed top-0 left-0 w-full z-40 pointer-events-none width-full">

    {{-- Toggle Button: placed below nav --}}
    <button id="header-ad-toggle" 
            class="absolute top-[64px] right-4 z-50 bg-blue-600 text-white px-3 py-1 rounded-b-md shadow hover:bg-blue-700 transition-colors pointer-events-auto">
        Ads ▼
    </button>

    {{-- Ad Panel --}}
    <div id="header-ad-panel" class="w-full bg-white shadow-md border-b border-gray-200 overflow-hidden transform -translate-y-full transition-transform duration-700 ease-out z-45 pointer-events-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2 flex justify-center">
            @foreach($adPlacements['header'] as $ad)
                <div class="header-ad-wrapper w-full max-w-[728px]">
                    <x-ad-display :ad="$ad" placement="header" :delay="0" />
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const adPanel = document.getElementById('header-ad-panel');
    const toggleButton = document.getElementById('header-ad-toggle');
    
    // Check if user manually hid the ad
    const isManuallyHidden = sessionStorage.getItem('headerAdManuallyHidden');
    
    // If manually hidden, keep it hidden and stop here
    if (isManuallyHidden === 'true') {
        toggleButton.textContent = 'Ads ▼';
        return;
    }
    
    // Check if we should auto-show (first visit in session)
    const hasVisited = sessionStorage.getItem('headerAdHasVisited');
    
    if (!hasVisited) {
        // First visit - set flag and show ad after delay
        sessionStorage.setItem('headerAdHasVisited', 'true');
        
        setTimeout(() => {
            adPanel.classList.remove('-translate-y-full');
            adPanel.classList.add('translate-y-0');
            toggleButton.textContent = 'Ads ▲';
        }, 1000);
    } else {
        // Subsequent visits - keep ad visible if not manually hidden
        adPanel.classList.remove('-translate-y-full');
        adPanel.classList.add('translate-y-0');
        toggleButton.textContent = 'Ads ▲';
    }
    
    // Toggle functionality
    toggleButton.addEventListener('click', function() {
        if (adPanel.classList.contains('-translate-y-full')) {
            // Show ad
            adPanel.classList.remove('-translate-y-full');
            adPanel.classList.add('translate-y-0');
            toggleButton.textContent = 'Ads ▲';
            sessionStorage.removeItem('headerAdManuallyHidden');
        } else {
            // Hide ad
            adPanel.classList.remove('translate-y-0');
            adPanel.classList.add('-translate-y-full');
            toggleButton.textContent = 'Ads ▼';
            sessionStorage.setItem('headerAdManuallyHidden', 'true');
        }
    });
});
</script>

<style>
.header-ad-container {
    height: auto;
}

#header-ad-panel {
    will-change: transform;
}

/* Smooth transition for the ad panel */
#header-ad-panel {
    transition: transform 0.7s ease-out;
}

/* Ensure the toggle button stays visible */
#header-ad-toggle {
    transition: all 0.3s ease;
}
</style>
@endif



{{-- Navigation bar --}}
<div class="sticky top-0 z-50 bg-white">
    @include('layouts.navigation')
</div>

<style>
/* Subtle bounce for ad content */
@keyframes bounce-slow {
  0%,100% { transform: translateY(0); }
  50% { transform: translateY(-2px); }
}
.header-ad-wrapper .ad-card-container {
  animation: bounce-slow 3s infinite;
}

/* Smooth slide down animation */
.header-ad-slide-down {
    transform: translateY(0) !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("header-ad-toggle");
    const adPanel = document.getElementById("header-ad-panel");
    const navBarHeight = document.querySelector('.sticky').offsetHeight;
    let isOpen = false;

    // Ensure ad panel starts hidden above nav
    adPanel.style.transform = `translateY(-${adPanel.offsetHeight}px)`;

    // Slide down automatically on page load
    setTimeout(() => {
        adPanel.style.transform = `translateY(${navBarHeight}px)`;
        isOpen = true;
        toggleBtn.innerHTML = "Ads ▲";
    }, 300);

    // Toggle button click
    toggleBtn.addEventListener("click", () => {
        if (isOpen) {
            adPanel.style.transform = `translateY(-${adPanel.offsetHeight}px)`;
            toggleBtn.innerHTML = "Ads ▼";
        } else {
            adPanel.style.transform = `translateY(${navBarHeight}px)`;
            toggleBtn.innerHTML = "Ads ▲";
        }
        isOpen = !isOpen;
    });
});
</script>

    <!-- Page Heading -->
    @isset($header)
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
    @endisset

           
           
    {{-- Main Content with Sidebar Ads --}}
    <div class="max-w-[1920px] mx-auto relative">
    
    
    {{-- Sidebar Ads --}}
    @if(isset($adPlacements['sidebar']) && $adPlacements['sidebar']->isNotEmpty())

    {{-- Left Sidebar (desktop only) --}}
    <div class="hidden xl:block fixed left-0 top-2/3 transform -translate-y-1/2 w-[160px] ml-4 z-30">
        <div class="sidebar-ads space-y-4">
            @foreach($adPlacements['sidebar'] as $index => $ad)
                <div class="sidebar-ad-wrapper relative w-full">
                    <x-ad-display 
                        :ad="$ad" 
                        placement="sidebar-left" 
                        :delay="$index * 1.5 + 4" 
                    />
                    <button class="sidebar-close-btn absolute top-1 right-1 bg-red-700 text-white text-lg font-bold px-2 py-1 rounded hover:bg-red-900 hidden">
                        ×
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Right Sidebar (desktop only) --}}
    <div class="hidden xl:block fixed right-0 top-1/2 transform -translate-y-1/2 w-[160px] mr-4 z-30">
        <div class="sidebar-ads space-y-4">
            @foreach($adPlacements['sidebar']->skip(1) as $index => $ad)
                <div class="sidebar-ad-wrapper relative w-full">
                    <x-ad-display 
                        :ad="$ad" 
                        placement="sidebar-right" 
                        :delay="$index * 1.5 + 3" 
                    />
                    <button class="sidebar-close-btn absolute top-1 right-1 bg-red-700 text-white text-lg font-bold px-2 py-1 rounded hover:bg-red-900 hidden">
                        ×
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Mobile-friendly sidebar (full visible, side) --}}
    <div class="xl:hidden fixed top-2/3 right-0 transform -translate-y-0 z-40 w-[160px] sm:w-[180px]">
        <div class="sidebar-ads space-y-4">
            @foreach($adPlacements['sidebar'] as $index => $ad)
                <div class="sidebar-ad-wrapper relative w-full">
                    <x-ad-display 
                        :ad="$ad" 
                        placement="sidebar-mobile" 
                        :delay="$index * 1.5 + 3" 
                    />
                    <button class="sidebar-close-btn absolute top-1 right-1 bg-red-700 text-white text-lg font-bold px-2 py-1 rounded hover:bg-red-900 hidden">
                        ×
                    </button>
                </div>
            @endforeach
        </div>
    </div>

    @endif

<style>
/* Subtle bounce for attention */
@keyframes bounce-slow {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-2px); }
}
.sidebar-ad-wrapper .ad-card-container {
    animation: bounce-slow 3s infinite;
    transition: transform 0.3s ease, opacity 0.3s ease;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", () => {
    document.querySelectorAll('.sidebar-ad-wrapper').forEach(wrapper => {
        const closeBtn = wrapper.querySelector('.sidebar-close-btn');

        // Show close button 5s after ad appears
        setTimeout(() => closeBtn.classList.remove('hidden'), 5000);

        // Close action
        closeBtn.addEventListener('click', () => {
            wrapper.style.transform = 'translateX(100%)';
            wrapper.style.opacity = '0';
            setTimeout(() => wrapper.remove(), 400);
        });
    });
});
</script>




                {{-- Main Content --}}
                {{-- <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 relative z-20"> --}}
                <main class="max-w-9xl mx-auto lg:px-8 relative z-20">
                    {{-- In-content ads (before main content) --}}
                    @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->isNotEmpty())
                        <div class="in-content-ad-top mb-6">
                            @foreach($adPlacements['in-content'] as $ad)
                                <x-ad-display :ad="$ad" placement="in-content" :delay="2" />
                            @endforeach
                        </div>
                    @endif

                    {{-- Page Content --}}
                    {{-- {{ $slot }} --}}

                    @yield('content')

                    {{-- Success and Error Messages --}}

                    {{-- In-content ads (after main content) --}}
                    @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->count() > 1)
                        <div class="in-content-ad-bottom mt-6">
                            @foreach($adPlacements['in-content']->skip(1) as $ad)
                                <x-ad-display :ad="$ad" placement="in-content" :delay="5" />
                            @endforeach
                        </div>
                    @endif
                </main>
            </div>

          {{-- Footer Ads --}}
@if(isset($adPlacements['footer']) && $adPlacements['footer']->isNotEmpty())
<div class="footer-ad-container fixed bottom-0 w-full bg-white border-t shadow-inner z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-center">
        @foreach($adPlacements['footer'] as $ad)
            <div class="footer-ad-wrapper max-w-[728px] w-full">
                <x-ad-display :ad="$ad" placement="footer" :delay="3" />
            </div>
        @endforeach
    </div>
</div>
@endif

{{-- Floating Ads --}}
@if(isset($adPlacements['floating']) && $adPlacements['floating']->isNotEmpty())
@foreach($adPlacements['floating'] as $index => $ad)
<div class="floating-ad fixed z-50" style="bottom:20px; right:20px;">
    <x-ad-display :ad="$ad" placement="floating" :delay="$index * 2 + 8" />
</div>
@endforeach
@endif


{{-- Popup Ads --}}
@if(isset($adPlacements['popup']) && $adPlacements['popup']->isNotEmpty())
    @foreach($adPlacements['popup'] as $index => $ad)
        <x-ad-display :ad="$ad" placement="popup" :delay="$index * 3 + 10" />
    @endforeach
@endif



{{-- Interstitial Ads --}}
@if(isset($adPlacements['interstitial']) && $adPlacements['interstitial']->isNotEmpty())
@foreach($adPlacements['interstitial'] as $index => $ad)
<div class="interstitial-ad fixed inset-0 bg-black/70 flex items-center justify-center z-50 hidden" 
     id="interstitial-ad-{{ $index }}">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full overflow-hidden relative p-6">
        <button class="absolute top-2 right-2 text-white bg-red-600 px-2 py-1 rounded z-50 close-interstitial">
            ✕
        </button>
        <x-ad-display :ad="$ad" placement="interstitial" :delay="$index * 5 + 15000" />
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const interstitial = document.getElementById("interstitial-ad-{{ $index }}");
        setTimeout(() => interstitial.classList.remove("hidden"), {{ $index * 5000 + 15000 }});
        interstitial.querySelector(".close-interstitial").addEventListener("click", () => {
            interstitial.classList.add("hidden");
        });
    });
</script>
@endforeach
@endif


            {{-- Interstitial Ads --}}
            @if(isset($adPlacements['interstitial']) && $adPlacements['interstitial']->isNotEmpty())
                @foreach($adPlacements['interstitial'] as $index => $ad)
                    <x-ad-display 
                        :ad="$ad" 
                        placement="interstitial" 
                        :delay="$index * 5 + 15" 
                    />
                @endforeach
            @endif
        </div>

        {{-- Global Ad Scripts --}}
        <script>
            // Enhanced Ad Manager with better positioning and performance
           // In your layout file, within the <script> section
window.AdManager = {
    // Initialize all ads
    init: function() {
        this.trackPageVisit();
        this.setupAdContainers();
        this.setupIntersectionObserver();
        this.setupClickHandlers();
        this.setupVisibilityChangeHandler();
        this.setupBeforeUnloadHandler();
    },


       setupAdContainers: function() {
        // Add data attributes to all ad containers for tracking
        document.querySelectorAll('.ad-container').forEach(container => {
            const adId = container.getAttribute('data-ad-id');
            const placement = container.getAttribute('data-placement');
            
            if (adId && placement) {
                container.setAttribute('data-ad-id', adId);
                container.setAttribute('data-placement', placement);
            }
        });
        
        console.log('Ad containers setup complete');
    },

    // Track page visit for analytics
   trackPageVisit: function() {
        fetch('/ads/track/page-visit', { // ✅ Correct endpoint
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                url: window.location.href,
                referrer: document.referrer,
                timestamp: Date.now(),
                screen_size: { width: window.screen.width, height: window.screen.height },
                viewport_size: { width: window.innerWidth, height: window.innerHeight }
            })
        }).catch(error => console.error('Page visit tracking error:', error));
    },

    // Setup intersection observer for ad visibility tracking
    setupIntersectionObserver: function() {
        if ('IntersectionObserver' in window) {
            this.adObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const adId = entry.target.getAttribute('data-ad-id');
                    const placement = entry.target.getAttribute('data-placement');
                    
                    if (entry.isIntersecting) {
                        if (adId && !entry.target.hasAttribute('data-view-tracked')) {
                            this.trackAdView(adId, placement);
                            entry.target.setAttribute('data-view-tracked', 'true');
                        }
                        
                        if (adId) {
                            this.startAdTimeTracking(adId);
                        }
                    } else {
                        if (adId && this.adViewTimes.has(adId)) {
                            this.updateAdTimeTracking(adId);
                        }
                    }
                });
            }, { threshold: 0.5, rootMargin: '0px 0px -100px 0px' });

            document.querySelectorAll('.ad-container[data-ad-id]').forEach(ad => {
                this.adObserver.observe(ad);
            });
        }
    },
    // Setup click handlers for all ad links
    setupClickHandlers: function() {
        document.addEventListener('click', (e) => {
            const adLink = e.target.closest('[data-ad-id]');
            if (adLink) {
                e.preventDefault();
                const adId = adLink.getAttribute('data-ad-id');
                const targetUrl = adLink.href;
                this.trackAdClick(adId, targetUrl);
                
                // Redirect after tracking
                setTimeout(() => {
                    window.location.href = targetUrl;
                }, 200);
            }
        });
    },

    // Track ad view

    trackAdView: function(adId, placement) {
        fetch('/ads/track/view', { // ✅ Correct endpoint
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ad_id: adId,
                placement: placement,
                timestamp: Date.now(),
                url: window.location.href,
                referrer: document.referrer,
                viewport: { width: window.innerWidth, height: window.innerHeight },
                session_id: this.getSessionId()
            })
        }).catch(error => console.error('View tracking error:', error));
    },

    // Track ad click
   trackAdClick: function(adId, targetUrl, placement = null) {
    const payload = {
        ad_id: adId,
        timestamp: Date.now(),
        url: window.location.href,
        target_url: targetUrl,
        session_id: this.getSessionId(),
        placement: placement
    };

    fetch('/ads/track/click', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(payload)
    })
    .then(() => {
        // Handle redirect after successful tracking
        if (targetUrl) {
            if (targetUrl.startsWith('http') && !targetUrl.includes(window.location.hostname)) {
                window.open(targetUrl, '_blank');
            } else {
                window.location.href = targetUrl;
            }
        }
    })
    .catch(error => {
        console.error('Click tracking error:', error);
        // Fallback redirect
        if (targetUrl) window.location.href = targetUrl;
    });
}

     trackTimeSpent: function(adId, timeSpent, placement = null) {
        const payload = {
            ad_id: parseInt(adId),
            time_spent: parseFloat(timeSpent.toFixed(2)),
            last_tracked_at: Date.now(),
            session_id: this.getSessionId(),
            placement: placement
        };

        fetch('/ads/track/time-spent', { // ✅ Correct endpoint
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(payload)
        }).catch(error => console.error('Time tracking error:', error));
    },


    // Time tracking functions
    adViewTimes: new Map(),
    adTimeTracking: new Map(),
    
    startAdTimeTracking: function(adId) {
        if (!this.adViewTimes.has(adId)) {
            this.adViewTimes.set(adId, Date.now());
            this.adTimeTracking.set(adId, 0);
        }
    },
    
    updateAdTimeTracking: function(adId) {
        if (this.adViewTimes.has(adId)) {
            const startTime = this.adViewTimes.get(adId);
            const currentTime = Date.now();
            const sessionTime = (currentTime - startTime) / 1000; // in seconds
            const currentTotal = this.adTimeTracking.get(adId) || 0;
            const newTotal = currentTotal + sessionTime;
            
            this.adTimeTracking.set(adId, newTotal);
            this.adViewTimes.set(adId, currentTime);
            
            // Send periodic updates (every 5 seconds)
            if (newTotal > 0 && newTotal % 5 < 0.1) {
                this.trackTimeSpent(adId, newTotal);
            }
        }
    },

    // Session management
    getSessionId: function() {
        let sessionId = localStorage.getItem('ad_session_id');
        if (!sessionId) {
            sessionId = Math.random().toString(36).substring(2) + Date.now().toString(36);
            localStorage.setItem('ad_session_id', sessionId);
        }
        return sessionId;
    },

    // Handle visibility changes
    setupVisibilityChangeHandler: function() {
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                // User switched away, update all tracked ads
                this.adViewTimes.forEach((startTime, adId) => {
                    this.updateAdTimeTracking(adId);
                });
            } else {
                // User returned, restart tracking for visible ads
                document.querySelectorAll('.ad-container[data-ad-id]').forEach(adElement => {
                    const adId = adElement.getAttribute('data-ad-id');
                    const rect = adElement.getBoundingClientRect();
                    const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                    
                    if (isVisible && this.adViewTimes.has(adId)) {
                        this.adViewTimes.set(adId, Date.now()); // Reset start time
                    }
                });
            }
        });
    },

    // Final time tracking before page unload
    setupBeforeUnloadHandler: function() {
        window.addEventListener('beforeunload', () => {
            // Update all currently tracked ads
            this.adViewTimes.forEach((startTime, adId) => {
                this.updateAdTimeTracking(adId);
            });
            
            // Send final time spent data for each ad
            this.adTimeTracking.forEach((timeSpent, adId) => {
                if (timeSpent > 0) {
                    const payload = JSON.stringify({
                        ad_id: adId,
                        time_spent: Math.round(timeSpent),
                        session_id: this.getSessionId(),
                        url: window.location.href
                    });
                    
                    navigator.sendBeacon('/ads/track/time-spent', payload);
                }
            });
        });
    }
};


// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    AdManager.init();
});
        </script>


   <footer class="bg-gray-900 text-gray-300 mt-10 relative ">
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


    </body>
</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Alpine.js -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-[#FDFDFC] text-[#1b1b18] flex flex-col min-h-screen">

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
@endif

  <!-- Navigation -->
 <div class="sticky top-0 z-50 bg-white">
    <x-guest-nav />
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
                    :delay="$index * 1.5 + 1" 
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
                    :delay="$index * 1.5 + 1" 
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
                    :delay="$index * 1.5 + 1" 
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
                <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 relative z-20">
                    {{-- In-content ads (before main content) --}}
                    @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->isNotEmpty())
                        <div class="in-content-ad-top mb-6">
                            @foreach($adPlacements['in-content'] as $ad)
                                <x-ad-display :ad="$ad" placement="in-content" :delay="2" />
                            @endforeach
                        </div>
                    @endif

                    <!-- Page Content -->
                    <main class="flex-1">
                        {{ $slot }}
                    </main>

                    

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
  <!-- Footer -->
  <div class="" >
    <x-guest-footer />
  </div>
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
<div class="popup-ad fixed inset-0 bg-black/50 flex items-center justify-center z-50 hidden" 
     id="popup-ad-{{ $index }}">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full overflow-hidden relative p-6">
        <button class="absolute top-2 right-2 text-white bg-red-600 px-2 py-1 rounded z-50 close-popup">
            ✕
        </button>
        <x-ad-display :ad="$ad" placement="popup" :delay="$index * 3 + 10" />
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const popup = document.getElementById("popup-ad-{{ $index }}");
        setTimeout(() => popup.classList.remove("hidden"), {{ $index * 3000 + 10000 }}); // delayed show
        popup.querySelector(".close-popup").addEventListener("click", () => {
            popup.classList.add("hidden");
        });
    });
</script>
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
            window.AdManager = {
                // Initialize all ads
                init: function() {
                    this.trackPageVisit();
                    this.setupAdContainers();
                    this.setupIntersectionObserver();
                    this.setupScrollHandler();
                    this.setupVisibilityChangeHandler();
                    this.setupBeforeUnloadHandler();
                },

                // Setup ad containers with proper positioning
                setupAdContainers: function() {
                    // Position sidebar ads correctly based on viewport
                    this.positionSidebarAds();
                    
                    // Handle responsive behavior
                    window.addEventListener('resize', () => {
                        this.positionSidebarAds();
                    });
                },

                // Position sidebar ads with proper offsets
                positionSidebarAds: function() {
                    const headerHeight = document.querySelector('header')?.offsetHeight || 0;
                    const viewportHeight = window.innerHeight;
                    const sidebarAds = document.querySelectorAll('.sidebar-ads');
                    
                    sidebarAds.forEach(container => {
                        const containerHeight = container.offsetHeight;
                        const maxTop = headerHeight + 20;
                        const minBottom = viewportHeight - containerHeight - 20;
                        
                        // Center vertically but ensure it stays within viewport bounds
                        container.style.top = `${Math.max(maxTop, Math.min(minBottom, (viewportHeight - containerHeight) / 2))}px`;
                    });
                },

                // Track page visit for analytics
                trackPageVisit: function() {
                    fetch('/api/ads/track/page-visit', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            url: window.location.href,
                            referrer: document.referrer,
                            timestamp: Date.now(),
                            screen_size: {
                                width: window.screen.width,
                                height: window.screen.height
                            },
                            viewport_size: {
                                width: window.innerWidth,
                                height: window.innerHeight
                            }
                        })
                    });
                },

                // Setup intersection observer for ad visibility tracking
                setupIntersectionObserver: function() {
                    if ('IntersectionObserver' in window) {
                        this.adObserver = new IntersectionObserver((entries) => {
                            entries.forEach(entry => {
                                const adId = entry.target.getAttribute('data-ad-id');
                                
                                if (entry.isIntersecting) {
                                    // Track ad view (once per session)
                                    if (adId && !entry.target.hasAttribute('data-view-tracked')) {
                                        this.trackAdView(adId);
                                        entry.target.setAttribute('data-view-tracked', 'true');
                                    }
                                    
                                    // Start time tracking
                                    if (adId) {
                                        startAdTimeTracking(adId);
                                    }
                                } else {
                                    // Ad went out of view, update time tracking
                                    if (adId && adViewTimes.has(adId)) {
                                        updateAdTimeTracking(adId);
                                    }
                                }
                            });
                        }, { 
                            threshold: 0.5, // Track when at least 50% of ad is visible
                            rootMargin: '0px 0px -100px 0px' // Add some buffer at bottom
                        });

                        // Observe all ad containers
                        document.querySelectorAll('.ad-container[data-ad-id]').forEach(ad => {
                            this.adObserver.observe(ad);
                        });
                    }
                },

                // Setup scroll handler for fixed ads
                setupScrollHandler: function() {
                    let lastScrollPosition = window.pageYOffset;
                    const floatingAds = document.querySelectorAll('.floating-ad-container');
                    
                    window.addEventListener('scroll', () => {
                        const currentScrollPosition = window.pageYOffset;
                        const scrollDirection = currentScrollPosition > lastScrollPosition ? 'down' : 'up';
                        lastScrollPosition = currentScrollPosition;
                        
                        // Adjust floating ads position based on scroll direction
                        floatingAds.forEach(ad => {
                            this.adjustFloatingAdPosition(ad, scrollDirection);
                        });
                    });
                },

                // Adjust floating ad position based on scroll direction
                adjustFloatingAdPosition: function(ad, scrollDirection) {
                    const adRect = ad.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    
                    if (scrollDirection === 'down') {
                        // When scrolling down, keep ad near bottom of viewport
                        const maxTop = viewportHeight - adRect.height - 20;
                        ad.style.top = `${Math.min(maxTop, window.pageYOffset + viewportHeight - adRect.height - 20)}px`;
                    } else {
                        // When scrolling up, keep ad near top of viewport
                        const minTop = window.pageYOffset + 20;
                        ad.style.top = `${Math.max(minTop, window.pageYOffset + 20)}px`;
                    }
                },

                // Track ad view
                trackAdView: function(adId) {
                    fetch('/api/ads/track/view', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ad_id: adId,
                            timestamp: Date.now(),
                            url: window.location.href,
                            referrer: document.referrer,
                            viewport: {
                                width: window.innerWidth,
                                height: window.innerHeight
                            },
                            position: this.getAdPosition(adId)
                        })
                    });
                },

                // Get ad position on page
                getAdPosition: function(adId) {
                    const adElement = document.querySelector(`.ad-container[data-ad-id="${adId}"]`);
                    if (!adElement) return 'unknown';
                    
                    const rect = adElement.getBoundingClientRect();
                    const viewportHeight = window.innerHeight;
                    const viewportWidth = window.innerWidth;
                    
                    // Determine position relative to viewport
                    if (rect.top < viewportHeight * 0.25) return 'top';
                    if (rect.top > viewportHeight * 0.75) return 'bottom';
                    if (rect.left < viewportWidth * 0.33) return 'left';
                    if (rect.left > viewportWidth * 0.66) return 'right';
                    return 'middle';
                },

                // Track ad click
                trackAdClick: function(adId, targetUrl) {
                    fetch('/api/ads/track/click', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            ad_id: adId,
                            timestamp: Date.now(),
                            url: window.location.href,
                            target_url: targetUrl,
                            position: this.getAdPosition(adId)
                        })
                    });
                },

                // Setup visibility change handler for time tracking
                setupVisibilityChangeHandler: function() {
                    document.addEventListener('visibilitychange', () => {
                        if (document.hidden) {
                            // User switched away, update all tracked ads
                            adViewTimes.forEach((startTime, adId) => {
                                updateAdTimeTracking(adId);
                            });
                        } else {
                            // User returned, restart tracking for visible ads
                            document.querySelectorAll('.ad-container[data-ad-id]').forEach(adElement => {
                                const adId = adElement.getAttribute('data-ad-id');
                                const rect = adElement.getBoundingClientRect();
                                const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                                
                                if (isVisible && adViewTimes.has(adId)) {
                                    adViewTimes.set(adId, Date.now()); // Reset start time
                                }
                            });
                        }
                    });
                },

                // Setup beforeunload handler for final time tracking
                setupBeforeUnloadHandler: function() {
                    window.addEventListener('beforeunload', () => {
                        // Update all currently tracked ads
                        adViewTimes.forEach((startTime, adId) => {
                            updateAdTimeTracking(adId);
                        });
                        
                        // Send time spent data for each ad
                        adTimeTracking.forEach((timeSpent, adId) => {
                            if (timeSpent > 0) {
                                const payload = JSON.stringify({
                                    ad_id: adId,
                                    time_spent: Math.round(timeSpent),
                                    session_id: null,
                                    url: window.location.href,
                                    position: this.getAdPosition(adId)
                                });
                                
                                navigator.sendBeacon('/api/ads/track/time-spent', payload);
                            }
                        });
                    });
                }
            };

            // Initialize ad management when DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                AdManager.init();
            });

            // Track time spent per ad
            let adViewTimes = new Map();
            let adTimeTracking = new Map();
            
            function startAdTimeTracking(adId) {
                if (!adViewTimes.has(adId)) {
                    adViewTimes.set(adId, Date.now());
                    adTimeTracking.set(adId, 0);
                }
            }
            
            function updateAdTimeTracking(adId) {
                if (adViewTimes.has(adId)) {
                    const startTime = adViewTimes.get(adId);
                    const currentTime = Date.now();
                    const sessionTime = currentTime - startTime;
                    const currentTotal = adTimeTracking.get(adId) || 0;
                    adTimeTracking.set(adId, currentTotal + sessionTime);
                    adViewTimes.set(adId, currentTime);
                }
            }
        </script>

</body>
</html>




  

  
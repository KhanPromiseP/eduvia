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

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net" />
        <link
            href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap"
            rel="stylesheet"
        />

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
                <div class="header-ad-container">
                    @foreach($adPlacements['header'] as $ad)
                        <x-ad-display :ad="$ad" placement="header" :delay="0" />
                    @endforeach
                </div>
            @endif

            {{-- Navigation --}}
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Main Content with Sidebar Ads --}}
            <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 relative">
                <div class="flex flex-col lg:flex-row gap-6">
                    {{-- Main Content Area --}}
                    <div class="flex-1">
                        {{-- In-content ads (before main content) --}}
                        @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->isNotEmpty())
                            <div class="in-content-ad-top mb-6">
                                @foreach($adPlacements['in-content'] as $ad)
                                    <x-ad-display :ad="$ad" placement="in-content" :delay="2" />
                                @endforeach
                            </div>
                        @endif

                        {{-- Page Content --}}
                        {{ $slot }}

                        {{-- In-content ads (after main content) --}}
                        @if(isset($adPlacements['in-content']) && $adPlacements['in-content']->count() > 1)
                            <div class="in-content-ad-bottom mt-6">
                                @foreach($adPlacements['in-content']->skip(1) as $ad)
                                    <x-ad-display :ad="$ad" placement="in-content" :delay="5" />
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Sidebar with Ads --}}
                    <aside class="w-full lg:w-80 space-y-6">
                        @if(isset($adPlacements['sidebar']) && $adPlacements['sidebar']->isNotEmpty())
                            <div class="sidebar-ads space-y-4">
                                @foreach($adPlacements['sidebar'] as $index => $ad)
                                    <x-ad-display 
                                        :ad="$ad" 
                                        placement="sidebar" 
                                        :delay="$index * 1.5 + 1" 
                                    />
                                @endforeach
                            </div>
                        @endif
                        
                        {{-- Regular sidebar content --}}
                        @yield('sidebar')
                    </aside>
                </div>
            </main>

            {{-- Footer Ad Placement --}}
            @if(isset($adPlacements['footer']) && $adPlacements['footer']->isNotEmpty())
                <div class="footer-ad-container mt-8 mb-4">
                    @foreach($adPlacements['footer'] as $ad)
                        <x-ad-display :ad="$ad" placement="footer" :delay="3" />
                    @endforeach
                </div>
            @endif

            {{-- Floating Ads --}}
            @if(isset($adPlacements['floating']) && $adPlacements['floating']->isNotEmpty())
                @foreach($adPlacements['floating'] as $index => $ad)
                    <x-ad-display 
                        :ad="$ad" 
                        placement="floating" 
                        :delay="$index * 2 + 8" 
                    />
                @endforeach
            @endif

            {{-- Popup Ads (Enhanced with new functionality) --}}
            @if(isset($adPlacements['popup']) && $adPlacements['popup']->isNotEmpty())
                @foreach($adPlacements['popup'] as $index => $ad)
                    <x-ad-display 
                        :ad="$ad" 
                        placement="popup" 
                        :delay="$index * 3 + 10" 
                    />
                @endforeach
            @endif

            {{-- Legacy popup support (backward compatibility) --}}
            @if(isset($ads) && $ads->isNotEmpty())
                @foreach($ads as $ad)
                    @if($ad->type === 'popup')
                        <div
                            x-data="{ open: true }"
                            x-show="open"
                            class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50"
                            style="display: none;"
                            x-transition
                        >
                            <div
                                class="bg-white p-6 rounded shadow max-w-md mx-auto text-center"
                            >
                                {!! $ad->content !!}
                                <button
                                    @click="open = false"
                                    class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                >
                                    Close
                                </button>
                            </div>
                        </div>
                    @elseif($ad->type === 'interstitial')
                        <div
                            x-data="{ open: false }"
                            x-init="setTimeout(() => open = true, 2000)"
                            x-show="open"
                            class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50"
                            style="display: none;"
                            x-transition
                        >
                            <div
                                class="bg-white p-6 rounded shadow max-w-lg mx-auto relative"
                            >
                                {!! $ad->content !!}
                                <button
                                    @click="open = false"
                                    class="absolute top-2 right-2 text-gray-700 hover:text-gray-900"
                                >
                                    &times;
                                </button>
                            </div>
                        </div>
                    @endif
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
            // Global ad management functions
            window.AdManager = {
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
                            timestamp: Date.now()
                        })
                    });
                },

                // Load ads dynamically for AJAX content
                loadAdsForPlacement: function(placement, container) {
                    fetch(`/api/ads/placement/${placement}`, {
                        method: 'GET',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.ads.length > 0) {
                            // Render ads dynamically
                            this.renderAds(data.ads, container, placement);
                        }
                    });
                },

                // Render ads dynamically
                renderAds: function(ads, container, placement) {
                    ads.forEach((adData, index) => {
                        const adElement = this.createAdElement(adData, placement, index);
                        container.appendChild(adElement);
                    });
                },

                // Create ad element
                createAdElement: function(adData, placement, index) {
                    const adDiv = document.createElement('div');
                    adDiv.className = 'dynamic-ad';
                    adDiv.innerHTML = this.getAdHTML(adData, placement);
                    
                    // Initialize the ad after a delay
                    setTimeout(() => {
                        this.initializeDynamicAd(adDiv, adData);
                    }, (index + 1) * 1000);
                    
                    return adDiv;
                },

                // Get HTML for different ad types
                getAdHTML: function(adData, placement) {
                    const adId = `dynamic-ad-${adData.id}-${Date.now()}`;
                    
                    switch(adData.type) {
                        case 'image':
                            return `
                                <div class="ad-container" data-ad-id="${adData.id}">
                                    <a href="${adData.link || '#'}" target="_blank" data-clickable>
                                        <img src="${adData.content}" alt="${adData.title}" class="w-full h-auto rounded-lg shadow-md" />
                                    </a>
                                    <div class="ad-label">Ad</div>
                                </div>
                            `;
                        case 'banner':
                            return `
                                <div class="ad-container bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-lg" data-ad-id="${adData.id}">
                                    <a href="${adData.link || '#'}" target="_blank" data-clickable>
                                        ${adData.content}
                                    </a>
                                    <div class="ad-label">Ad</div>
                                </div>
                            `;
                        case 'popup':
                            return `
                                <div 
                                    class="fixed inset-0 bg-black bg-opacity-60 flex items-center justify-center z-50 ad-container" 
                                    data-ad-id="${adData.id}"
                                    style="display: none;"
                                >
                                    <div class="bg-white p-6 rounded shadow max-w-md mx-auto text-center relative">
                                        <div class="popup-content">
                                            <h4 class="font-semibold mb-2">${adData.title}</h4>
                                            ${adData.content}
                                        </div>
                                        <button 
                                            onclick="this.closest('.ad-container').style.display='none'" 
                                            class="mt-4 px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                                        >
                                            Close
                                        </button>
                                        <div class="ad-label">Ad</div>
                                    </div>
                                </div>
                            `;
                        case 'interstitial':
                            return `
                                <div 
                                    class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 ad-container" 
                                    data-ad-id="${adData.id}"
                                    style="display: none;"
                                >
                                    <div class="bg-white p-6 rounded shadow max-w-lg mx-auto relative">
                                        <div class="interstitial-content">
                                            <h4 class="font-semibold mb-2">${adData.title}</h4>
                                            ${adData.content}
                                        </div>
                                        <button 
                                            onclick="this.closest('.ad-container').style.display='none'" 
                                            class="absolute top-2 right-2 text-gray-700 hover:text-gray-900 text-2xl"
                                        >
                                            &times;
                                        </button>
                                        <div class="ad-label">Ad</div>
                                    </div>
                                </div>
                            `;
                        default:
                            return `
                                <div class="ad-container p-4 bg-gray-100 border rounded-lg" data-ad-id="${adData.id}">
                                    <div class="text-center">
                                        <h4 class="font-semibold">${adData.title}</h4>
                                        <div class="mt-2 text-sm text-gray-600">${adData.content.substring(0, 100)}...</div>
                                        ${adData.link ? `<a href="${adData.link}" target="_blank" class="mt-2 inline-block text-blue-600 hover:text-blue-800" data-clickable>Learn More</a>` : ''}
                                    </div>
                                    <div class="ad-label">Ad</div>
                                </div>
                            `;
                    }
                },

                // Initialize dynamic ad
                initializeDynamicAd: function(element, adData) {
                    // Track view
                    this.trackAdView(adData.id);
                    
                    // Setup click tracking
                    const clickableElements = element.querySelectorAll('[data-clickable]');
                    clickableElements.forEach(el => {
                        el.addEventListener('click', () => {
                            this.trackAdClick(adData.id, adData.link);
                        });
                    });
                    
                    // Show popup/interstitial ads
                    if (adData.type === 'popup' || adData.type === 'interstitial') {
                        element.style.display = 'flex';
                        document.body.appendChild(element);
                        
                        // Start time tracking immediately for popup/interstitial ads
                        startAdTimeTracking(adData.id);
                        
                        // Track when popup/interstitial is closed
                        const closeButtons = element.querySelectorAll('[onclick*="style.display"], button');
                        closeButtons.forEach(btn => {
                            btn.addEventListener('click', () => {
                                updateAdTimeTracking(adData.id);
                            });
                        });
                    } else {
                        // Show with animation for other ad types
                        element.style.opacity = '0';
                        element.style.transform = 'translateY(20px)';
                        element.style.transition = 'all 0.5s ease-out';
                        
                        setTimeout(() => {
                            element.style.opacity = '1';
                            element.style.transform = 'translateY(0)';
                            
                            // Start time tracking if ad is visible
                            const rect = element.getBoundingClientRect();
                            const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                            if (isVisible) {
                                startAdTimeTracking(adData.id);
                            }
                        }, 100);
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
                            }
                        })
                    });
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
                            target_url: targetUrl
                        })
                    });
                },

                // Refresh ads periodically
                startAdRefresh: function(interval = 300000) { // 5 minutes
                    setInterval(() => {
                        this.refreshVisibleAds();
                    }, interval);
                },

                // Refresh visible ads
                refreshVisibleAds: function() {
                    const adContainers = document.querySelectorAll('.ad-container[data-ad-id]');
                    adContainers.forEach(container => {
                        if (this.isElementVisible(container)) {
                            // Optionally refresh ad content
                            this.fadeOutAndRefresh(container);
                        }
                    });
                },

                // Check if element is visible
                isElementVisible: function(element) {
                    const rect = element.getBoundingClientRect();
                    return (
                        rect.top >= 0 &&
                        rect.left >= 0 &&
                        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
                    );
                },

                // Fade out and refresh ad
                fadeOutAndRefresh: function(container) {
                    container.style.transition = 'opacity 0.5s ease-out';
                    container.style.opacity = '0.5';
                    
                    setTimeout(() => {
                        container.style.opacity = '1';
                    }, 1000);
                },

                // Load ads for specific placement (useful for AJAX pages)
                loadPlacementAds: function(placement) {
                    const container = document.querySelector(`.${placement}-ads, .${placement}-ad-container`);
                    if (container) {
                        this.loadAdsForPlacement(placement, container);
                    }
                }
            };

            // Initialize ad management when DOM is loaded
            document.addEventListener('DOMContentLoaded', function() {
                // Track page visit
                AdManager.trackPageVisit();
                
                // Start ad refresh (optional)
                // AdManager.startAdRefresh();
                
                // Setup intersection observer for better ad view tracking and time tracking
                if ('IntersectionObserver' in window) {
                    const adObserver = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            const adId = entry.target.getAttribute('data-ad-id');
                            
                            if (entry.isIntersecting) {
                                // Track ad view (once per session)
                                if (adId && !entry.target.hasAttribute('data-view-tracked')) {
                                    AdManager.trackAdView(adId);
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
                        threshold: 0.1, // Track when at least 10% of ad is visible
                        rootMargin: '0px 0px -50px 0px' // Add some buffer
                    });

                    // Observe all ad containers
                    document.querySelectorAll('.ad-container[data-ad-id]').forEach(ad => {
                        adObserver.observe(ad);
                    });
                    
                    // Also observe dynamically added ads
                    const observeNewAds = () => {
                        document.querySelectorAll('.ad-container[data-ad-id]:not([data-observed])').forEach(ad => {
                            adObserver.observe(ad);
                            ad.setAttribute('data-observed', 'true');
                        });
                    };
                    
                    // Check for new ads every 2 seconds
                    setInterval(observeNewAds, 2000);
                }
            });

            // Track time spent per ad
            let adViewTimes = new Map(); // Store when each ad was first viewed
            let adTimeTracking = new Map(); // Store accumulated time per ad
            
            // Function to start tracking time for an ad
            function startAdTimeTracking(adId) {
                if (!adViewTimes.has(adId)) {
                    adViewTimes.set(adId, Date.now());
                    adTimeTracking.set(adId, 0);
                }
            }
            
            // Function to update time spent on an ad
            function updateAdTimeTracking(adId) {
                if (adViewTimes.has(adId)) {
                    const startTime = adViewTimes.get(adId);
                    const currentTime = Date.now();
                    const sessionTime = currentTime - startTime;
                    const currentTotal = adTimeTracking.get(adId) || 0;
                    adTimeTracking.set(adId, currentTotal + sessionTime);
                    // Reset start time for continuous tracking
                    adViewTimes.set(adId, currentTime);
                }
            }
            
            // Track page unload time per ad
            window.addEventListener('beforeunload', function() {
                // Update all currently tracked ads
                adViewTimes.forEach((startTime, adId) => {
                    updateAdTimeTracking(adId);
                });
                
                // Send time spent data for each ad
                adTimeTracking.forEach((timeSpent, adId) => {
                    if (timeSpent > 0) { // Only send if user actually spent time viewing the ad
                        const payload = JSON.stringify({
                            ad_id: adId,
                            time_spent: Math.round(timeSpent), // Convert to milliseconds, rounded
                            session_id: null, // Will be handled by server
                            url: window.location.href
                        });
                        
                        navigator.sendBeacon('/api/ads/track/time-spent', payload);
                    }
                });
            });
            
            // Track when ads come into view and go out of view
            window.addEventListener('scroll', function() {
                // Throttle scroll events
                clearTimeout(window.scrollTimeout);
                window.scrollTimeout = setTimeout(() => {
                    document.querySelectorAll('.ad-container[data-ad-id]').forEach(adElement => {
                        const adId = adElement.getAttribute('data-ad-id');
                        const rect = adElement.getBoundingClientRect();
                        const isVisible = rect.top < window.innerHeight && rect.bottom > 0;
                        
                        if (isVisible) {
                            startAdTimeTracking(adId);
                        } else if (adViewTimes.has(adId)) {
                            updateAdTimeTracking(adId);
                        }
                    });
                }, 100);
            });
            
            // Track when user switches tabs/windows
            document.addEventListener('visibilitychange', function() {
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

            // Add Alpine.js integration for dynamic ad loading
            document.addEventListener('alpine:init', () => {
                Alpine.data('dynamicAds', () => ({
                    loadAds(placement) {
                        AdManager.loadPlacementAds(placement);
                    }
                }));
            });
        </script>

        {{-- Additional CSS for better ad styling --}}
        <style>
            .ad-label {
                position: absolute;
                top: 2px;
                right: 2px;
                background: rgba(0, 0, 0, 0.5);
                color: white;
                font-size: 10px;
                padding: 2px 4px;
                border-radius: 2px;
                pointer-events: none;
            }
            
            .ad-container {
                position: relative;
            }
            
            .header-ad-container,
            .footer-ad-container {
                width: 100%;
                display: flex;
                justify-content: center;
                padding: 1rem 0;
            }
            
            .sidebar-ads .ad-container {
                margin-bottom: 1rem;
            }
            
            .in-content-ad-top,
            .in-content-ad-bottom {
                display: flex;
                justify-content: center;
                margin: 1rem 0;
            }
            
            .dynamic-ad {
                transition: all 0.3s ease;
            }
            
            .dynamic-ad:hover {
                transform: translateY(-2px);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }
        </style>
    </body>
</html>
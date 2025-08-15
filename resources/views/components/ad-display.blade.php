{{-- resources/views/components/ad-display.blade.php --}}
@props(['ad', 'placement' => 'inline', 'delay' => 0])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
    $type = $ad->type;
    $content = $ad->content;
    $link = $ad->link;
    $targeting = $ad->targeting ?? [];
    
    // Define placement styles
    $placementStyles = [
        'inline' => 'position: relative; margin: 20px auto;',
        'sidebar' => 'position: sticky; top: 100px; float: right; margin: 0 0 20px 20px;',
        'header' => 'position: sticky; top: 0; z-index: 1000; width: 100%;',
        'footer' => 'position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000;',
        'floating' => 'position: fixed; bottom: 20px; right: 20px; z-index: 1050;',
        'in-content' => 'position: relative; margin: 30px 0; clear: both;',
    ];
    
    $style = $placementStyles[$placement] ?? $placementStyles['inline'];
@endphp

{{-- Main Ad Container --}}
<div 
    id="{{ $adId }}" 
    class="ad-container"
    data-ad-id="{{ $ad->id }}"
    data-ad-type="{{ $type }}"
    data-placement="{{ $placement }}"
    data-delay="{{ $delay }}"
    style="{{ $style }} display: none;"
>
    @switch($type)
        @case('image')
            @include('components.ads.image-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('video')
            @include('components.ads.video-ad', ['ad' => $ad, 'adId' => $adId])
            @break

             @case('video')
            @include('components.ads.image-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('banner')
            @include('components.ads.banner-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('js')
            @include('components.ads.js-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('popup')
            @include('components.ads.popup-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('persistent')
            @include('components.ads.persistent-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('interstitial')
            @include('components.ads.interstitial-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @default
            @include('components.ads.default-ad', ['ad' => $ad, 'adId' => $adId])
    @endswitch
</div>

{{-- JavaScript for Ad Management --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const adContainer = document.getElementById('{{ $adId }}');
    const adData = {
        id: {{ $ad->id }},
        type: '{{ $type }}',
        placement: '{{ $placement }}',
        delay: {{ $delay }},
        targeting: @json($targeting),
        link: '{{ $link }}',
        title: '{{ addslashes($ad->title) }}'
    };
    
    // Initialize ad with delay
    setTimeout(function() {
        initializeAd(adContainer, adData);
    }, adData.delay * 1000);
});

function initializeAd(container, adData) {
    // Check targeting criteria
    if (!checkTargeting(adData.targeting)) {
        return;
    }
    
    // Show the ad with animation
    showAd(container, adData);
    
    // Track view
    trackAdView(adData.id);
    
    // Setup click tracking
    setupClickTracking(container, adData);
    
    // Setup close functionality for certain ad types
    setupAdControls(container, adData);
}

function checkTargeting(targeting) {
    if (!targeting || Object.keys(targeting).length === 0) {
        return true;
    }
    
    // Device targeting
    if (targeting.device) {
        const isMobile = window.innerWidth <= 768;
        const isTablet = window.innerWidth > 768 && window.innerWidth <= 1024;
        const isDesktop = window.innerWidth > 1024;
        
        if (targeting.device === 'mobile' && !isMobile) return false;
        if (targeting.device === 'tablet' && !isTablet) return false;
        if (targeting.device === 'desktop' && !isDesktop) return false;
    }
    
    // Time targeting
    if (targeting.hours) {
        const currentHour = new Date().getHours();
        if (!targeting.hours.includes(currentHour)) return false;
    }
    
    // URL targeting
    if (targeting.urls) {
        const currentUrl = window.location.pathname;
        const matchesUrl = targeting.urls.some(url => currentUrl.includes(url));
        if (!matchesUrl) return false;
    }
    
    // User behavior targeting (example: scroll depth)
    if (targeting.scroll_depth) {
        // This would be implemented with scroll tracking
        return checkScrollDepthTargeting(targeting.scroll_depth);
    }
    
    return true;
}

function showAd(container, adData) {
    const animations = {
        'popup': 'adFadeInScale',
        'interstitial': 'adSlideDown',
        'persistent': 'adSlideUp',
        'floating': 'adFadeInRight',
        'default': 'adFadeIn'
    };
    
    const animation = animations[adData.type] || animations['default'];
    container.style.display = 'block';
    container.classList.add(animation);
    
    // Remove animation class after animation completes
    setTimeout(() => {
        container.classList.remove(animation);
    }, 500);
}

function trackAdView(adId) {
    // Use Intersection Observer for accurate view tracking
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
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
                observer.disconnect();
            }
        });
    }, {
        threshold: 0.5,
        rootMargin: '0px 0px -50px 0px'
    });
    
    observer.observe(document.getElementById('ad-' + adId));
}

function setupClickTracking(container, adData) {
    const clickableElements = container.querySelectorAll('a, button, [data-clickable]');
    
    clickableElements.forEach(element => {
        element.addEventListener('click', function(e) {
            trackAdClick(adData.id, adData.link);
        });
    });
}

function trackAdClick(adId, adLink) {
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
            target_url: adLink
        })
    });
}

function setupAdControls(container, adData) {
    // Add close button for popup and interstitial ads
    if (['popup', 'interstitial', 'persistent'].includes(adData.type)) {
        const closeBtn = document.createElement('button');
        closeBtn.innerHTML = '×';
        closeBtn.className = 'ad-close-btn';
        closeBtn.style.cssText = `
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0,0,0,0.7);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            cursor: pointer;
            font-size: 16px;
            line-height: 1;
            z-index: 10001;
        `;
        
        closeBtn.addEventListener('click', function() {
            closeAd(container, adData);
        });
        
        container.appendChild(closeBtn);
        
        // Auto-close for popup ads
        if (adData.type === 'popup') {
            setTimeout(() => {
                if (container.style.display !== 'none') {
                    closeAd(container, adData);
                }
            }, 10000); // Auto close after 10 seconds
        }
    }
}

function closeAd(container, adData) {
    container.style.opacity = '0';
    container.style.transform = 'scale(0.8)';
    
    setTimeout(() => {
        container.style.display = 'none';
        
        // Track ad close
        fetch('/api/ads/track/close', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                ad_id: adData.id,
                timestamp: Date.now()
            })
        });
    }, 300);
}

function checkScrollDepthTargeting(targetDepth) {
    const scrollDepth = (window.scrollY / (document.documentElement.scrollHeight - window.innerHeight)) * 100;
    return scrollDepth >= targetDepth;
}
</script>

{{-- CSS Styles --}}
<style>
/* Animation Classes */
.adFadeIn {
    animation: adFadeIn 0.5s ease-in-out;
}

.adFadeInScale {
    animation: adFadeInScale 0.3s ease-out;
}

.adSlideDown {
    animation: adSlideDown 0.4s ease-out;
}

.adSlideUp {
    animation: adSlideUp 0.4s ease-out;
}

.adFadeInRight {
    animation: adFadeInRight 0.5s ease-out;
}

/* Keyframes */
@keyframes adFadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes adFadeInScale {
    from { 
        opacity: 0; 
        transform: scale(0.8); 
    }
    to { 
        opacity: 1; 
        transform: scale(1); 
    }
}

@keyframes adSlideDown {
    from { 
        transform: translateY(-100%); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

@keyframes adSlideUp {
    from { 
        transform: translateY(100%); 
        opacity: 0; 
    }
    to { 
        transform: translateY(0); 
        opacity: 1; 
    }
}

@keyframes adFadeInRight {
    from { 
        transform: translateX(100%); 
        opacity: 0; 
    }
    to { 
        transform: translateX(0); 
        opacity: 1; 
    }
}

/* Ad Container Base Styles */
.ad-container {
    transition: all 0.3s ease;
    font-family: Arial, sans-serif;
}

.ad-container:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

/* Responsive Design */
@media (max-width: 768px) {
    .ad-container[data-placement="sidebar"] {
        position: relative !important;
        float: none !important;
        margin: 20px auto !important;
        max-width: 100% !important;
    }
    
    .ad-container[data-placement="floating"] {
        bottom: 10px !important;
        right: 10px !important;
        left: 10px !important;
        width: auto !important;
    }
}

/* Ad Label Styling */
.ad-label {
    position: absolute;
    bottom: 0;
    right: 0;
    background: rgba(255,255,255,0.9);
    color: #666;
    font-size: 10px;
    padding: 2px 6px;
    font-weight: 600;
    letter-spacing: 0.5px;
    border-radius: 2px 0 0 0;
    font-family: Arial, sans-serif;
    user-select: none;
    z-index: 10;
}

/* Close Button Hover Effect */
.ad-close-btn:hover {
    background: rgba(0,0,0,0.9) !important;
    transform: scale(1.1);
}

/* Overlay for popup and interstitial ads */
.ad-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0,0,0,0.7);
    z-index: 10000;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
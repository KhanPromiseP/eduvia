below is my ad-display component. I want you to make it supper robust for each and any ad and as well it should tack care of the tracking that is implimented in the app layout.  @props(['ad', 'placement' => 'inline', 'delay' => 0])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
    $type = $ad->type;
    $content = $ad->content;
    $link = $ad->link;
    $targeting = $ad->targeting ?? [];
    
    // Define placement styles
    $placementStyles = [
        'inline' => 'position: relative; margin: 20px auto;',
        'sidebar' => 'position: sticky; top: 100px; float: right; margin: 0 0 10px 10px; right: 5px',
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
    
    // Show the ad without animation
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
    
    return true;
}

function showAd(container, adData) {
    // Simply show the container without any animation
    container.style.display = 'block';
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

// function setupAdControls(container, adData) {
//     // Add close button for popup and interstitial ads
//     if (['popup', 'interstitial', 'persistent'].includes(adData.type)) {
//         const closeBtn = document.createElement('button');
//         closeBtn.innerHTML = '×';
//         closeBtn.className = 'ad-close-btn';
//         closeBtn.style.cssText = `
//             position: absolute;
//             top: 5px;
//             right: 5px;
//             background: rgba(0,0,0,0.7);
//             color: white;
//             border: none;
//             border-radius: 50%;
//             width: 25px;
//             height: 25px;
//             cursor: pointer;
//             font-size: 16px;
//             line-height: 1;
//             z-index: 10001;
//         `;
        
//         // Show close button after 5 seconds
//         setTimeout(() => {
//             container.appendChild(closeBtn);
//         }, 5000);
        
//         closeBtn.addEventListener('click', function() {
//             closeAd(container, adData);
//         });
        
//         // Auto-close for popup ads after 10 seconds
//         if (adData.type === 'popup') {
//             setTimeout(() => {
//                 if (container.style.display !== 'none') {
//                     closeAd(container, adData);
//                 }
//             }, 10000);
//         }
//     }
// }

function closeAd(container, adData) {
    // Simply hide the container without animation
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
}
</script>
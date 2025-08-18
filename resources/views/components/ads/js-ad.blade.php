@props(['ad'])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
@endphp

<div class="ad-js-container bg-white rounded-lg shadow-lg border border-gray-200 overflow-hidden relative" 
     style="min-width: 300px; min-height: 250px;" id="js-ad-{{ $adId }}">
    
    <!-- JS Ad Content -->
    <div id="js-ad-content-{{ $adId }}" class="w-full h-full"></div>

    <!-- Ad Label -->
    <div class="ad-label absolute top-2 right-2 text-xs text-gray-400 z-10">Ad</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const adId = "{{ $adId }}";
    const container = document.getElementById(`js-ad-content-${adId}`);
    const adData = {
        id: {{ $ad->id }},
        content: `{!! addslashes($ad->content) !!}`,
        link: '{{ $ad->link }}'
    };

    // Render ad content safely
    try {
        const scriptElement = document.createElement('script');
        scriptElement.type = 'text/javascript';
        scriptElement.text = adData.content;
        container.appendChild(scriptElement);
    } catch (error) {
        console.error('Ad script error:', error);
        container.innerHTML = '<div class="p-4 text-gray-500 text-center">Ad failed to load</div>';
    }

    // View tracking using Intersection Observer
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                trackAdView(adData.id);
                observer.disconnect();
            }
        });
    }, { threshold: 0.5 });
    observer.observe(container);

    // Click tracking
    container.addEventListener('click', () => {
        if (adData.link) {
            trackAdClick(adData.id, adData.link);
        }
    });

    function trackAdView(adId) {
        fetch('/api/ads/track/view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ad_id: adId, timestamp: Date.now(), url: window.location.href })
        });
    }

    function trackAdClick(adId, link) {
        fetch('/api/ads/track/click', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ad_id: adId, timestamp: Date.now(), target_url: link })
        });
    }
});
</script>

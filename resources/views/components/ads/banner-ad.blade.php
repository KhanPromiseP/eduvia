@props(['ad'])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
@endphp

<div id="{{ $adId }}" class="ad-card-container max-w-[350px] w-full rounded-xl overflow-hidden shadow-lg border border-gray-200 bg-white transition-transform duration-300 hover:scale-105 hover:shadow-2xl relative"
     style="display: none;">
    <!-- Banner Image -->
    <a href="{{ $ad->link ?? '#' }}" target="_blank" rel="noopener noreferrer" class="ad-clickable">
        <img src="{{ $ad->content }}" alt="{{ $ad->title }}" class="w-full h-auto object-cover">
    </a>

    <!-- Content -->
    <div class="p-4 text-center">
        <h2 class="text-lg md:text-xl font-bold text-gray-900 mb-2">{{ $ad->title }}</h2>
        @if(isset($ad->description))
            <p class="text-gray-600 text-sm md:text-base mb-3">{{ $ad->description }}</p>
        @endif
        @if(isset($ad->price))
            <p class="text-gray-900 text-base md:text-lg font-semibold mb-4">${{ number_format($ad->price, 2) }}</p>
        @endif
        <a href="{{ $ad->link ?? '#' }}" target="_blank" rel="noopener noreferrer"
           class="inline-block px-4 py-2 bg-blue-600 text-white text-sm rounded-md hover:bg-blue-700 transition-colors duration-300 ad-clickable"  @click="trackAdClick>
            Click Here
        </a>
    </div>

    <!-- Close Button & Countdown Bar -->
    @if(in_array($ad->type, ['persistent', 'floating', 'popup']))
        <div class="absolute top-0 left-0 w-full h-1 bg-gray-300">
            <div class="progress-bar bg-blue-600 h-full w-0 transition-width duration-1000"></div>
        </div>
        <button class="ad-close-btn absolute top-2 right-2 bg-black bg-opacity-60 text-white rounded-full w-6 h-6 text-sm flex items-center justify-center opacity-0 cursor-not-allowed">
            ×
        </button>
    @endif
</div>

<style>
.ad-card-container {
    animation: bounce-slow 3s infinite;
    z-index: 1050;
}

@keyframes bounce-slow {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-3px); }
}

.ad-close-btn:hover {
    opacity: 1 !important;
}

@media (max-width: 400px) {
    .ad-card-container { max-width: 100%; }
    .ad-card-container h2 { font-size: 1rem; }
    .ad-card-container p { font-size: 0.875rem; }
    .ad-card-container a { font-size: 0.8rem; padding: 8px 16px; }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('{{ $adId }}');
    const closeBtn = container.querySelector('.ad-close-btn');
    const progressBar = container.querySelector('.progress-bar');
    const adId = {{ $ad->id }};
    const displayDelay = {{ $ad->delay ?? 0 }};
    const closeDelay = 5; // seconds before close button appears
    const autoCloseTime = 15; // seconds before popup auto closes

    // Show ad after delay
    setTimeout(() => {
        container.style.display = 'block';
        trackView(adId);

        // Start countdown for close button
        if(closeBtn && progressBar){
            let elapsed = 0;
            closeBtn.classList.add('cursor-not-allowed');
            const interval = setInterval(() => {
                elapsed++;
                progressBar.style.width = `${(elapsed / closeDelay) * 100}%`;
                if(elapsed >= closeDelay){
                    closeBtn.classList.remove('opacity-0', 'cursor-not-allowed');
                    closeBtn.classList.add('opacity-100', 'cursor-pointer');
                    clearInterval(interval);
                }
            }, 1000);

            // Auto-close popup if applicable
            if('{{ $ad->type }}' === 'popup'){
                setTimeout(() => {
                    closeAd();
                }, autoCloseTime * 1000);
            }
        }
    }, displayDelay * 1000);

    // Close button click
    if(closeBtn){
        closeBtn.addEventListener('click', function(){
            closeAd();
        });
    }

    // Click tracking
    container.querySelectorAll('.ad-clickable').forEach(el => {
        el.addEventListener('click', function(){
            fetch('/api/ads/track/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ad_id: adId, timestamp: Date.now() })
            });
        });
    });

    function closeAd(){
        container.style.display = 'none';
        fetch('/api/ads/track/close', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ad_id: adId, timestamp: Date.now() })
        });
    }

    function trackView(adId){
        fetch('/api/ads/track/view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ad_id: adId, timestamp: Date.now(), url: window.location.href })
        });
    }
});
</script>

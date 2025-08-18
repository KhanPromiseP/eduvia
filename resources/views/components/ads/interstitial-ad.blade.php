@props(['ad'])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
@endphp

<div class="ad-overlay fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" id="interstitial-overlay-{{ $adId }}" style="display: none;">
    <div class="ad-interstitial-container bg-white rounded-2xl shadow-2xl overflow-hidden relative w-11/12 max-w-lg max-h-[80vh]">

        <!-- Progress Bar -->
        <div class="absolute top-0 left-0 w-full h-1 bg-gray-300">
            <div class="progress-bar bg-blue-600 h-full w-0 transition-width duration-1000"></div>
        </div>

        <!-- Close Button -->
        <button id="close-ad-{{ $adId }}" class="absolute top-4 right-4 text-gray-500 hover:text-gray-900 text-2xl font-bold opacity-0 cursor-not-allowed">&times;</button>

        @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" class="block group ad-clickable">
            <div class="relative">
                <div class="h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center">
                    <h2 class="text-3xl font-bold text-white text-center px-6">{{ $ad->title }}</h2>
                </div>
                <div class="p-8">
                    <div class="text-gray-700 text-lg leading-relaxed mb-6">
                        {!! $ad->content !!}
                    </div>
                    <div class="text-center">
                        <span class="inline-block bg-gradient-to-r from-blue-500 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-blue-600 hover:to-purple-700 transition-all duration-300 transform group-hover:scale-105 shadow-lg">
                            Discover More
                        </span>
                    </div>
                </div>
            </div>
        </a>
        @else
        <div class="relative">
            <div class="h-64 bg-gradient-to-br from-blue-400 via-purple-500 to-pink-500 flex items-center justify-center">
                <h2 class="text-3xl font-bold text-white text-center px-6">{{ $ad->title }}</h2>
            </div>
            <div class="p-8">
                <div class="text-gray-700 text-lg leading-relaxed">
                    {!! $ad->content !!}
                </div>
            </div>
        </div>
        @endif

        <div class="ad-label absolute bottom-2 right-4 text-xs text-gray-400">Ad</div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const adId = "{{ $adId }}";
    const overlay = document.getElementById(`interstitial-overlay-${adId}`);
    const closeBtn = document.getElementById(`close-ad-${adId}`);
    const progressBar = overlay.querySelector('.progress-bar');
    const displayDelay = {{ $ad->delay ?? 0 }} * 1000; // optional delay before showing
    const closeDelay = 5; // seconds before close button appears
    const autoCloseTime = 15; // seconds before auto-close

    // Show ad if not already shown in session
    if (!sessionStorage.getItem(`adShown-${adId}`)) {
        setTimeout(() => {
            overlay.style.display = "flex";
            trackView();
            startCountdown();
        }, displayDelay);
    }

    function startCountdown() {
        let elapsed = 0;
        closeBtn.classList.add('cursor-not-allowed');
        const interval = setInterval(() => {
            elapsed++;
            progressBar.style.width = `${(elapsed / closeDelay) * 100}%`;
            if (elapsed >= closeDelay) {
                closeBtn.classList.remove('opacity-0', 'cursor-not-allowed');
                closeBtn.classList.add('opacity-100', 'cursor-pointer');
                clearInterval(interval);
            }
        }, 1000);

        // Auto-close after autoCloseTime
        setTimeout(() => {
            closeAd();
        }, autoCloseTime * 1000);
    }

    // Close button click
    closeBtn.addEventListener('click', closeAd);

    function closeAd() {
        overlay.style.display = "none";
        sessionStorage.setItem(`adShown-${adId}`, "true");
        trackClose();
    }

    // Click tracking
    overlay.querySelectorAll('.ad-clickable').forEach(el => {
        el.addEventListener('click', () => {
            fetch('/api/ads/track/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ad_id: "{{ $ad->id }}", timestamp: Date.now() })
            });
        });
    });

    // View tracking
    function trackView() {
        fetch('/api/ads/track/view', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ad_id: "{{ $ad->id }}", timestamp: Date.now(), url: window.location.href })
        });
    }

    // Close tracking
    function trackClose() {
        fetch('/api/ads/track/close', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ ad_id: "{{ $ad->id }}", timestamp: Date.now() })
        });
    }
});
</script>

{{-- Popup Ads --}}
@if(isset($adPlacements['popup']) && $adPlacements['popup']->isNotEmpty())
    @foreach($adPlacements['popup'] as $index => $ad)
        <x-ad-display :ad="$ad" placement="popup" :delay="$index * 3 + 10" />
    @endforeach
@endif

{{-- Ad Display Component --}}
@props(['ad', 'delay' => 0])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
@endphp

<div class="popup-ad hidden" 
     id="popup-ad-{{ $adId }}" 
     data-delay="{{ $delay }}">
    <div class="ad-overlay fixed inset-0 bg-black/60 flex items-center justify-center z-[9999]">
        <div class="ad-popup-container bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-md w-full mx-4 relative overflow-hidden">

            <!-- Progress Bar -->
            <div class="progress-bar absolute top-0 left-0 h-1 bg-gray-200 w-full">
                <div class="progress-fill h-full bg-blue-500 w-0 transition-all duration-100 linear"></div>
            </div>

            <!-- Close Button -->
            <button class="ad-close-btn absolute top-3 right-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full w-8 h-8 flex items-center justify-center hidden">
                ✕
            </button>

            <!-- Ad Content -->
            @if($ad->link)
            <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" class="block ad-clickable">
                <div class="p-6 text-center">
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $ad->title }}</h3>
                    <div class="text-gray-700 text-sm mb-4">{!! $ad->content !!}</div>
                    <span class="inline-block bg-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-transform duration-300 transform hover:scale-105">
                        Learn More
                    </span>
                </div>
            </a>
            @else
            <div class="p-6 text-center">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $ad->title }}</h3>
                <div class="text-gray-700 text-sm">{!! $ad->content !!}</div>
            </div>
            @endif

            <div class="absolute bottom-2 right-2 text-gray-500 text-xs">Ad</div>
        </div>
    </div>
</div>

<script>
(() => {
    const ads = document.querySelectorAll('.popup-ad');

    ads.forEach(ad => {
        const delay = parseInt(ad.dataset.delay) * 1000 || 0;
        const overlay = ad.querySelector('.ad-overlay');
        const closeBtn = ad.querySelector('.ad-close-btn');
        const progressFill = ad.querySelector('.progress-fill');
        const adClickable = ad.querySelector('.ad-clickable');
        const adId = ad.id;

        // Show only once per session
        if (sessionStorage.getItem(`adShown-${adId}`)) return;

        setTimeout(() => {
            ad.classList.remove('hidden');
            sessionStorage.setItem(`adShown-${adId}`, 'true');

            const duration = 10000; // 10s
            const closeBtnTime = 5000; // show close after 5s
            const startTime = Date.now();

            const updateProgress = () => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration * 100, 100);
                progressFill.style.width = `${progress}%`;

                if (elapsed >= closeBtnTime) closeBtn.classList.remove('hidden');

                if (elapsed >= duration) {
                    ad.remove(); // remove overlay and popup
                } else {
                    requestAnimationFrame(updateProgress);
                }
            };
            requestAnimationFrame(updateProgress);

        }, delay);

        // Manual close
        closeBtn.addEventListener('click', () => ad.remove());

        // Click tracking
        if (adClickable) {
            adClickable.addEventListener('click', () => {
                fetch('/api/ads/track/click', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ad_id: adId, timestamp: Date.now(), target_url: adClickable.href })
                });
            });
        }

        // View tracking
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                    fetch('/api/ads/track/view', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ ad_id: adId, timestamp: Date.now(), url: window.location.href })
                    });
                    observer.disconnect();
                }
            });
        }, { threshold: 0.5 });
        observer.observe(ad.querySelector(".ad-popup-container"));
    });
})();
</script>

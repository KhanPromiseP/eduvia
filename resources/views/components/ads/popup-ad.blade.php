@props(['ad', 'delay' => 0])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
@endphp

<div class="ad-overlay fixed inset-0 flex items-center justify-center bg-black/60 z-[9999] hidden"
     id="popup-overlay-{{ $adId }}">
    <div class="ad-popup-container bg-white rounded-2xl shadow-2xl border border-gray-200 max-w-md w-full mx-4 relative overflow-hidden">

        <!-- Progress Bar -->
        <div class="progress-bar absolute top-0 left-0 h-1 bg-gray-200 w-full">
            <div class="progress-fill h-full bg-blue-500 w-0 transition-all duration-100 linear"></div>
        </div>

        <!-- Close Button -->
        <button id="ad-close-btn-{{ $adId }}" 
                class="absolute top-3 right-3 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full w-8 h-8 flex items-center justify-center hidden"
                aria-label="Close ad">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
            </svg>
        </button>

        <!-- Ad Content -->
        @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" class="block ad-clickable">
            <div class="p-6 text-center">
                <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $ad->title }}</h3>
                <div class="text-gray-700 text-sm mb-4">
                    {!! $ad->content !!}
                </div>
                <span class="inline-block bg-blue-500 text-white px-5 py-2 rounded-lg font-semibold hover:bg-blue-600 transition-transform duration-300 transform hover:scale-105">
                    Learn More
                </span>
            </div>
        </a>
        @else
        <div class="p-6 text-center">
            <h3 class="text-xl font-semibold text-gray-900 mb-3">{{ $ad->title }}</h3>
            <div class="text-gray-700 text-sm">
                {!! $ad->content !!}
            </div>
        </div>
        @endif

        <!-- Ad Label -->
        <div class="absolute bottom-2 right-2 text-gray-500 text-xs">Ad</div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const overlay = document.getElementById("popup-overlay-{{ $adId }}");
    const closeBtn = document.getElementById("ad-close-btn-{{ $adId }}");
    const progressFill = overlay.querySelector(".progress-fill");
    const adClickable = overlay.querySelector(".ad-clickable");

    const adData = {
        id: {{ $ad->id }},
        link: '{{ $ad->link }}'
    };

    // Only show once per session
    if (!sessionStorage.getItem(`adShown-{{ $adId }}`)) {
        setTimeout(() => {
            overlay.classList.remove("hidden");
            sessionStorage.setItem(`adShown-{{ $adId }}`, "true");

            const duration = 10000; // total 10 seconds
            const closeBtnTime = 5000; // show close after 5s
            const startTime = Date.now();

            const updateProgress = () => {
                const elapsed = Date.now() - startTime;
                const progress = Math.min(elapsed / duration * 100, 100);
                progressFill.style.width = `${progress}%`;

                if (elapsed >= closeBtnTime && closeBtn.classList.contains("hidden")) {
                    closeBtn.classList.remove("hidden");
                }

                if (elapsed >= duration) {
                    overlay.classList.add("hidden");
                } else {
                    requestAnimationFrame(updateProgress);
                }
            };
            updateProgress();

        }, {{ $delay * 1000 }});
    }

    // Manual close
    closeBtn.addEventListener("click", () => {
        overlay.classList.add("hidden");
    });

    // Click tracking
    if (adClickable) {
        adClickable.addEventListener("click", () => {
            fetch('/api/ads/track/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ad_id: adData.id, timestamp: Date.now(), target_url: adData.link })
            });
        });
    }

    // View tracking when 50% visible
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && entry.intersectionRatio >= 0.5) {
                fetch('/api/ads/track/view', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ ad_id: adData.id, timestamp: Date.now(), url: window.location.href })
                });
                observer.disconnect();
            }
        });
    }, { threshold: 0.5 });
    observer.observe(overlay.querySelector(".ad-popup-container"));
});
</script>

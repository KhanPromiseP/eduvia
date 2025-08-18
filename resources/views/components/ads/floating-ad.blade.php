@props(['ad', 'placement' => 'bottom-right'])

@php
    $adId = 'floating-ad-' . $ad->id . '-' . uniqid();
    $placementStyles = [
        'bottom-right' => 'bottom:20px; right:20px;',
        'bottom-left'  => 'bottom:20px; left:20px;',
        'top-right'    => 'top:20px; right:20px;',
        'top-left'     => 'top:20px; left:20px;',
    ];
    $style = $placementStyles[$placement] ?? $placementStyles['bottom-right'];
@endphp

<div id="{{ $adId }}" 
     class="ad-floating-container fixed z-50 w-72 bg-white shadow-2xl rounded-lg overflow-hidden transform translate-y-20 transition-transform duration-700"
     style="{{ $style }}">
    
    {{-- Close Button --}}
    <button id="{{ $adId }}-close" 
            class="absolute top-2 right-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-full w-7 h-7 flex items-center justify-center hidden"
            aria-label="Close ad">
        ✕
    </button>

    {{-- Ad Content --}}
    <div class="p-4 text-center">
        <div class="text-gray-400 text-xs mb-1">Advertisement</div>
        <div class="text-gray-700 font-semibold">{{ $ad->title }}</div>
        @if($ad->content)
            <div class="text-gray-500 text-sm mt-1">
                {!! Str::limit($ad->content, 60) !!}
            </div>
        @endif
        @if($ad->link)
            <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer"
               class="mt-3 inline-block px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors duration-300 ad-clickable">
                Learn More
            </a>
        @endif
    </div>

    {{-- Ad Label --}}
    <div class="ad-label absolute bottom-2 right-2 text-xs text-gray-400">Ad</div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const ad = document.getElementById("{{ $adId }}");
    const closeBtn = document.getElementById("{{ $adId }}-close");
    const clickable = ad.querySelector(".ad-clickable");

    // Show ad with slide-in after 1s
    setTimeout(() => {
        ad.classList.remove("translate-y-20");
    }, 1000);

    // Show close button after 5 seconds
    setTimeout(() => {
        closeBtn.classList.remove("hidden");
    }, 5000);

    // Close action
    closeBtn.addEventListener("click", () => {
        ad.style.transform = "translateY(100%)";
        ad.style.opacity = "0";
        setTimeout(() => ad.remove(), 400);
    });

    // Click tracking
    if (clickable) {
        clickable.addEventListener("click", () => {
            fetch('/api/ads/track/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ ad_id: "{{ $ad->id }}", timestamp: Date.now(), target_url: "{{ $ad->link }}" })
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
                    body: JSON.stringify({ ad_id: "{{ $ad->id }}", timestamp: Date.now(), url: window.location.href })
                });
                observer.disconnect();
            }
        });
    }, { threshold: 0.5 });
    observer.observe(ad);
});
</script>

<style>
.ad-floating-container:hover {
    transform: scale(1.02) !important;
    transition: transform 0.3s;
}
@media (max-width: 640px) {
    .ad-floating-container { width: 90%; }
    .ad-floating-container .text-sm { font-size: 0.75rem; }
}
</style>

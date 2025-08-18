@props(['ad'])

@if(isset($ad))
@php
    $adId = 'persistent-ad-' . $ad->id . '-' . uniqid();
@endphp

<div id="{{ $adId }}" 
     class="ad-persistent-container fixed bottom-0 left-0 right-0 z-50 min-h-[60px] bg-gradient-to-r from-indigo-600 to-purple-600 text-white shadow-xl border-t border-indigo-500 transform translate-y-20 transition-transform duration-700 overflow-hidden">
    
    {{-- Particle overlay --}}
    <canvas id="{{ $adId }}-canvas" class="absolute top-0 left-0 w-full h-full pointer-events-none"></canvas>

    <div class="container mx-auto px-4 py-3 flex flex-col sm:flex-row items-center justify-between relative">
        
        {{-- Close Button --}}
        <button id="{{ $adId }}-close" 
                class="absolute top-2 right-2 text-white bg-red-600 hover:bg-red-700 font-bold px-3 py-1 rounded z-50 text-lg">
            ✕
        </button>

        {{-- Ad Content --}}
        @if($ad->link)
        <a href="{{ $ad->link }}" target="_blank" rel="noopener noreferrer" 
           class="flex flex-col sm:flex-row items-center w-full sm:w-auto justify-between hover:bg-white hover:bg-opacity-10 rounded-lg px-4 py-2 transition-all duration-300 ad-clickable">
            <div class="flex-1 mb-2 sm:mb-0">
                <h4 class="font-bold text-lg">{{ $ad->title }}</h4>
                <div class="text-sm opacity-90">{!! $ad->content !!}</div>
            </div>
            <div class="ml-0 sm:ml-4 mt-1 sm:mt-0">
                <span class="bg-white bg-opacity-20 hover:bg-opacity-30 px-4 py-2 rounded-lg font-semibold transition-all duration-300">
                    Get Started →
                </span>
            </div>
        </a>
        @else
        <div class="flex flex-col sm:flex-row items-center justify-between w-full sm:w-auto px-4 py-2">
            <div class="flex-1 mb-2 sm:mb-0">
                <h4 class="font-bold text-lg">{{ $ad->title }}</h4>
                <div class="text-sm opacity-90">{!! $ad->content !!}</div>
            </div>
        </div>
        @endif

        {{-- Label --}}
        <div class="ad-label absolute bottom-1 right-4 text-xs text-white/80">Ad</div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const ad = document.getElementById("{{ $adId }}");
    const closeBtn = document.getElementById("{{ $adId }}-close");
    const adClickable = ad.querySelector(".ad-clickable");

    // Show only if not closed in session
    if (!sessionStorage.getItem("persistentAdClosed-{{ $adId }}")) {
        setTimeout(() => {
            ad.classList.remove("translate-y-20");
        }, 500);
    } else {
        ad.remove();
    }

    // Close action
    closeBtn.addEventListener("click", () => {
        ad.style.transform = "translateY(100%)";
        ad.style.opacity = "0";
        sessionStorage.setItem("persistentAdClosed-{{ $adId }}", "true");
        setTimeout(() => ad.remove(), 400);
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

    // Particle effect
    const canvas = document.getElementById("{{ $adId }}-canvas");
    const ctx = canvas.getContext("2d");
    function resizeCanvas() {
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
    }
    window.addEventListener('resize', resizeCanvas);
    resizeCanvas();

    let particles = [];
    const count = window.innerWidth < 640 ? 15 : 30;
    for(let i=0; i<count; i++){
        particles.push({ x: Math.random()*canvas.width, y: Math.random()*canvas.height, r: Math.random()*2+1, dx: (Math.random()-0.5)*0.5, dy: -Math.random()*1 });
    }

    function animateParticles(){
        ctx.clearRect(0,0,canvas.width,canvas.height);
        particles.forEach(p=>{
            ctx.beginPath();
            ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
            ctx.fillStyle = "rgba(255,255,255,0.5)";
            ctx.fill();
            p.x += p.dx;
            p.y += p.dy;
            if(p.y<0) p.y=canvas.height;
            if(p.x<0) p.x=canvas.width;
            if(p.x>canvas.width) p.x=0;
        });
        requestAnimationFrame(animateParticles);
    }
    animateParticles();
});
</script>

<style>
.ad-persistent-container a:hover {
    transform: translateY(-2px);
}
@media (max-width: 640px) {
    .ad-persistent-container { min-height: 80px; padding: 0.5rem 1rem; }
    .ad-persistent-container h4 { font-size: 1rem; }
    .ad-persistent-container .text-sm { font-size: 0.75rem; }
    #{{ $adId }}-close { top: 0.25rem; right: 0.25rem; padding: 0.25rem 0.5rem; }
}
</style>
@endif

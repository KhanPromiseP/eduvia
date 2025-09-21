@props(['ad', 'placement' => 'inline', 'delay' => 0])

@php
    $adId = 'ad-' . $ad->id . '-' . uniqid();
    $type = $ad->type;
    $content = $ad->content;
    $link = $ad->link;
    $targeting = $ad->targeting ?? [];
    $isActive = $ad->is_active && (!$ad->end_at || $ad->end_at->isFuture());
    
    // Placement styles
    $placementStyles = [
        'inline' => 'position: relative; margin: 1.25rem auto; max-width: 100%;',
        'sidebar' => 'position: sticky; top: 100px; margin: 0 0 1rem 1rem; width: 160px;',
        'header' => 'position: sticky; top: 0; z-index: 1000; width: 100%; background: white;',
        'footer' => 'position: fixed; bottom: 0; left: 0; right: 0; z-index: 1000; background: white; border-top: 1px solid #e5e7eb;',
        'floating' => 'position: fixed; bottom: 1.25rem; right: 1.25rem; z-index: 1050; width: 300px; max-width: 90vw;',
        'in-content' => 'position: relative; margin: 1.5rem 0; clear: both;',
        'popup' => 'position: fixed; inset: 0; z-index: 1100; background: rgba(0,0,0,0.7); display: flex; justify-content: center; align-items: center;',
        'interstitial' => 'position: fixed; inset: 0; z-index: 1100; background: rgba(0,0,0,0.85); display: flex; justify-content: center; align-items: center;'
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
    style="{{ $style }}"
    x-data="adDisplayData({{ $ad->id }}, '{{ $type }}', '{{ $placement }}', {{ $delay }}, {{ json_encode($targeting) }}, '{{ $link }}', {{ $isActive ? 'true' : 'false' }})"
    x-show="isVisible"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    x-intersect.once="trackAdView()"
>
    @switch($type)
        @case('image')
            @include('components.ads.image-ad', ['ad' => $ad, 'adId' => $adId])
            @break
        
        @case('video')
            @include('components.ads.video-ad', ['ad' => $ad, 'adId' => $adId])
            @break

        @case('banner')
                       @include('components.ads.video-ad', ['ad' => $ad, 'adId' => $adId])

            @break
        
        @case('js')
            <div class="w-full h-full">
                {!! $content !!}
            </div>
            @break
        
        @case('popup')
              
                @include('components.ads.popup-ad', ['ad' => $ad, 'adId' => $adId])
        
            @break
        
        @case('persistent')
            <div class="relative">
                <button x-show="showCloseBtn" @click="closeAd" 
                        class="absolute top-2 right-2 bg-gray-800 text-white w-6 h-6 rounded-full flex items-center justify-center z-50">
                    &times;
                </button>
                @if($link)
                    <a href="{{ $link }}" target="_blank" class="block w-full h-full" @click="trackAdClick">
                        {!! $content !!}
                    </a>
                @else
                    {!! $content !!}
                @endif
            </div>
            @break
        
        @case('interstitial')
            <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full relative overflow-hidden">
                <button x-show="showCloseBtn" @click="closeAd" 
                        class="absolute top-4 right-4 bg-gray-800 text-white w-8 h-8 rounded-full flex items-center justify-center text-xl z-50">
                    &times;
                </button>
                @if($link)
                    <a href="{{ $link }}" target="_blank" class="block w-full h-full" @click="trackAdClick">
                        {!! $content !!}
                    </a>
                @else
                    {!! $content !!}
                @endif
            </div>
            @break
        
        @default
            <div class="w-full h-full p-4 bg-gray-50 border rounded-lg">
                <a href="{{ $link }}" target="_blank" class="block w-full h-full" @click="trackAdClick">
                    <h3 class="text-lg font-bold">{{ $ad->title }}</h3>
                    <p class="mt-2">{{ $content }}</p>
                </a>
            </div>
    @endswitch
</div>

@if(!$isActive)
    <!-- Inactive ad placeholder -->
    <div class="bg-gray-100 p-2 text-center text-sm text-gray-500">
        Ad (ID: {{ $ad->id }}) is not currently active
    </div>
@endif

<script>
// Define this in your main layout or a separate JS file
function adDisplayData(adId, type, placement, delay, targeting, link, isActive) {
    return {
        isVisible: false,
        isClosed: false,
        showCloseBtn: false,
        timeSpent: 0,
        timeTracker: null,
        init() {
            if (!isActive) return;
            if (!this.checkTargeting(targeting)) return;
            
            setTimeout(() => {
                this.showAd();
            }, delay * 1000);
        },
        showAd() {
            if (this.isClosed) return;
            this.isVisible = true;
            this.$nextTick(() => {
                this.setupTimeTracking();
                this.setupClickTracking();
                
                if (['popup', 'interstitial', 'persistent'].includes(type)) {
                    setTimeout(() => {
                        this.showCloseBtn = true;
                    }, 5000);
                }
                
                if (type === 'popup') {
                    setTimeout(() => {
                        this.closeAd();
                    }, 15000);
                }
            });
        },
        checkTargeting(targeting) {
            if (!targeting || Object.keys(targeting).length === 0) return true;
            
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
        },
       
        trackAdView() {
            fetch('/ads/track/view', {
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
                    },
                    placement: placement,
                    session_id: this.getSessionId()
                })
            });
        },
        trackAdClick() {
            fetch('/ads/track/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ad_id: adId,
                    timestamp: Date.now(),
                    url: window.location.href,
                    target_url: link,
                    placement: placement,
                    session_id: this.getSessionId()
                })
            });
        },
        trackAdClose() {
            fetch('/ads/track/close', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    ad_id: adId,
                    timestamp: Date.now(),
                    placement: placement,
                    session_id: this.getSessionId(),
                    time_spent: this.timeSpent
                })
            });
        },
        setupClickTracking() {
            const container = this.$el;
            const clickableElements = container.querySelectorAll('a, button, [data-clickable]');
            
            clickableElements.forEach(element => {
                element.addEventListener('click', (e) => {
                    if (element.tagName === 'A' && link) {
                        e.preventDefault();
                        this.trackAdClick();
                        setTimeout(() => {
                            window.location.href = link;
                        }, 200);
                    } else {
                        this.trackAdClick();
                    }
                });
            });
        },
        closeAd() {
            clearInterval(this.timeTracker);
            this.isVisible = false;
            this.isClosed = true;
            this.trackAdClose();
        },
        getSessionId() {
            let sessionId = localStorage.getItem('ad_session_id');
            if (!sessionId) {
                sessionId = Math.random().toString(36).substring(2) + Date.now().toString(36);
                localStorage.setItem('ad_session_id', sessionId);
            }
            return sessionId;
        }
    };
}
</script>
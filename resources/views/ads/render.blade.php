@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Active Ads</h1>

    {{-- Loop through all ads --}}
    @foreach($ads as $ad)
        @php
            $type = $ad->type; // e.g., 'popup', 'banner', 'sticky', 'video'
            $duration = $ad->duration ?? 10; // seconds
            $persistent = $ad->persistent ?? false;
        @endphp

        {{-- Inline Banner Ad --}}
        @if($type === 'banner')
            <div 
                class="relative w-full p-4 mb-4 bg-yellow-100 border border-yellow-400 rounded-lg flex justify-between items-center shadow-md"
                data-duration="{{ $duration }}" data-persistent="{{ $persistent }}">
                <div class="flex items-center space-x-3">
                    <i class="bi bi-megaphone-fill text-yellow-600 text-xl"></i>
                    <span class="font-semibold">{{ $ad->title }}</span>
                </div>
                <a href="{{ $ad->link }}" target="_blank" class="text-blue-600 hover:underline">Learn more</a>
            </div>
        @endif

        {{-- Popup Ad --}}
        @if($type === 'popup')
            <div 
                class="fixed inset-0 flex items-center justify-center bg-black/50 z-50 hidden popup-ad"
                data-duration="{{ $duration }}" data-persistent="{{ $persistent }}">
                <div class="bg-white rounded-lg p-6 w-96 shadow-lg text-center relative">
                    <button class="absolute top-2 right-2 text-gray-500 hover:text-red-600 close-popup">
                        <i class="bi bi-x-circle-fill"></i>
                    </button>
                    <h2 class="text-lg font-bold mb-2">{{ $ad->title }}</h2>
                    <p class="mb-4">{{ $ad->content }}</p>
                    <a href="{{ $ad->link }}" target="_blank" 
                        class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        Visit Now
                    </a>
                </div>
            </div>
        @endif

        {{-- Sticky Bottom Ad --}}
        @if($type === 'sticky')
            <div 
                class="fixed bottom-0 inset-x-0 bg-green-100 border-t border-green-400 p-4 flex justify-between items-center shadow-lg sticky-ad"
                data-duration="{{ $duration }}" data-persistent="{{ $persistent }}">
                <span>{{ $ad->content }}</span>
                <a href="{{ $ad->link }}" target="_blank" class="text-blue-600 hover:underline">Check it out</a>
            </div>
        @endif

        {{-- Video Ad --}}
        @if($type === 'video')
            <div 
                class="relative w-full max-w-xl mx-auto mb-6 border border-gray-300 rounded-lg overflow-hidden shadow-lg video-ad"
                data-duration="{{ $duration }}" data-persistent="{{ $persistent }}">
                <video autoplay muted class="w-full">
                    <source src="{{ $ad->video_url }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
                <a href="{{ $ad->link }}" target="_blank" 
                   class="absolute bottom-2 right-2 bg-blue-600 text-white px-3 py-1 rounded text-sm hover:bg-blue-700">
                   Visit Site
                </a>
            </div>
        @endif
    @endforeach
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-duration]').forEach(ad => {
        let duration = parseInt(ad.getAttribute('data-duration')) * 1000;
        let persistent = ad.getAttribute('data-persistent') === 'true';

        // Show popup if needed
        if (ad.classList.contains('popup-ad')) {
            ad.classList.remove('hidden');
            ad.querySelector('.close-popup').addEventListener('click', () => ad.remove());
        }

        // Auto-remove if not persistent
        if (!persistent && duration > 0) {
            setTimeout(() => {
                ad.remove();
            }, duration);
        }
    });
});
</script>
@endsection

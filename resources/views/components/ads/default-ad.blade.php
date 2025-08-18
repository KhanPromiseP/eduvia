@props(['ad'])

@php
    $type = $ad->type;
    $content = $ad->content;
    $link = $ad->link;
@endphp

<div 
    class="ad-container bg-white rounded shadow-md border border-gray-300 overflow-hidden max-w-sm mx-auto" 
    style="width: 300px; height: 250px; position: relative;"
>
    @if($link)
        <a href="{{ $link }}" target="_blank" rel="noopener noreferrer" class="block w-full h-full">
    @endif

    @switch($type)
        @case('image')
            <img src="{{ $content }}" alt="{{ $ad->title }}" class="w-full h-full object-cover" />
            @break

        @case('video')
            <video controls class="w-full h-full object-cover">
                <source src="{{ $content }}" type="video/mp4">
                Your browser does not support the video tag.
            </video>
            @break

        @case('banner')
            {!! $content !!}
            @break

        @case('js')
            <script>{!! $content !!}</script>
            @break

        @case('popup')
            {{-- Popup handled separately; don't display inline --}}
            <div class="text-center text-sm text-gray-500">Popup Ad: {{ $ad->title }}</div>
            @break

        @case('persistent')
            <div class="fixed bottom-0 left-0 w-full bg-gray-800 text-white p-4 text-center z-40">
                {!! $content !!}
            </div>
            @break

        @case('interstitial')
            {{-- Interstitial handled separately; don't display inline --}}
            <div class="text-center text-sm text-gray-500">Interstitial Ad: {{ $ad->title }}</div>
            @break

        @default
            <div class="text-gray-600 italic p-4">Unsupported ad type.</div>
    @endswitch

    @if($link)
        </a>
    @endif

    {{-- Optional: Ad label --}}
    <div class="absolute bottom-0 right-0 bg-gray-100 text-gray-600 text-xs px-1 py-0.5 font-semibold select-none" style="font-family: Arial, sans-serif;">
        Ad
    </div>
</div>

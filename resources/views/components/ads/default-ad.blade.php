<div class="ad-default-container bg-gray-100 border-2 border-dashed border-gray-300 rounded-lg flex items-center justify-center" 
     style="width: 300px; height: 250px; position: relative;">
    <div class="text-center p-6">
        <div class="text-gray-400 text-sm mb-2">Advertisement</div>
        <div class="text-gray-600 font-semibold">{{ $ad->title }}</div>
        @if($ad->content)
            <div class="text-gray-500 text-xs mt-2">
                {!! Str::limit($ad->content, 50) !!}
            </div>
        @endif
    </div>
    <div class="ad-label">Ad</div>
</div>
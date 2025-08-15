<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-gray-900">
            {{ __("You're logged in!") }}

            {{-- Display Ads --}}
            {{-- @if($ads->isNotEmpty())
                <div class="mt-8">
                    <h3 class="text-lg font-semibold mb-4">Sponsored Ads</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                        @foreach($ads as $ad)
                            <div>
                                {{-- <x-ads.display-ad :ad="$ad" /> --}}

                                {{-- <div class="mt-1 text-xs text-gray-500 text-center font-mono">
                                    @if($ad->start_at)
                                        <span>Start: {{ \Carbon\Carbon::parse($ad->start_at)->format('M d, Y') }}</span>
                                    @endif
                                    @if($ad->end_at)
                                        <span class="ml-2">End: {{ \Carbon\Carbon::parse($ad->end_at)->format('M d, Y') }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @else  --}}
                {{-- <div class="mt-8 text-gray-500 italic">No ads available currently.</div>
            @endif --}}
        </div>
    </div>
</x-app-layout>

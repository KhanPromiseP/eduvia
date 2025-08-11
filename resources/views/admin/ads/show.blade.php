<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Ad Details: {{ $ad->title }}
        </h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-6 px-4 sm:px-6 lg:px-8 space-y-6">

        {{-- Ad Basic Info --}}
        <section class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4 text-gray-700">
                <div>
                    <dt class="font-medium">Title:</dt>
                    <dd>{{ $ad->title }}</dd>
                </div>
                <div>
                    <dt class="font-medium">Type:</dt>
                    <dd class="capitalize">{{ $ad->type }}</dd>
                </div>
                <div>
                    <dt class="font-medium">Creator:</dt>
                    <dd>{{ $ad->user->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="font-medium">Product:</dt>
                    <dd>{{ $ad->product->name ?? 'N/A' }}</dd>
                </div>
                <div>
                    <dt class="font-medium">Status:</dt>
                    <dd>
                        @if($ad->isCurrentlyActive())
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                <i class="bi bi-check-circle-fill me-1"></i> Active
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                <i class="bi bi-x-circle-fill me-1"></i> Inactive
                            </span>
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-medium">Start Date:</dt>
                    <dd>{{ $ad->start_at?->format('Y-m-d') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium">End Date:</dt>
                    <dd>{{ $ad->end_at?->format('Y-m-d') ?? '-' }}</dd>
                </div>
                <div>
                    <dt class="font-medium">Placement:</dt>
                    <dd>{{ $ad->placement ?? '-' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="font-medium">Targeting:</dt>
                    <dd>
                        @if(is_array($ad->targeting))
                            <pre class="whitespace-pre-wrap bg-gray-100 p-2 rounded text-sm text-gray-700">{{ json_encode($ad->targeting, JSON_PRETTY_PRINT) }}</pre>
                        @else
                            -
                        @endif
                    </dd>
                </div>
                <div>
                    <dt class="font-medium">Is Random Placement:</dt>
                    <dd>{{ $ad->is_random ? 'Yes' : 'No' }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="font-medium">Content:</dt>
                    <dd class="break-words">{{ $ad->content }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="font-medium">Link:</dt>
                    <dd>
                        @if($ad->link)
                            <a href="{{ $ad->link }}" target="_blank" rel="noopener" class="text-blue-600 hover:underline">
                                {{ $ad->link }}
                            </a>
                        @else
                            -
                        @endif
                    </dd>
                </div>
            </dl>
        </section>

        {{-- Analytics Summary --}}
        <section class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-semibold mb-4">Analytics Summary</h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 text-center text-gray-700">
                <div class="p-4 bg-blue-50 rounded">
                    <div class="text-3xl font-bold">{{ number_format($analytics['total_views']) }}</div>
                    <div class="mt-1 uppercase font-semibold text-sm text-blue-700 flex justify-center items-center gap-1">
                        <i class="bi bi-eye-fill"></i> Views
                    </div>
                </div>
                <div class="p-4 bg-green-50 rounded">
                    <div class="text-3xl font-bold">{{ number_format($analytics['total_clicks']) }}</div>
                    <div class="mt-1 uppercase font-semibold text-sm text-green-700 flex justify-center items-center gap-1">
                        <i class="bi bi-mouse-fill"></i> Clicks
                    </div>
                </div>
                <div class="p-4 bg-yellow-50 rounded">
                    <div class="text-3xl font-bold">{{ number_format($analytics['total_impressions']) }}</div>
                    <div class="mt-1 uppercase font-semibold text-sm text-yellow-700 flex justify-center items-center gap-1">
                        <i class="bi bi-graph-up-arrow"></i> Impressions
                    </div>
                </div>
            </div>

            <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div class="p-4 bg-gray-50 rounded border border-gray-200 text-center">
                    <h4 class="text-sm font-semibold mb-2">Click-Through Rate (CTR)</h4>
                    <div class="text-2xl font-bold text-indigo-700">{{ $analytics['ctr'] }}%</div>
                </div>
                <div class="p-4 bg-gray-50 rounded border border-gray-200 text-center">
                    <h4 class="text-sm font-semibold mb-2">Average View Duration (seconds)</h4>
                    <div class="text-2xl font-bold text-indigo-700">{{ number_format($analytics['average_view_duration'], 2) }}</div>
                </div>
            </div>
        </section>

        <div class="flex space-x-4">
            <a href="{{ route('admin.ads.edit', $ad) }}" class="inline-flex items-center rounded bg-yellow-500 px-4 py-2 text-white hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400">
                <i class="bi bi-pencil-fill me-2"></i> Edit Ad
            </a>
            <a href="{{ route('admin.ads.index') }}" class="inline-flex items-center rounded bg-gray-300 px-4 py-2 text-gray-700 hover:bg-gray-400">
                <i class="bi bi-arrow-left-circle me-2"></i> Back to List
            </a>
        </div>

    </div>
</x-admin-layout>

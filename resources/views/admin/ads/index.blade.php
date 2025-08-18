@extends('layouts.admin')

@section('header')
    <h2 class="text-2xl font-semibold leading-tight text-gray-800">
        Ads Management
    </h2>
@endsection

@section('content')

<div class="container mx-auto px-4 py-8">
    <div class="max-w-7xl mx-auto">
        {{-- Success message --}}
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg flex items-center">
                <i class="bi bi-check-circle-fill mr-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.ads.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">
            {{-- Status --}}
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select id="status" name="status" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            {{-- Search by Title --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="search"
                    id="search"
                    name="search"
                    placeholder="Search by title..."
                    value="{{ request('search') }}"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- Placement --}}
            <div>
                <label for="placement" class="block text-sm font-medium text-gray-700 mb-1">Placement</label>
                <select id="placement" name="placement" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Any Placement</option>
                    @foreach($placements as $value => $label)
                        <option value="{{ $value }}" {{ request('placement') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Ad Type</label>
                <select id="type" name="type" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Types</option>
                    @foreach($adTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Start Date --}}
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input 
                    type="datetime-local" 
                    id="start_date"
                    name="start_date" 
                    value="{{ request('start_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- End Date --}}
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input 
                    type="datetime-local" 
                    id="end_date"
                    name="end_date" 
                    value="{{ request('end_date') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                >
            </div>

            {{-- Weight --}}
            <div>
                <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight</label>
                <input 
                    type="number" 
                    id="weight"
                    name="weight" 
                    min="1" 
                    max="10" 
                    value="{{ request('weight') }}" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="1-10"
                >
            </div>

            {{-- Random Display --}}
            <div class="flex items-center mt-6">
                <input 
                    type="checkbox" 
                    id="is_random" 
                    name="is_random" 
                    value="1" 
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    {{ request('is_random') ? 'checked' : '' }}
                >
                <label for="is_random" class="ml-2 text-sm text-gray-700">Random Display</label>
            </div>

            {{-- Submit and Reset Buttons --}}
            <div class="flex space-x-4 mt-6 md:col-span-4">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-blue-500 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                
                    <i class="bi bi-funnel-fill mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.ads.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="bi bi-arrow-counterclockwise mr-2"></i> Reset
                </a>
            </div>
        </form>

        {{-- Create New Ad Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.ads.create') }}" 
               class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="bi bi-plus-circle mr-2"></i> Create New Ad
            </a>
        </div>


        {{-- Ads Table --}}
       <div class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200 table-fixed">
        <thead class="bg-gray-50">
            <tr>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Type</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Creator</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Product</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Placement</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Status</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Start Date</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">End Date</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Weight</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Impressions</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Clicks</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Random</th>
                <th class="w-[100px] px-2 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Type</th>
                <th class="w-[100px] px-2 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider overflow-x-auto whitespace-nowrap">Actions</th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($ads as $ad)
            <tr class="hover:bg-gray-50">
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->title }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap capitalize">{{ $ad->type }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->user->name ?? 'N/A' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->product->name ?? 'N/A' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap capitalize">{{ $ad->placement ?? 'Any' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">
                    @if($ad->is_active)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                            Active
                        </span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                            Inactive
                        </span>
                    @endif
                </td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->start_at?->format('Y-m-d H:i') ?? '-' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->end_at?->format('Y-m-d H:i') ?? '-' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->weight ?? '-' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->max_impressions ?? 'Unlimited' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->max_clicks ?? 'Unlimited' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">{{ $ad->is_random ? 'Yes' : 'No' }}</td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap">
                    @if($ad->type === 'image')
                        <img src="{{ $ad->content }}" alt="{{ $ad->title }}" class="h-16 w-auto">
                    @elseif($ad->type === 'video')
                        <video src="{{ $ad->content }}" class="h-16 w-auto" controls></video>
                    @else
                        <span>{{ $ad->content }}</span>
                    @endif
                </td>
                <td class="w-[100px] px-2 py-4 overflow-x-auto whitespace-nowrap text-center space-x-2">
                    <a href="{{ route('admin.ads.show', $ad->id) }}" class="text-blue-600 hover:text-blue-900">View</a>
                    <a href="{{ route('admin.ads.edit', $ad->id) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                   <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" 
                        class="inline"
                        onsubmit="return confirm('Are you sure you want to delete this ad? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>

                </td>
            </tr>
            @empty
            <tr>
                <td colspan="13" class="px-6 py-4 text-center text-gray-500">
                    No ads found.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

        {{-- Pagination --}}
    @if($ads instanceof \Illuminate\Pagination\AbstractPaginator && $ads->hasPages())
    <div class="mt-4 px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
        <div class="flex-1 flex justify-between sm:hidden">
            @if($ads->onFirstPage())
                <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white opacity-50 cursor-not-allowed">
                    Previous
                </span>
            @else
                <a href="{{ $ads->previousPageUrl() }}" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Previous
                </a>
            @endif

            @if($ads->hasMorePages())
                <a href="{{ $ads->nextPageUrl() }}" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                    Next
                </a>
            @else
                <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white opacity-50 cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>
        <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
            <div>
                <p class="text-sm text-gray-700">
                    Showing
                    <span class="font-medium">{{ $ads->firstItem() }}</span>
                    to
                    <span class="font-medium">{{ $ads->lastItem() }}</span>
                    of
                    <span class="font-medium">{{ $ads->total() }}</span>
                    results
                </p>
            </div>
            <div>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    {{ $ads->links() }}
                </nav>
            </div>
        </div>
    </div>
    @endif
    </div>
</div>
@endsection
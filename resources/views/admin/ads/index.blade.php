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
        <form method="GET" action="{{ route('admin.ads.index') }}" class="mb-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
         
            {{-- Search by Title --}}
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input
                    type="search"
                    id="search"
                    name="search"
                    placeholder="Search by title..."
                    value="{{ request('search') }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                >
            </div>

            {{-- Placement --}}
            <div>
                <label for="placement" class="block text-sm font-medium text-gray-700 mb-1">Placement</label>
                <select id="placement" name="placement" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">Any Placement</option>
                    @foreach($placements as $value => $label)
                        <option value="{{ $value }}" {{ request('placement') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Type --}}
            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Ad Type</label>
                <select id="type" name="type" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm">
                    <option value="">All Types</option>
                    @foreach($adTypes as $value => $label)
                        <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
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
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                    placeholder="1-10"
                >
            </div>

            {{-- Random Display --}}
            <div class="flex items-center">
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
            <div class="flex space-x-2 sm:col-span-2 lg:col-span-3 xl:col-span-4">
                <button
                    type="submit"
                    class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="bi bi-funnel-fill mr-2"></i> Filter
                </button>
                <a href="{{ route('admin.ads.index') }}" 
                   class="inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <i class="bi bi-arrow-counterclockwise mr-2"></i> Reset
                </a>
            </div>
        </form>

        {{-- Create New Ad Button --}}
        <div class="mb-6">
            <a href="{{ route('admin.ads.create') }}" 
               class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="bi bi-plus-circle mr-2"></i> Create New Ad
            </a>
        </div>

        {{-- Desktop Table (hidden on mobile) --}}
        <div class="hidden md:block overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Placement</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ads as $ad)
                    <tr class="hover:bg-gray-50">
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ Str::limit($ad->title, 20) }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $ad->type }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ad->user->name ?? 'N/A' }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500 capitalize">{{ $ad->placement ?? 'Any' }}</td>
                        <td class="px-3 py-4 whitespace-nowrap">
                            @if($ad->is_active)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800">
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ad->start_at?->format('M j, Y') ?? '-' }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-500">{{ $ad->end_at?->format('M j, Y') ?? '-' }}</td>
                        <td class="px-3 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <a href="{{ route('admin.ads.show', $ad->id) }}" class="text-blue-600 hover:text-blue-900" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.ads.edit', $ad->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" class="inline"
                                    onsubmit="return confirm('Are you sure you want to delete this ad? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            No ads found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (shown on mobile) --}}
        <div class="md:hidden space-y-4">
            @forelse($ads as $ad)
            <div class="bg-white rounded-lg shadow p-4">
                <div class="flex justify-between items-start mb-3">
                    <h3 class="text-sm font-medium text-gray-900">{{ Str::limit($ad->title, 30) }}</h3>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium {{ $ad->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $ad->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                
                <div class="grid grid-cols-2 gap-2 text-xs text-gray-600 mb-3">
                    <div>
                        <span class="font-medium">Type:</span> {{ $ad->type }}
                    </div>
                    <div>
                        <span class="font-medium">Creator:</span> {{ $ad->user->name ?? 'N/A' }}
                    </div>
                    <div>
                        <span class="font-medium">Placement:</span> {{ $ad->placement ?? 'Any' }}
                    </div>
                    <div>
                        <span class="font-medium">Weight:</span> {{ $ad->weight ?? '-' }}
                    </div>
                </div>

                <div class="text-xs text-gray-600 mb-3">
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <span class="font-medium">Start:</span> {{ $ad->start_at?->format('M j, Y') ?? '-' }}
                        </div>
                        <div>
                            <span class="font-medium">End:</span> {{ $ad->end_at?->format('M j, Y') ?? '-' }}
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                    <div class="text-xs text-gray-500">
                        {{ $ad->is_random ? 'Random Display: Yes' : '' }}
                    </div>
                    <div class="flex space-x-3">
                        <a href="{{ route('admin.ads.show', $ad->id) }}" class="text-blue-600 hover:text-blue-900" title="View">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('admin.ads.edit', $ad->id) }}" class="text-yellow-600 hover:text-yellow-900" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('admin.ads.destroy', $ad->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Are you sure you want to delete this ad? This action cannot be undone.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="bg-white rounded-lg shadow p-6 text-center">
                <p class="text-gray-500">No ads found.</p>
            </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($ads instanceof \Illuminate\Pagination\AbstractPaginator && $ads->hasPages())
        <div class="mt-6 px-4 py-3 flex items-center justify-between border-t border-gray-200">
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

<style>
@media (max-width: 640px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    .grid-cols-1 {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .text-xs {
        font-size: 0.75rem;
    }
    
    .p-4 {
        padding: 1rem;
    }
}

.fas {
    font-size: 14px;
}
</style>

@endsection
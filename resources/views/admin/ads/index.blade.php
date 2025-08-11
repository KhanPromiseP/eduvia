<x-admin-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
            Ads Management
        </h2>
    </x-slot>

    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        {{-- Success message --}}
        @if(session('success'))
            <div class="mb-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Filters --}}
        <form method="GET" action="{{ route('admin.ads.index') }}" class="mb-6 grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Status --}}
            <select name="status" class="rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Statuses</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            {{-- Search --}}
            <input
                type="search"
                name="search"
                placeholder="Search by title..."
                value="{{ request('search') }}"
                class="rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >

            {{-- Placement --}}
            <input
                type="text"
                name="placement"
                placeholder="Filter by placement"
                value="{{ request('placement') }}"
                class="rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
            >

            {{-- Type --}}
            <select name="type" class="rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">All Types</option>
                @foreach(['image', 'video', 'banner', 'js', 'popup', 'persistent', 'interstitial'] as $type)
                    <option value="{{ $type }}" {{ request('type') === $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>

            {{-- Start Date --}}
            <input 
                type="date" 
                name="start_date" 
                value="{{ request('start_date') }}" 
                class="rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500 md:col-span-2"
                placeholder="Start date"
            >

            {{-- End Date --}}
            <input 
                type="date" 
                name="end_date" 
                value="{{ request('end_date') }}" 
                class="rounded border-gray-300 shadow-sm focus:ring-blue-500 focus:border-blue-500"
                placeholder="End date"
            >

            {{-- Submit Button --}}
            <button
                type="submit"
                class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
            >
                <i class="bi bi-funnel-fill me-2"></i> Filter
            </button>

            {{-- Reset Button --}}
            <a href="{{ route('admin.ads.index') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="bi bi-arrow-counterclockwise me-2"></i> Reset
            </a>
        </form>

        {{-- Create New Ad Button --}}
        <div class="mb-4">
            <a href="{{ route('admin.ads.create') }}" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                <i class="bi bi-plus-circle me-2"></i> Create New Ad
            </a>
        </div>

        {{-- Ads Table --}}
        <div class="overflow-x-auto bg-white rounded shadow">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Title</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Creator</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">End Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($ads as $ad)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ad->title }}</td>
                            <td class="px-6 py-4 whitespace-nowrap capitalize">{{ $ad->type }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ad->user->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ad->product->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($ad->isCurrentlyActive())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                        <i class="bi bi-check-circle-fill me-1"></i> Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                        <i class="bi bi-x-circle-fill me-1"></i> Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ad->start_at?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">{{ $ad->end_at?->format('Y-m-d') ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center space-x-2">
                                <a href="{{ route('admin.ads.show', $ad) }}" title="View" class="text-blue-600 hover:text-blue-900">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.ads.edit', $ad) }}" title="Edit" class="text-yellow-600 hover:text-yellow-900">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('admin.ads.destroy', $ad) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this ad?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
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
</x-admin-layout>
@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 bg-white border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900">Manage Categories</h3>
                <a href="{{ route('admin.categories.create') }}"
                   class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Add New Category
                </a>
            </div>

            <!-- Table (Desktop) -->
            <div class="overflow-x-auto hidden sm:block">
                <table class="min-w-full divide-y divide-gray-200 table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Slug</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Users</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Description</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($categories as $category)
                            <tr class="hover:bg-gray-50">
                                <!-- Name -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                </td>

                                <!-- Slug -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $category->slug }}
                                </td>

                                <!-- Users Count -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $category->users_count }} users
                                </td>

                                <!-- Description -->
                                <td class="px-4 py-3 text-sm text-gray-500">
                                    {{ Str::limit($category->description, 50) }}
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('admin.categories.show', $category) }}" 
                                        class="text-indigo-600 hover:text-indigo-900" 
                                        aria-label="View" 
                                        title="View Category">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.categories.edit', $category) }}" 
                                        class="text-blue-600 hover:text-blue-900" 
                                        aria-label="Edit" 
                                        title="Edit Category">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form action="{{ route('admin.categories.destroy', $category) }}" 
                                            method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this category?')" 
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    aria-label="Delete" 
                                                    title="Delete Category">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">
                                    No categories found. <a href="{{ route('admin.categories.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first category</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards (sm:hidden) -->
            <div class="sm:hidden px-4 space-y-4 mt-6">
                @foreach ($categories as $category)
                    <div class="border border-gray-200 rounded-lg p-4 flex space-x-4 items-start shadow-sm bg-white">
                        <!-- Details -->
                        <div class="flex-grow min-w-0">
                            <div class="text-base font-semibold text-gray-900 truncate">{{ $category->name }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ $category->slug }}</div>

                            <div class="mt-2 flex items-center justify-between text-sm text-gray-600">
                                <div>{{ $category->users_count }} users</div>
                            </div>

                            <div class="mt-2 text-sm text-gray-500">
                                {{ Str::limit($category->description, 50) }}
                            </div>

                            <div class="mt-3 flex space-x-3">
                                <a href="{{ route('admin.categories.show', $category) }}" 
                                class="text-indigo-600 hover:text-indigo-900" 
                                aria-label="View" 
                                title="View Category">
                                    <i class="fas fa-eye"></i>
                                </a>

                                <a href="{{ route('admin.categories.edit', $category) }}" 
                                class="text-blue-600 hover:text-blue-900" 
                                aria-label="Edit" 
                                title="Edit Category">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <form action="{{ route('admin.categories.destroy', $category) }}" 
                                    method="POST" 
                                    onsubmit="return confirm('Are you sure you want to delete this category?')" 
                                    class="inline-block">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900" 
                                            aria-label="Delete" 
                                            title="Delete Category">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="px-4 py-4 bg-white border-t border-gray-200 flex justify-center">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
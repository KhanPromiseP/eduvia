@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">

            <!-- Header -->
            <div class="px-4 sm:px-6 py-4 bg-white border-b border-gray-200 flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-3 sm:space-y-0">
                <h3 class="text-lg font-medium text-gray-900">Manage Courses</h3>
                <a href="{{ route('admin.courses.create') }}"
                   class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    <i class="fas fa-plus mr-2"></i> Add New Course
                </a>
            </div>

            <!-- Table (Desktop) -->
            <div class="overflow-x-auto hidden sm:block">
                <table class="min-w-full divide-y divide-gray-200 table-auto text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Title</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Price</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Modules</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($courses as $course)
                            <tr class="hover:bg-gray-50">
                                <!-- Title -->
                                <td class="px-4 py-3 whitespace-nowrap flex items-center">
                                    @if($course->image)
                                        <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-10 w-10 rounded-full object-cover mr-3">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center mr-3">
                                            <i class="fas fa-book text-indigo-600"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $course->title }}</div>
                                        <div class="text-sm text-gray-500">{{ Str::limit($course->description, 50) }}</div>
                                    </div>
                                </td>

                                <!-- Price -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    ${{ number_format($course->price, 2) }}
                                </td>

                                <!-- Modules -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ $course->modules_count }} modules
                                </td>

                                <!-- Status -->
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>

                                <!-- Actions -->
                                <td class="px-4 py-3 whitespace-nowrap text-sm font-medium">
                                    <div class="mt-3 flex space-x-3">
                                        <a href="{{ route('admin.courses.show', $course) }}" 
                                        class="text-indigo-600 hover:text-indigo-900" 
                                        aria-label="View" 
                                        title="View Course">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.courses.edit', $course) }}" 
                                        class="text-blue-600 hover:text-blue-900" 
                                        aria-label="Edit" 
                                        title="Edit Course">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="{{ route('admin.courses.modules', $course) }}" 
                                        class="text-purple-600 hover:text-purple-900" 
                                        aria-label="Modules" 
                                        title="Manage Modules">
                                            <i class="fas fa-layer-group"></i>
                                        </a>

                                        <form action="{{ route('admin.courses.toggle-publish', $course) }}" 
                                            method="POST" class="inline-block">
                                            @csrf
                                            <button type="submit" 
                                                    class="text-gray-600 hover:text-gray-900" 
                                                    aria-label="Toggle Publish" 
                                                    title="{{ $course->is_published ? 'Unpublish Course' : 'Publish Course' }}">
                                                <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                            </button>
                                        </form>

                                        <form action="{{ route('admin.courses.destroy', $course) }}" 
                                            method="POST" 
                                            onsubmit="return confirmDelete()" 
                                            class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900" 
                                                    aria-label="Delete" 
                                                    title="Delete Course">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>

                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center text-sm text-gray-500">
                                    No courses found. <a href="{{ route('admin.courses.create') }}" class="text-indigo-600 hover:text-indigo-900">Create your first course</a>.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards (sm:hidden) -->
            <div class="sm:hidden px-4 space-y-4 mt-6">
                @foreach ($courses as $course)
                    <div class="border border-gray-200 rounded-lg p-4 flex space-x-4 items-start shadow-sm bg-white">
                        <!-- Image -->
                        <div class="flex-shrink-0">
                            @if($course->image)
                                <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="h-12 w-12 rounded-full object-cover">
                            @else
                                <div class="h-12 w-12 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600">
                                    <i class="fas fa-book"></i>
                                </div>
                            @endif
                        </div>

                        <!-- Details -->
                        <div class="flex-grow min-w-0">
                            <div class="text-base font-semibold text-gray-900 truncate">{{ $course->title }}</div>
                            <div class="text-sm text-gray-500 truncate">{{ Str::limit($course->description, 50) }}</div>

                            <div class="mt-2 flex items-center justify-between text-sm text-gray-600">
                                <div>${{ number_format($course->price, 2) }}</div>
                                <div>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $course->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                        {{ $course->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </div>
                            </div>

                           <div class="mt-3 flex space-x-3">
                            <a href="{{ route('admin.courses.show', $course) }}" 
                            class="text-indigo-600 hover:text-indigo-900" 
                            aria-label="View" 
                            title="View Course">
                                <i class="fas fa-eye"></i>
                            </a>

                            <a href="{{ route('admin.courses.edit', $course) }}" 
                            class="text-blue-600 hover:text-blue-900" 
                            aria-label="Edit" 
                            title="Edit Course">
                                <i class="fas fa-edit"></i>
                            </a>

                            <a href="{{ route('admin.courses.modules', $course) }}" 
                            class="text-purple-600 hover:text-purple-900" 
                            aria-label="Modules" 
                            title="Manage Modules">
                                <i class="fas fa-layer-group"></i>
                            </a>

                            <form action="{{ route('admin.courses.toggle-publish', $course) }}" 
                                method="POST" class="inline-block">
                                @csrf
                                <button type="submit" 
                                        class="text-gray-600 hover:text-gray-900" 
                                        aria-label="Toggle Publish" 
                                        title="{{ $course->is_published ? 'Unpublish Course' : 'Publish Course' }}">
                                    <i class="fas {{ $course->is_published ? 'fa-eye-slash' : 'fa-eye' }}"></i>
                                </button>
                            </form>

                            <form action="{{ route('admin.courses.destroy', $course) }}" 
                                method="POST" 
                                onsubmit="return confirmDelete()" 
                                class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-red-600 hover:text-red-900" 
                                        aria-label="Delete" 
                                        title="Delete Course">
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
                {{ $courses->links() }}
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete() {
    return confirm('Are you sure you want to delete this course?');
}
</script>
@endsection

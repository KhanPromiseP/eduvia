@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow-sm sm:rounded-lg overflow-hidden">
            <div class="px-4 sm:px-6 py-4 bg-white border-b border-gray-200 flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Category Details</h3>
                <div class="flex space-x-3">
                    <a href="{{ route('admin.categories.edit', $category) }}"
                        class="inline-flex items-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="inline-flex items-center px-3 py-1 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <i class="fas fa-arrow-left mr-1"></i> Back
                    </a>
                </div>
            </div>

            <div class="px-4 sm:px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Category Details -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Category Information</h4>
                        
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Name</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->name }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->slug }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Description</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->description ?? 'No description' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Total Users</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->users_count }} users</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Created At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->created_at->format('M d, Y H:i') }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Updated At</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ $category->updated_at->format('M d, Y H:i') }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- Users List -->
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <h4 class="text-md font-medium text-gray-900 mb-4">Users Interested in This Category</h4>
                        
                        @if($category->users->count() > 0)
                            <div class="space-y-3">
                                @foreach($category->users as $user)
                                    <div class="flex items-center justify-between p-3 bg-white rounded-lg shadow-sm">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex-shrink-0">
                                                <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                    <span class="text-indigo-600 font-medium">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                                </div>
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            {{ $user->country ?? 'Unknown' }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-sm text-gray-500">No users have selected this category as an interest yet.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
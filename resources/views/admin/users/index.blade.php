@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Manage Users</h1>
            <p class="text-gray-600 mt-2">Manage all platform users, their roles, and permissions</p>
        </div>
        <div class="flex items-center space-x-4">
            <div class="text-sm text-gray-600">
                Total Users: <span class="font-semibold">{{ $users->total() }}</span>
            </div>
            <a href="{{ route('admin.users.create') }}"
               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition shadow-md">
                <i class="fas fa-plus mr-2"></i> Add New User
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" 
                       placeholder="Search users by name, email, or role..." 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            <div class="flex space-x-2">
                <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="">All Roles</option>
                    <option value="admin">Admin</option>
                    <option value="instructor">Instructor</option>
                    <option value="student">Student</option>
                </select>
                
                <button class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">
                    <i class="fas fa-filter mr-2"></i>Filter
                </button>
            </div>
        </div>
    </div>

    @if($users->count() > 0)
        <!-- Desktop Table -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden hidden md:block">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Contact & Role
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Courses
                            </th>
                          
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Joined
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($users as $user)
                            <tr class="hover:bg-gray-50 transition">
                                <!-- User Info -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            @if($user->profile_path)
                                                <img class="h-10 w-10 rounded-full object-cover border border-indigo-200"
                                                    src="{{ asset('storage/' . $user->profile_path) }}"
                                                    alt="{{ $user->name }}">
                                            @else
                                                <img class="h-10 w-10 rounded-full object-cover border border-indigo-200"
                                                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                                    alt="{{ $user->name }}">
                                            @endif
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                                @if($user->hasRole('admin'))
                                                    <i class="fas fa-crown text-yellow-500 ml-1" title="Admin"></i>
                                                @endif
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                ID: {{ $user->id }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Contact & Role -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $user->email }}</div>
                                    <div class="text-xs text-gray-500 capitalize">
                                        @foreach($user->getRoleNames() as $role)
                                            <span class="inline-block px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                                                {{ $role }}
                                            </span>
                                        @endforeach
                                    </div>
                                </td>

                                <!-- Courses -->
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <span class="font-semibold">{{ $user->courses_count ?? 0 }}</span> courses
                                    </div>
                                    @if($user->courses_count > 0)
                                        <div class="text-xs text-gray-500">
                                            {{ $user->courses_created_count ?? 0 }} created
                                        </div>
                                    @endif
                                </td>

                               

                                <!-- Joined Date -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $user->created_at->format('M j, Y') }}
                                    <div class="text-xs text-gray-400">
                                        {{ $user->created_at->diffForHumans() }}
                                    </div>
                                </td>

                                <!-- Actions -->
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-3">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="text-indigo-600 hover:text-indigo-900 transition"
                                           title="View User">
                                            <i class="fas fa-eye"></i>
                                        </a>

                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="text-blue-600 hover:text-blue-900 transition"
                                           title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <a href="mailto:{{ $user->email }}" 
                                           class="text-green-600 hover:text-green-900 transition"
                                           title="Send Email">
                                            <i class="fas fa-envelope"></i>
                                        </a>

                                        <form action="{{ route('admin.users.destroy', $user) }}" 
                                            method="POST" 
                                            onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="text-red-600 hover:text-red-900 transition" 
                                                    title="Delete User">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Mobile Cards -->
        <div class="space-y-4 md:hidden">
            @foreach($users as $user)
                <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-3">
                            @if($user->profile_path)
                                <img class="h-12 w-12 rounded-full object-cover border border-indigo-200"
                                    src="{{ asset('storage/' . $user->profile_path) }}"
                                    alt="{{ $user->name }}">
                            @else
                                <img class="h-12 w-12 rounded-full object-cover border border-indigo-200"
                                    src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random"
                                    alt="{{ $user->name }}">
                            @endif
                            <div>
                                <div class="flex items-center">
                                    <h3 class="text-base font-semibold text-gray-900">{{ $user->name }}</h3>
                                    @if($user->hasRole('admin'))
                                        <i class="fas fa-crown text-yellow-500 ml-1" title="Admin"></i>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="inline-block px-2 py-1 bg-indigo-100 text-indigo-800 rounded-full text-xs">
                                            {{ $role }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-3 grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-gray-500">Courses:</span>
                            <span class="font-medium text-gray-900 ml-1">{{ $user->courses_count ?? 0 }}</span>
                        </div>
                        
                    </div>
                    
                    <div class="mt-3 flex justify-between items-center">
                        <span class="text-xs text-gray-500">
                            Joined {{ $user->created_at->format('M j, Y') }}
                        </span>
                        <div class="flex space-x-2">
                            <a href="{{ route('admin.users.show', $user) }}" 
                               class="text-indigo-600 hover:text-indigo-900 p-1">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('admin.users.edit', $user) }}" 
                               class="text-blue-600 hover:text-blue-900 p-1">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="mailto:{{ $user->email }}" 
                               class="text-green-600 hover:text-green-900 p-1">
                                <i class="fas fa-envelope"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" 
                                method="POST" 
                                onsubmit="return confirm('Delete this user?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900 p-1">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

       

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $users->links() }}
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm p-8 text-center">
            <div class="max-w-md mx-auto">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-users text-gray-400 text-xl"></i>
                </div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">No Users Found</h3>
                <p class="text-gray-500 mb-6">
                    There are no users matching your current filters.
                </p>
                <a href="{{ route('admin.users.create') }}" 
                   class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700">
                    <i class="fas fa-plus mr-2"></i>
                    Add New User
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush
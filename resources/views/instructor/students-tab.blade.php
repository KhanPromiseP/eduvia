@if($students->count() > 0)
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
        <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Courses Enrolled</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($students as $student)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full object-cover" 
                                 src="{{ $student->profile_photo_path ? asset('storage/' . $student->profile_photo_path) : asset('images/default-avatar.png') }}" 
                                 alt="{{ $student->name }}">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="bg-indigo-100 text-indigo-800 px-2 py-1 rounded-full text-xs font-medium">
                            {{ $student->courses_count }} courses
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $student->created_at->format('M j, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <a href="{{ route('instructor.students.detail', $student->id) }}" 
                           class="text-indigo-600 hover:text-indigo-900 mr-3">View Details</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    @if($students->hasPages())
    <div class="mt-6">
        {{ $students->links() }}
    </div>
    @endif
@else
    <div class="text-center text-gray-500 py-12">
        <i class="fas fa-users text-6xl mb-4 text-gray-300"></i>
        <h4 class="text-lg font-semibold text-gray-600 mb-2">No Students Yet</h4>
        <p class="text-gray-500">You don't have any students enrolled in your courses yet.</p>
        <a href="{{ route('instructor.courses.create') }}" 
           class="mt-4 inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
            Create Your First Course
        </a>
    </div>
@endif
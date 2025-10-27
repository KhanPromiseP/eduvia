@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-800">My Followers</h1>
        <div class="flex items-center space-x-4">
            <div class="bg-white rounded-lg shadow-sm px-4 py-2">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">Total Followers:</span>
                    <span class="font-semibold text-indigo-600">{{ $totalFollowers }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Followers Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Followers</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalFollowers }}</p>
                </div>
                <i class="fas fa-users text-blue-500 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Active Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeStudents }}</p>
                </div>
                <i class="fas fa-user-check text-green-500 text-xl"></i>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">New This Month</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $newThisMonth }}</p>
                </div>
                <i class="fas fa-chart-line text-purple-500 text-xl"></i>
            </div>
        </div>
    </div>

    <!-- Followers List -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-semibold text-gray-800">All Followers</h3>
                <div class="flex space-x-2">
                    <input type="text" id="searchFollowers" placeholder="Search followers..." 
                           class="text-sm border border-gray-300 rounded-lg px-3 py-1 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    <select id="filterStatus" class="text-sm border border-gray-300 rounded-lg px-3 py-1">
                        <option value="all">All Followers</option>
                        <option value="student">Students</option>
                        <option value="non-student">Non-Students</option>
                        <option value="recent">Recent</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="divide-y divide-gray-200">
            @if($followers->count() > 0)
                @foreach($followers as $follower)
                    @if($follower) {{-- Check if follower exists --}}
                    <div class="p-6 follower-item" data-is-student="{{ $follower->is_student ? 'true' : 'false' }}" data-joined="{{ $follower->pivot->created_at ?? $follower->created_at }}">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center space-x-4">
                                {{-- Safe profile image with null check --}}
                                <img src="{{ $follower->profile_path ? asset('storage/' . $follower->profile_path) : asset('images/default-avatar.png') }}" 
                                     alt="{{ $follower->name ?? 'Unknown User' }}" 
                                     class="w-12 h-12 rounded-full object-cover border border-gray-200">
                                <div>
                                    {{-- Safe user data with null checks --}}
                                    <h4 class="font-semibold text-gray-900">{{ $follower->name ?? 'Unknown User' }}</h4>
                                    <p class="text-sm text-gray-600">{{ $follower->email ?? 'No email' }}</p>
                                    <div class="flex items-center space-x-3 mt-1">
                                        <span class="text-xs text-gray-500">
                                            Joined {{ ($follower->pivot->created_at ?? $follower->created_at)->diffForHumans() }}
                                        </span>
                                        @if($follower->is_student ?? false)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <i class="fas fa-graduation-cap mr-1"></i> Student
                                        </span>
                                        @endif
                                        @if(($follower->enrolled_courses_count ?? 0) > 0)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            <i class="fas fa-book mr-1"></i> {{ $follower->enrolled_courses_count }} course(s)
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="text-right">
                                    @if($follower->is_student ?? false)
                                    <p class="text-sm font-medium text-gray-900">{{ $follower->enrolled_courses_count ?? 0 }} courses</p>
                                    <p class="text-xs text-gray-500">Enrolled</p>
                                    @endif
                                </div>
                                <div class="flex space-x-2">
                                    <button class="message-btn text-indigo-600 hover:text-indigo-800 p-2 rounded-lg hover:bg-indigo-50"
                                            data-user-id="{{ $follower->id }}"
                                            data-user-name="{{ $follower->name ?? 'User' }}"
                                            title="Send Message">
                                        <i class="fas fa-envelope"></i>
                                    </button>
                                    @if($follower->id)
                                    <button class="view-profile-btn text-gray-600 hover:text-gray-800 p-2 rounded-lg hover:bg-gray-100"
                                            onclick="window.location='{{ route('instructor.students.detail', $follower->id) }}'"
                                            title="View Profile">
                                        <i class="fas fa-external-link-alt"></i>
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                @endforeach
            @else
                <div class="text-center py-12">
                    <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-600 mb-2">No Followers Yet</h3>
                    <p class="text-gray-500">You haven't gained any followers yet. Share your courses to attract followers!</p>
                    <div class="mt-4">
                        <a href="{{ route('instructor.courses.index') }}" 
                           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium">
                            <i class="fas fa-share-alt mr-2"></i> Share Your Courses
                        </a>
                    </div>
                </div>
            @endif
        </div>

        @if($followers->count() > 0)
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $followers->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Send Message</h3>
        </div>
        <div class="px-6 py-4">
            <form id="messageForm">
                @csrf
                <input type="hidden" name="recipient_id" id="recipientId">
                <div class="mb-4">
                    <p class="text-sm text-gray-600">To: <span id="recipientName" class="font-semibold"></span></p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                    <textarea name="message" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                              placeholder="Write your message..."
                              required></textarea>
                </div>
            </form>
        </div>
        <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
            <button type="button" 
                    class="cancel-message-btn px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 text-sm font-medium">
                Cancel
            </button>
            <button type="button" 
                    class="send-message-btn px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 text-sm font-medium">
                Send Message
            </button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
.follower-item {
    transition: all 0.2s ease;
}

.follower-item:hover {
    background-color: #f9fafb;
    transform: translateX(4px);
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Filter functionality
    const searchInput = document.getElementById('searchFollowers');
    const filterStatus = document.getElementById('filterStatus');
    const followerItems = document.querySelectorAll('.follower-item');

    function filterFollowers() {
        const searchValue = searchInput.value.toLowerCase();
        const statusValue = filterStatus.value;

        followerItems.forEach(item => {
            const userName = item.querySelector('h4').textContent.toLowerCase();
            const userEmailElement = item.querySelector('p.text-sm');
            const userEmail = userEmailElement ? userEmailElement.textContent.toLowerCase() : '';
            const isStudent = item.getAttribute('data-is-student') === 'true';
            const joinedDateStr = item.getAttribute('data-joined');
            const joinedDate = new Date(joinedDateStr).getTime() / 1000;
            const isRecent = (Date.now() / 1000 - joinedDate) < (30 * 24 * 60 * 60); // Within 30 days

            const searchMatch = userName.includes(searchValue) || userEmail.includes(searchValue);
            let statusMatch = true;

            switch (statusValue) {
                case 'student':
                    statusMatch = isStudent;
                    break;
                case 'non-student':
                    statusMatch = !isStudent;
                    break;
                case 'recent':
                    statusMatch = isRecent;
                    break;
            }

            if (searchMatch && statusMatch) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    searchInput.addEventListener('input', filterFollowers);
    filterStatus.addEventListener('change', filterFollowers);

    // Message functionality
    const messageModal = document.getElementById('messageModal');
    let currentRecipient = null;

    document.querySelectorAll('.message-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            currentRecipient = {
                id: this.getAttribute('data-user-id'),
                name: this.getAttribute('data-user-name')
            };
            
            document.getElementById('recipientId').value = currentRecipient.id;
            document.getElementById('recipientName').textContent = currentRecipient.name;
            messageModal.classList.remove('hidden');
        });
    });

    document.querySelector('.cancel-message-btn').addEventListener('click', function() {
        messageModal.classList.add('hidden');
        document.getElementById('messageForm').reset();
    });

    document.querySelector('.send-message-btn').addEventListener('click', function() {
        const form = document.getElementById('messageForm');
        const formData = new FormData(form);

        fetch('{{ route("instructor.followers.message") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageModal.classList.add('hidden');
                form.reset();
                showNotification('Message sent successfully', 'success');
            } else {
                showNotification('Error sending message: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error sending message', 'error');
        });
    });

    function showNotification(message, type) {
        // Simple notification - you can replace with a proper notification system
        const alertClass = type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg ${alertClass} font-medium z-50`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});
</script>
@endpush
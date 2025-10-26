@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Cover Photo & Profile Header -->
        <div class="bg-white rounded-xl shadow-xl overflow-hidden mb-8">
            <!-- Cover Photo Section -->
            <div class="relative h-64 bg-gradient-to-r from-indigo-500 to-purple-600">
                @if($instructor->cover_photo_path)
                    <img src="{{ asset('storage/' . $instructor->cover_photo_path) }}" 
                         alt="Cover Photo" class="w-full h-full object-cover">
                @endif
                <div class="absolute inset-0 bg-black bg-opacity-20"></div>
                
                <!-- Profile Action Buttons -->
                <div class="absolute top-4 right-4 flex space-x-2">
                    @auth
                        @if(auth()->id() !== $instructor->user_id)
                            <button class="follow-btn bg-white text-indigo-600 px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-300 transition-all duration-200 shadow-lg flex items-center space-x-2"
                                    data-instructor-id="{{ $instructor->id }}"
                                    data-is-following="{{ auth()->user()->isFollowingInstructor($instructor->id) ? 'true' : 'false' }}">
                                <i class="fas fa-user-plus"></i>
                                <span>{{ auth()->user()->isFollowingInstructor($instructor->id) ? 'Following' : 'Follow' }}</span>
                            </button>
                           
                        @else
                            <a href="{{ route('instructor.analytics') }}" 
                               class="bg-white text-gray-700 px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-50 transition-all duration-200 shadow-lg flex items-center space-x-2">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="bg-white text-indigo-600 px-4 py-2 rounded-full font-semibold text-sm hover:bg-gray-50 transition-all duration-200 shadow-lg">
                            Login to Follow
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Profile Header Section -->
            <div class="relative px-8 pb-6">
                <!-- Profile Avatar & Basic Info -->
                <div class="flex items-end justify-between -mt-20 mb-6">
                    <div class="flex items-end space-x-6">
                        <div class="relative">
                            @if($instructor->user->profile_path)
                                <img src="{{ asset('storage/' . $instructor->user->profile_path) }}" 
                                     alt="{{ $instructor->user->name }}"
                                     class="w-32 h-32 rounded-full border-4 border-white shadow-2xl object-cover">
                            @else
                                <div class="w-32 h-32 bg-gradient-to-r from-indigo-400 to-purple-500 rounded-full border-4 border-white shadow-2xl flex items-center justify-center">
                                    <span class="text-white font-bold text-3xl">
                                        {{ strtoupper(substr($instructor->user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                            
                            <!-- Verification Badge -->
                            @if($instructor->is_verified)
                                <div class="absolute bottom-2 right-2 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center border-2 border-white">
                                    <i class="fas fa-check text-white text-xs"></i>
                                </div>
                            @endif
                            
                            <!-- Suspension Badge -->
                            @if($instructor->isSuspended())
                                <div class="absolute -top-2 -right-2 bg-red-500 text-white px-2 py-1 rounded-full text-xs font-bold">
                                    Suspended
                                </div>
                            @endif
                        </div>
                        
                        <!-- Basic Info -->
                        <div class="pb-4">
                            <div class="flex items-center space-x-3 mb-2">
                               <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight font-serif">
                            {{ $instructor->user->name }}
                        </h1>

                                @if($instructor->is_verified)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-sm font-medium flex items-center space-x-1">
                                        <i class="fas fa-check-circle"></i>
                                        <span>Verified Instructor</span>
                                    </span>
                                @endif
                            </div>
                            
                            <p class="text-xl text-gray-800 mb-3">{{ $instructor->headline ?? 'Professional Instructor' }}</p>
                            
                            <!-- Rating and Stats -->
                            <div class="flex items-center space-x-6">
                                <div class="flex items-center space-x-2">
                                    <div class="flex items-center">
                                        @php
                                            $rating = $instructor->rating ?? 4.5;
                                            $totalReviews = $instructor->total_reviews ?? 0;
                                        @endphp
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($rating))
                                                <i class="fas fa-star text-yellow-400 text-lg"></i>
                                            @elseif($i - 0.5 <= $rating)
                                                <i class="fas fa-star-half-alt text-yellow-400 text-lg"></i>
                                            @else
                                                <i class="far fa-star text-yellow-400 text-lg"></i>
                                            @endif
                                        @endfor
                                    </div>
                                    <span class="text-lg font-semibold text-gray-700">{{ number_format($rating, 1) }}</span>
                                    <span class="text-gray-400">•</span>
                                    <span class="text-gray-600">{{ $totalReviews }} reviews</span>
                                </div>
                                
                                <!-- Followers Count -->
                                <div class="flex items-center space-x-1 text-gray-600">
                                    <i class="fas fa-users"></i>
                                    <span class="font-semibold">{{ $instructor->followers_count ?? '0' }}</span>
                                    <span>followers</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Profile Tabs -->
                <div class="border-b border-gray-200">
                    <nav class="flex space-x-8" id="profileTabs">
                        <button type="button" 
                                data-tab="overview"
                                class="tab-btn py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 {{ request()->is('instructor/'.$instructor->user_id) ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            Overview
                        </button>

                        <button type="button"
                                data-tab="courses" 
                                class="tab-btn py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Courses ({{ $instructor->courses_count ?? 0 }})
                        </button>

                        <button type="button"
                                data-tab="reviews"
                                class="tab-btn py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Reviews ({{ $instructor->total_reviews ?? 0 }})
                        </button>

                        @auth
                            @if(auth()->id() === $instructor->user_id)
                            <button type="button"
                                    data-tab="students" 
                                    class="tab-btn py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Students ({{ $instructor->total_students ?? 0 }})
                            </button>
                            @endif
                        @endauth

                        @auth
                            @if(auth()->id() !== $instructor->user_id)
                            <button type="button"
                                    data-tab="contact"
                                    class="tab-btn py-4 px-1 border-b-2 font-medium text-sm transition-all duration-200 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                                Contact
                            </button>
                            @endif
                        @endauth
                    </nav>
                </div>
            </div>
        </div>

        <!-- Tab Content -->
        <div id="tab-content">
            <!-- Overview Tab -->
            <div id="overview-tab" class="tab-content active">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left Column -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- About Section -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">About Me</h3>
                            <p class="text-gray-700 leading-relaxed whitespace-pre-line">
                                {{ $instructor->bio ?? 'No bio available.' }}
                            </p>
                            
                            <!-- Expertise & Skills -->
                            <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                                @if($instructor->headline)
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Headline</h4>
                                    <p class="text-gray-700">{{ $instructor->headline }}</p>
                                </div>
                                @endif
                                
                                @if($instructor->skills && count($instructor->skills) > 0)
                                <div>
                                    <h4 class="font-semibold text-gray-900 mb-2">Skills & Expertise</h4>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($instructor->skills as $skill)
                                            <span class="bg-indigo-100 text-indigo-800 px-3 py-1 rounded-full text-sm font-medium">
                                                {{ $skill }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Recent Activity -->
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-2xl font-bold text-gray-900 mb-6 tracking-tight font-serif">
                                Recent Activity
                            </h3>

                            @if($recentCourses->count() > 0)
                                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                                    @foreach($recentCourses as $course)
                                        <div class="group bg-gray-50 hover:bg-gray-100 transition-all duration-300 rounded-xl overflow-hidden border border-gray-100 hover:shadow-md">
                                            <div class="relative">
                                                @if($course->image)
                                                    <img src="{{ asset('storage/' . $course->image) }}" 
                                                        alt="{{ $course->title }}" 
                                                        class="w-full h-40 object-cover transition-transform duration-300 group-hover:scale-105">
                                                @else
                                                    <div class="w-full h-40 bg-gradient-to-r from-indigo-500 to-purple-500 flex items-center justify-center">
                                                        <i class="fas fa-book text-white text-2xl"></i>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="p-4">
                                                <h5 class="font-semibold text-gray-900 line-clamp-2 mb-1">
                                                    {{ $course->title }}
                                                </h5>
                                                <p class="text-xs text-gray-500 mb-3">
                                                    Published {{ $course->created_at->diffForHumans() }}
                                                </p>

                                                <div class="flex items-center justify-between text-sm text-gray-600 mb-3">
                                                    <span class="flex items-center space-x-1">
                                                        <i class="fas fa-users text-gray-400"></i>
                                                        <span>{{ $course->user_courses_count }} students</span>
                                                    </span>
                                                    @if($course->reviews_avg_rating)
                                                        <span class="flex items-center space-x-1">
                                                            <i class="fas fa-star text-yellow-400"></i>
                                                            <span>{{ number_format($course->reviews_avg_rating, 1) }}</span>
                                                        </span>
                                                    @endif
                                                    <div class="text-right">
                                                        @php
                                                            $rating = $course->average_rating ?? 0;
                                                            $ratingCount = $course->total_reviews ?? 0;
                                                        @endphp
                                                        <div class="flex items-center gap-1 mb-1">
                                                            <div class="flex text-yellow-400 text-sm">
                                                                @for($i = 1; $i <= 5; $i++)
                                                                    @if($i <= floor($rating))
                                                                        <i class="fas fa-star"></i>
                                                                    @elseif($i - 0.5 <= $rating)
                                                                        <i class="fas fa-star-half-alt"></i>
                                                                    @else
                                                                        <i class="far fa-star"></i>
                                                                    @endif
                                                                @endfor
                                                            </div>
                                                            <span class="text-sm font-bold text-gray-900">{{ number_format($rating, 1) }}</span>
                                                        </div>
                                                        <span class="text-xs text-gray-500">{{ $ratingCount }} reviews</span>
                                                    </div>
                                                </div>

                                                <a href="{{ route('courses.show', $course->id) }}" 
                                                class="block text-center bg-indigo-600 text-white py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                                                    View Course
                                                </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center text-gray-500 py-10">
                                    <i class="fas fa-book-open text-5xl mb-3"></i>
                                    <p class="text-sm">No recent course activity yet.</p>
                                </div>
                            @endif
                            
                        </div>
                         <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-6">
                        <div class="text-center text-gray-500 py-10">
                            <i class="fas fa-book-open text-5xl mb-3 text-gray-300"></i>
                            <p class="text-sm">Other recent activities here...</p>
                        </div>
                    </div>
                    </div>

                   

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Stats Card -->
                        <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl p-6 text-white shadow-lg">
                            <h3 class="text-lg font-bold mb-4">Teaching Statistics</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center space-x-2">
                                        <i class="fas fa-users"></i>
                                        <span>Total Students</span>
                                    </span>
                                    <span class="font-bold text-lg">{{ $instructor->total_students ?? '0' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center space-x-2">
                                        <i class="fas fa-book"></i>
                                        <span>Courses Created</span>
                                    </span>
                                    <span class="font-bold text-lg">{{ $instructor->courses_count ?? '0' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center space-x-2">
                                        <i class="fas fa-star"></i>
                                        <span>Total Reviews</span>
                                    </span>
                                    <span class="font-bold text-lg">{{ $instructor->total_reviews ?? '0' }}</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="flex items-center space-x-2">
                                        <i class="fas fa-clock"></i>
                                        <span>Avg. Rating</span>
                                    </span>
                                    <span class="font-bold text-lg">{{ number_format($instructor->rating ?? 0, 1) }}/5.0</span>
                                </div>
                            </div>
                        </div>

                        <!-- Languages -->
                        @if($instructor->languages && count($instructor->languages) > 0)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                            <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center space-x-2">
                                <i class="fas fa-language text-indigo-600"></i>
                                <span>Languages</span>
                            </h3>
                            <div class="space-y-3">
                                @foreach($instructor->languages as $language)
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                        <span class="text-gray-700 font-medium">{{ $language }}</span>
                                        <span class="text-sm text-gray-500 bg-gray-100 px-2 py-1 rounded">Fluent</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Contact Info -->
                        @auth
                            @if(auth()->id() !== $instructor->user_id)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <h3 class="text-lg font-bold text-gray-900 mb-4">Get in Touch</h3>
                                <p class="text-gray-600 text-sm mb-4">
                                    Have questions? Send a message directly to Instructor <span class="text-indigo-800 font-bold">{{ $instructor->user->name }}</span>.
                                </p>
                                 <form id="contact-form">
                                @csrf
                                <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                        <input type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                        <textarea name="message" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                                        Send Message
                                    </button>
                                </div>
                            </form>
                            </div>
                            @endif
                        @endauth
                    </div>
                </div>
            </div>

            <!-- Courses Tab -->
            <div id="courses-tab" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-6">All Courses</h3>
                    @if($courses->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($courses as $course)
                            <div class="bg-white border border-gray-200 rounded-lg overflow-hidden hover:shadow-lg transition-shadow duration-300 course-card">
                                @if($course->image)
                                    <img src="{{ asset('storage/' . $course->image) }}" 
                                         alt="{{ $course->title }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-r from-indigo-400 to-purple-500 flex items-center justify-center">
                                        <i class="fas fa-book-open text-white text-4xl"></i>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <div class="flex items-start justify-between mb-2">
                                        <h4 class="font-bold text-gray-900 line-clamp-2 flex-1">{{ $course->title }}</h4>
                                    </div>
                                    
                                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ Str::limit($course->description, 100) }}</p>
                                    
                                    @if($course->category)
                                        <span class="inline-block bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-medium mb-3">
                                            {{ $course->category->name }}
                                        </span>
                                    @endif
                                    
                                    <div class="flex justify-between items-center mb-3">
                                        <div class="flex items-center space-x-1">
                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                            <span class="text-sm font-semibold">{{ number_format($course->reviews_avg_rating ?? 0, 1) }}</span>
                                            <span class="text-gray-400 text-sm">({{ $course->reviews_count ?? 0 }})</span>
                                        </div>
                                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                                            {{ $course->enrollments_count }} students
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-lg font-bold text-indigo-600">
                                            @if($course->price > 0)
                                                ${{ number_format($course->price, 2) }}
                                            @else
                                                Free
                                            @endif
                                        </span>
                                        <a href="{{ route('courses.show', $course->id) }}" 
                                           class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors duration-200">
                                            View Course
                                        </a>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-500 py-12">
                            <i class="fas fa-book-open text-6xl mb-4 text-gray-300"></i>
                            <h4 class="text-lg font-semibold text-gray-600 mb-2">No Courses Published</h4>
                            <p class="text-gray-500">This instructor hasn't published any courses yet.</p>
                        </div>
                    @endif
                </div>
            </div>

           <!-- Reviews Tab -->
            <div id="reviews-tab" class="tab-content hidden">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-bold text-gray-900">Student Reviews For instructor <span class="text-indigo-800 font-bold">{{ $instructor->user->name }}</span>. </h3>
                        @auth
                            @if(auth()->id() !== $instructor->user_id)
                                @php
                                    $userId = auth()->id();
                                    $instructorId = $instructor->user_id;
                                    
                                    // Get all instructor course IDs
                                    $instructorCourseIds = \App\Models\Course::where('user_id', $instructorId)
                                        ->where('is_published', true)
                                        ->pluck('id');
                                    
                                    // Get all user enrollments
                                    $userEnrollments = \DB::table('user_courses')
                                        ->where('user_id', $userId)
                                        ->pluck('course_id');
                                    
                                    // Find matching courses
                                    $matchingCourses = $userEnrollments->intersect($instructorCourseIds);
                                    
                                    $hasEnrolled = $matchingCourses->count() > 0;
                                    
                                    // Check if user has already reviewed this instructor
                                    $userReview = \App\Models\InstructorReview::where('instructor_id', $instructor->id)
                                        ->where('user_id', $userId)
                                        ->first();
                                @endphp
                                
                                @if($hasEnrolled && !$userReview)
                                <button class="review-btn bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                                    Write a Review
                                </button>
                                @elseif(!$hasEnrolled)
                                <button class="bg-gray-400 text-white px-4 py-2 rounded-lg font-semibold cursor-not-allowed" 
                                        title="You need to enroll in a course first">
                                    Write a Review
                                </button>
                                @endif
                            @endif
                        @endauth
                    </div>

                    @if($reviews->count() > 0)
                        <div class="space-y-6">
                            @foreach($reviews as $review)
                            <div class="border-b border-gray-200 pb-6 last:border-b-0 last:pb-0 {{ $review->user_id === auth()->id() ? 'bg-blue-50 -mx-6 px-6 py-4 border-l-4 border-l-blue-500' : '' }}">
                                <div class="flex items-start space-x-4">
                                    <img src="{{ $review->user->profile_path ? asset('storage/' . $review->user->profile_path) : asset('images/default-avatar.png') }}" 
                                        alt="{{ $review->user->name }}" 
                                        class="w-12 h-12 rounded-full object-cover">
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <div class="flex items-center space-x-2">
                                                    <h4 class="font-semibold text-gray-900">{{ $review->user->name }}</h4>
                                                    @if($review->user_id === auth()->id())
                                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded-full text-xs font-medium">
                                                        Your Review
                                                    </span>
                                                    @endif
                                                </div>
                                                @if($review->course)
                                                    <p class="text-sm text-gray-600">Reviewed: {{ $review->course->title }}</p>
                                                @endif
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <div class="flex items-center space-x-1">
                                                    <span class="text-sm font-bold text-gray-900 px-1">{{ number_format($review->rating, 1) }}</span>
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <i class="fas fa-star text-yellow-400 text-sm"></i>
                                                        @else
                                                            <i class="far fa-star text-yellow-400 text-sm"></i>
                                                        @endif
                                                    @endfor
                                                    
                                                </div>
                                                
                                                @if($review->user_id === auth()->id())
                                                <div class="flex space-x-2 p-8">
                                                    <button class="edit-review-btn text-blue-600 hover:text-blue-800 transition-colors duration-200" 
                                                            data-review-id="{{ $review->id }}"
                                                            data-rating="{{ $review->rating }}"
                                                            data-review-text="{{ $review->review }}">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="delete-review-btn text-red-600 hover:text-red-800 transition-colors duration-200" 
                                                            data-review-id="{{ $review->id }}">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                        <p class="text-gray-700 leading-relaxed">{{ $review->review }}</p>
                                        <p class="text-sm text-gray-500 mt-2">{{ $review->created_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Pagination -->
                        @if($reviews->hasPages())
                        <div class="mt-6">
                            {{ $reviews->links() }}
                        </div>
                        @endif
                    @else
                        <div class="text-center text-gray-500 py-12">
                            <i class="fas fa-comments text-6xl mb-4 text-gray-300"></i>
                            <h4 class="text-lg font-semibold text-gray-600 mb-2">No Reviews Yet</h4>
                            <p class="text-gray-500">This instructor doesn't have any reviews yet.</p>
                            @auth
                                @if(auth()->id() !== $instructor->user_id)
                                    @php
                                        $userId = auth()->id();
                                        $instructorId = $instructor->user_id;
                                        $instructorCourseIds = \App\Models\Course::where('user_id', $instructorId)
                                            ->where('is_published', true)
                                            ->pluck('id');
                                        $userEnrollments = \DB::table('user_courses')
                                            ->where('user_id', $userId)
                                            ->pluck('course_id');
                                        $matchingCourses = $userEnrollments->intersect($instructorCourseIds);
                                        $hasEnrolled = $matchingCourses->count() > 0;
                                        
                                        $userReview = \App\Models\InstructorReview::where('instructor_id', $instructor->id)
                                            ->where('user_id', $userId)
                                            ->first();
                                    @endphp
                                    
                                    @if($hasEnrolled && !$userReview)
                                    <button class="review-btn bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200 mt-4">
                                        Be the first to review
                                    </button>
                                    @elseif(!$hasEnrolled)
                                    <div class="mt-4">
                                        <p class="text-sm text-gray-600 mb-2">Enroll in a course to leave a review</p>
                                        <a href="{{ route('courses.index') }}?instructor={{ $instructor->id }}" 
                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                                            Browse Courses
                                        </a>
                                    </div>
                                    @endif
                                @endif
                            @else
                                <div class="mt-4">
                                    <p class="text-sm text-gray-600 mb-2">Sign in to leave a review</p>
                                    <a href="{{ route('login') }}" 
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                                        Login
                                    </a>
                                </div>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>

            <!-- Edit Review Modal -->
            <div id="editReviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Edit Your Review</h3>
                            <button onclick="closeEditReviewModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <form id="edit-review-form">
                            @csrf
                            <input type="hidden" name="review_id" id="edit-review-id">
                            <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                    <div class="flex space-x-1" id="edit-rating-stars">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" class="text-2xl text-gray-300 hover:text-yellow-400 edit-rating-star" data-rating="{{ $i }}">
                                                <i class="far fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="edit-selected-rating" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                                    <textarea name="review" id="edit-review-text" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Share your experience with this instructor..." required></textarea>
                                </div>
                                <div class="flex space-x-3">
                                    <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                                        Update Review
                                    </button>
                                    <button type="button" onclick="closeEditReviewModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-all duration-200">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Delete Review Confirmation Modal -->
            <div id="deleteReviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-bold text-gray-900">Delete Review</h3>
                            <button onclick="closeDeleteReviewModal()" class="text-gray-400 hover:text-gray-600">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                        <div class="space-y-4">
                            <p class="text-gray-700">Are you sure you want to delete your review? This action cannot be undone.</p>
                            <div class="flex space-x-3">
                                <button type="button" onclick="confirmDeleteReview()" class="flex-1 bg-red-600 text-white py-3 rounded-lg font-semibold hover:bg-red-700 transition-all duration-200">
                                    Delete Review
                                </button>
                                <button type="button" onclick="closeDeleteReviewModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-all duration-200">
                                    Cancel
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Students Tab -->
            @auth
                @if(auth()->id() === $instructor->user_id)
                <div id="students-tab" class="tab-content hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Your Students</h3>
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
                                                         src="{{ $student->profile_path ? asset('storage/' . $student->profile_path) : asset('images/default-avatar.png') }}" 
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
                    </div>
                </div>
                @endif
            @endauth

            <!-- Contact Tab -->
            @auth
                @if(auth()->id() !== $instructor->user_id)
                <div id="contact-tab" class="tab-content hidden">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-6">Contact {{ $instructor->user->name }}</h3>
                        <div class="max-w-2xl">
                            <form id="contact-form">
                                @csrf
                                <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Subject</label>
                                        <input type="text" name="subject" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Message</label>
                                        <textarea name="message" rows="6" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Write your message here..." required></textarea>
                                    </div>
                                    <button type="submit" class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
            @endauth
        </div>
    </div>
</div>

<!-- Message Modal -->
<div id="messageModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Message {{ $instructor->user->name }}</h3>
                <button onclick="closeMessageModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="quick-message-form">
                @csrf
                <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                <div class="space-y-4">
                    <div>
                        <textarea name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Write your message here..." required></textarea>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                            Send Message
                        </button>
                        <button type="button" onclick="closeMessageModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-all duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Review Modal -->
<div id="reviewModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-4">
        <div class="p-6">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold text-gray-900">Write a Review</h3>
                <button onclick="closeReviewModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <form id="review-form">
                @csrf
                <input type="hidden" name="instructor_id" value="{{ $instructor->id }}">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                        <div class="flex space-x-1" id="rating-stars">
                            @for($i = 1; $i <= 5; $i++)
                                <button type="button" class="text-2xl text-gray-300 hover:text-yellow-400 rating-star" data-rating="{{ $i }}">
                                    <i class="far fa-star"></i>
                                </button>
                            @endfor
                        </div>
                        <input type="hidden" name="rating" id="selected-rating" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                        <textarea name="review" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500" placeholder="Share your experience with this instructor..." required></textarea>
                    </div>
                    <div class="flex space-x-3">
                        <button type="submit" class="flex-1 bg-indigo-600 text-white py-3 rounded-lg font-semibold hover:bg-indigo-700 transition-all duration-200">
                            Submit Review
                        </button>
                        <button type="button" onclick="closeReviewModal()" class="flex-1 bg-gray-300 text-gray-700 py-3 rounded-lg font-semibold hover:bg-gray-400 transition-all duration-200">
                            Cancel
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .tab-content {
        transition: opacity 0.3s ease-in-out;
    }
    .tab-content.hidden {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .course-card {
        transition: all 0.3s ease;
    }
    .course-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .custom-notification {
        transform: translateX(100%);
        opacity: 0;
    }
    .custom-notification.translate-x-0 {
        transform: translateX(0);
        opacity: 1;
    }
</style>
@endpush


<script>
// Global modal functions - must be available immediately
function openMessageModal() {
    console.log('Opening message modal');
    document.getElementById('messageModal').classList.remove('hidden');
}

function closeMessageModal() {
    document.getElementById('messageModal').classList.add('hidden');
}

function openReviewModal() {
    console.log('Opening review modal');
    document.getElementById('reviewModal').classList.remove('hidden');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.add('hidden');
    updateRatingStars(0);
}

// Edit and Delete Review Functionality
let currentReviewId = null;

function openEditReviewModal(reviewId, rating, reviewText) {
    console.log('Opening edit modal for review:', reviewId);
    currentReviewId = reviewId;
    document.getElementById('edit-review-id').value = reviewId;
    document.getElementById('edit-review-text').value = reviewText;
    updateEditRatingStars(rating);
    document.getElementById('editReviewModal').classList.remove('hidden');
}

function closeEditReviewModal() {
    document.getElementById('editReviewModal').classList.add('hidden');
    currentReviewId = null;
}

function openDeleteReviewModal(reviewId) {
    console.log('Opening delete modal for review:', reviewId);
    currentReviewId = reviewId;
    document.getElementById('deleteReviewModal').classList.remove('hidden');
}

function closeDeleteReviewModal() {
    document.getElementById('deleteReviewModal').classList.add('hidden');
    currentReviewId = null;
}

function updateEditRatingStars(rating) {
    const stars = document.querySelectorAll('.edit-rating-star');
    stars.forEach((star, index) => {
        const starIcon = star.querySelector('i');
        if (index < rating) {
            starIcon.classList.remove('far', 'fa-star');
            starIcon.classList.add('fas', 'fa-star', 'text-yellow-400');
        } else {
            starIcon.classList.remove('fas', 'fa-star', 'text-yellow-400');
            starIcon.classList.add('far', 'fa-star');
        }
    });
    document.getElementById('edit-selected-rating').value = rating;
}

function confirmDeleteReview() {
    if (!currentReviewId) return;
    
    console.log('Deleting review:', currentReviewId);
    
    fetch(`/instructor/review/${currentReviewId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeDeleteReviewModal();
            // Refresh the page to update the UI
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to delete review');
        }
    })
    .catch(error => {
        console.error('Error deleting review:', error);
        showNotification('Failed to delete review. Please try again.', 'error');
    });
}

// Initialize all functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded - initializing all functionality');
    initializeTabs();
    initializeEventListeners();
    initializeModals();
    initializeForms();
    initializeReviewButtons(); // Initialize review buttons on page load
});

function initializeTabs() {
    console.log('Initializing tabs...');
    
    const tabButtons = document.querySelectorAll('.tab-btn');
    console.log('Found tab buttons:', tabButtons.length);
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const tabName = this.getAttribute('data-tab');
            console.log('Switching to tab:', tabName);
            switchTab(tabName);
            
            // Re-initialize review buttons when reviews tab is shown
            if (tabName === 'reviews') {
                setTimeout(initializeReviewButtons, 100);
            }
        });
    });
}

function switchTab(tabName) {
    console.log('Switching to tab:', tabName);
    
    // Update active tab button
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('border-indigo-600', 'text-indigo-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    
    const activeButton = document.querySelector(`[data-tab="${tabName}"]`);
    if (activeButton) {
        activeButton.classList.add('border-indigo-600', 'text-indigo-600');
        activeButton.classList.remove('border-transparent', 'text-gray-500');
    }
    
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
        content.classList.remove('active');
    });
    
    // Show active tab content
    const activeContent = document.getElementById(`${tabName}-tab`);
    if (activeContent) {
        activeContent.classList.remove('hidden');
        activeContent.classList.add('active');
        console.log('Tab content shown:', tabName);
    }
}

function initializeReviewButtons() {
    console.log('Initializing review buttons...');
    
    // Edit review buttons
    const editButtons = document.querySelectorAll('.edit-review-btn');
    console.log('Found edit buttons:', editButtons.length);
    
    editButtons.forEach(button => {
        // Remove existing listeners to prevent duplicates
        button.replaceWith(button.cloneNode(true));
    });
    
    // Re-select and add new listeners
    document.querySelectorAll('.edit-review-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            const rating = parseInt(this.getAttribute('data-rating'));
            const reviewText = this.getAttribute('data-review-text');
            console.log('Edit button clicked:', reviewId, rating, reviewText);
            openEditReviewModal(reviewId, rating, reviewText);
        });
    });
    
    // Delete review buttons
    const deleteButtons = document.querySelectorAll('.delete-review-btn');
    console.log('Found delete buttons:', deleteButtons.length);
    
    deleteButtons.forEach(button => {
        // Remove existing listeners to prevent duplicates
        button.replaceWith(button.cloneNode(true));
    });
    
    // Re-select and add new listeners
    document.querySelectorAll('.delete-review-btn').forEach(button => {
        button.addEventListener('click', function() {
            const reviewId = this.getAttribute('data-review-id');
            console.log('Delete button clicked:', reviewId);
            openDeleteReviewModal(reviewId);
        });
    });
    
    // Review form buttons
    document.querySelectorAll('.review-btn').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Review button clicked');
            openReviewModal();
        });
    });
}

function initializeEventListeners() {
    console.log('Initializing event listeners...');
    
    // Follow functionality
    document.querySelectorAll('.follow-btn').forEach(button => {
        button.addEventListener('click', function() {
            const instructorId = this.getAttribute('data-instructor-id');
            const isFollowing = this.getAttribute('data-is-following') === 'true';
            followInstructor(instructorId, !isFollowing, this);
        });
    });

    // Message buttons
    document.querySelectorAll('.message-btn').forEach(button => {
        button.addEventListener('click', function() {
            openMessageModal();
        });
    });

    // Rating stars
    document.querySelectorAll('.rating-star').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            updateRatingStars(rating);
        });
    });

    // Edit rating stars
    document.querySelectorAll('.edit-rating-star').forEach(star => {
        star.addEventListener('click', function() {
            const rating = parseInt(this.getAttribute('data-rating'));
            updateEditRatingStars(rating);
        });
    });

    // Course card hover effects
    initializeCourseCards();
}

function initializeModals() {
    console.log('Initializing modals...');
    
    // Close modals when clicking outside
    document.addEventListener('click', function(event) {
        const messageModal = document.getElementById('messageModal');
        const reviewModal = document.getElementById('reviewModal');
        const editReviewModal = document.getElementById('editReviewModal');
        const deleteReviewModal = document.getElementById('deleteReviewModal');
        
        if (event.target === messageModal) {
            closeMessageModal();
        }
        if (event.target === reviewModal) {
            closeReviewModal();
        }
        if (event.target === editReviewModal) {
            closeEditReviewModal();
        }
        if (event.target === deleteReviewModal) {
            closeDeleteReviewModal();
        }
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            closeMessageModal();
            closeReviewModal();
            closeEditReviewModal();
            closeDeleteReviewModal();
        }
    });
}

function initializeForms() {
    console.log('Initializing forms...');
    
    // Quick message form
    const quickMessageForm = document.getElementById('quick-message-form');
    if (quickMessageForm) {
        quickMessageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitQuickMessage(this);
        });
    }

    // Review form
    const reviewForm = document.getElementById('review-form');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitReview(this);
        });
    }

    // Edit review form
    const editReviewForm = document.getElementById('edit-review-form');
    if (editReviewForm) {
        editReviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitEditReview(this);
        });
    }

    // Contact form
    const contactForm = document.getElementById('contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitContactForm(this);
        });
    }
}

function submitEditReview(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
    submitBtn.disabled = true;
    
    fetch('/instructor/review/update', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeEditReviewModal();
            // Refresh the page to update the UI
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Failed to update review');
        }
    })
    .catch(error => {
        console.error('Error updating review:', error);
        showNotification('Failed to update review. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// ... rest of your existing functions (initializeCourseCards, submitQuickMessage, submitContactForm, submitReview, updateRatingStars, followInstructor, showNotification)

function initializeCourseCards() {
    document.querySelectorAll('.course-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });
}

function submitQuickMessage(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;
    
    fetch('{{ route("instructor.message") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Message sent successfully!', 'success');
            form.reset();
            closeMessageModal();
        } else {
            throw new Error(data.message || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        showNotification('Failed to send message. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function submitContactForm(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
    submitBtn.disabled = true;
    
    fetch('{{ route("instructor.contact") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Message sent successfully!', 'success');
            form.reset();
        } else {
            throw new Error(data.message || 'Failed to send message');
        }
    })
    .catch(error => {
        console.error('Error sending contact form:', error);
        showNotification('Failed to send message. Please try again.', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function submitReview(form) {
    const formData = new FormData(form);
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
    submitBtn.disabled = true;
    
    fetch('{{ route("instructor.review.submit") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                throw new Error(JSON.stringify(errorData));
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            form.reset();
            closeReviewModal();
            // Refresh the page to show new review
            setTimeout(() => {
                window.location.reload();
            }, 2000);
        } else {
            throw new Error(data.message || 'Failed to submit review');
        }
    })
    .catch(error => {
        console.error('Error submitting review:', error);
        
        try {
            const errorData = JSON.parse(error.message);
            if (errorData.message) {
                showNotification(errorData.message, 'error');
            } else {
                showNotification('Failed to submit review. Please try again.', 'error');
            }
        } catch (e) {
            showNotification('Failed to submit review. Please try again.', 'error');
        }
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

function updateRatingStars(rating) {
    const stars = document.querySelectorAll('.rating-star');
    stars.forEach((star, index) => {
        const starIcon = star.querySelector('i');
        if (index < rating) {
            starIcon.classList.remove('far', 'fa-star');
            starIcon.classList.add('fas', 'fa-star', 'text-yellow-400');
        } else {
            starIcon.classList.remove('fas', 'fa-star', 'text-yellow-400');
            starIcon.classList.add('far', 'fa-star');
        }
    });
    document.getElementById('selected-rating').value = rating;
}

function followInstructor(instructorId, follow, buttonElement) {
    const originalText = buttonElement.innerHTML;
    buttonElement.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Loading...</span>';
    buttonElement.disabled = true;
    
    fetch(`/instructor/${instructorId}/follow`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify({ follow: follow })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            buttonElement.setAttribute('data-is-following', follow);
            buttonElement.innerHTML = follow ? 
                '<i class="fas fa-user-check"></i><span>Following</span>' : 
                '<i class="fas fa-user-plus"></i><span>Follow</span>';
            
            // Update followers count display
            const followersElement = document.querySelector('.followers-count');
            if (followersElement && data.followers_count !== undefined) {
                followersElement.textContent = data.followers_count;
            }
            
            showNotification(
                follow ? 'You are now following this instructor!' : 'You have unfollowed this instructor.', 
                'success'
            );
        } else {
            throw new Error(data.message || 'Operation failed');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        buttonElement.innerHTML = originalText;
        showNotification('An error occurred. Please try again.', 'error');
    })
    .finally(() => {
        buttonElement.disabled = false;
    });
}

function showNotification(message, type) {
    // Remove any existing notifications
    document.querySelectorAll('.custom-notification').forEach(notification => notification.remove());
    
    const notification = document.createElement('div');
    notification.className = `custom-notification fixed top-4 right-4 p-4 rounded-lg shadow-lg text-white transform transition-all duration-300 z-50 ${
        type === 'success' ? 'bg-green-500' : 'bg-red-500'
    }`;
    notification.innerHTML = `
        <div class="flex items-center space-x-3">
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} text-lg"></i>
            <span class="font-medium">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Animate in
    setTimeout(() => {
        notification.classList.add('translate-x-0', 'opacity-100');
    }, 10);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        notification.classList.remove('translate-x-0', 'opacity-100');
        notification.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => notification.remove(), 300);
    }, 6000);
}

// Make functions globally available
window.openMessageModal = openMessageModal;
window.closeMessageModal = closeMessageModal;
window.openReviewModal = openReviewModal;
window.closeReviewModal = closeReviewModal;
window.openEditReviewModal = openEditReviewModal;
window.closeEditReviewModal = closeEditReviewModal;
window.openDeleteReviewModal = openDeleteReviewModal;
window.closeDeleteReviewModal = closeDeleteReviewModal;
window.confirmDeleteReview = confirmDeleteReview;

console.log('All JavaScript functionality loaded successfully');
</script>


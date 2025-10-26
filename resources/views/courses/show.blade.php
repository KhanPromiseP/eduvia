@extends('layouts.app')

@section('content')

    <!-- Course Hero Section -->
    <div class="course-hero text-white fade-in">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
            <div class="flex flex-col lg:flex-row justify-between items-start gap-8">
                <div class="lg:w-2/3">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center space-x-2 text-sm text-indigo-200 mb-4">
                        <a href="{{ route('courses.index') }}" class="hover:text-white transition duration-200">Courses</a>
                        <span class="text-indigo-300">></span>
                        <a href="{{ route('courses.index') }}" class="hover:text-white transition duration-200">
                            {{ $course->category->name ?? 'All Categories' }}
                        </a>
                        <span class="text-indigo-300">></span>
                        <span class="text-white font-medium">{{ Str::limit($course->title, 50) }}</span>
                    </nav>
                    
                    <!-- Course Title -->
                    <h1 class="text-4xl lg:text-5xl mb-4 font-extrabold text-gray-900 tracking-tight font-serif">{{ $course->title }}</h1>
                    
                    <!-- Course Description -->
                    <p class="text-xl text-indigo-200 mb-6 max-w-3xl leading-relaxed">
                        {{ Str::limit($course->description, 200) }}
                    </p>
                    
                    <!-- Course Rating and Stats -->
                    <div class="flex flex-wrap items-center gap-6 mb-6">
                        <!-- Rating -->
                        <div class="flex items-center">
                            @php
                                $courseRating = $course->average_rating ?? 0;
                                $totalRatings = $course->total_reviews ?? 0;
                            @endphp
                            <div class="rating-stars mr-2">
                                <span class="text-2xl font-bold mr-2" id="heroAverageRating">{{ number_format($courseRating, 1) }}</span>
                                <div class="flex" id="heroStars">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($courseRating))
                                            <i class="fas fa-star text-yellow-400 text-lg"></i>
                                        @elseif($i - 0.5 <= $courseRating)
                                            <i class="fas fa-star-half-alt text-yellow-400 text-lg"></i>
                                        @else
                                            <i class="far fa-star text-yellow-400 text-lg"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <a href="#reviews" class="text-indigo-200 hover:text-white underline transition duration-200">
                                (<span id="heroTotalRatings">{{ number_format($totalRatings) }}</span> ratings)
                            </a>
                        </div>
                        
                        <!-- Students -->
                        <div class="flex items-center text-indigo-200">
                            <i class="fas fa-users mr-2 text-lg"></i>
                            <span>
                                {{ number_format($course->total_enrollments) }} students
                            </span>
                        </div>
                        
                        <!-- Last Updated -->
                        <div class="flex items-center text-indigo-200">
                            <i class="fas fa-clock mr-2 text-lg"></i>
                            <span>Last updated {{ $course->updated_at->format('M Y') }}</span>
                        </div>
                    </div>
                    
                    <!-- Created by -->
                    <div class="flex items-center">
                        <span class="text-indigo-200 mr-3">Created by</span>
                        <a href="{{ route('instructor.profile', $course->instructor->id) }}" >
                            <div class="flex items-center bg-white bg-opacity-10 rounded-full pl-1 pr-4 py-1">
            
                                @if($course->instructor->profile_path ?? false)
                                    <img src="{{ asset('storage/' . $course->instructor->profile_path) }}" 
                                        alt="{{ $course->instructor->name }}" 
                                        class="w-8 h-8 rounded-full mr-2 border-2 border-white">
                                @else
                                    <div class="w-8 h-8 bg-white bg-opacity-20 rounded-full flex items-center justify-center mr-2">
                                        <span class="text-white text-sm font-semibold">
                                            {{ substr($course->instructor->name ?? 'I', 0, 1) }}
                                        </span>
                                    </div>
                                @endif
                                <span class="font-semibold text-white">{{ $course->instructor->name ?? 'Professional Instructor' }}</span>
                           
                            </div>
                         </a>
                    </div>
                </div>
                
                <!-- Course Preview Video -->
                <div class="lg:w-1/3 w-full">
                    <div class="bg-black bg-opacity-20 rounded-2xl p-6 hover-lift">
                        <div class="aspect-w-16 aspect-h-9 bg-gray-800 rounded-xl mb-4 overflow-hidden shadow-2xl">
                            @if($course->preview_video)
                                <div class="w-full h-48 bg-gray-900 rounded-xl flex items-center justify-center cursor-pointer relative group"
                                     onclick="openVideoModal('{{ $course->preview_video }}', 'Course Preview')">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                                    <i class="fas fa-play-circle text-white text-5xl z-10 group-hover:scale-110 transition duration-300"></i>
                                    <div class="absolute bottom-4 left-4 z-10">
                                        <span class="text-white font-semibold">Course Preview</span>
                                    </div>
                                </div>
                            @else
                                <div class="w-full h-48 bg-gradient-to-br from-indigo-400 to-purple-600 rounded-xl flex items-center justify-center relative">
                                    <i class="fas fa-play-circle text-white text-6xl"></i>
                                    <div class="absolute bottom-4 left-4">
                                        <span class="text-white font-semibold">Course Preview</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <p class="text-center text-indigo-200 text-sm font-medium">Watch course introduction</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="flex flex-col lg:flex-row gap-8 fade-in">
            <!-- Main Content -->
            <div class="lg:w-2/3 space-y-8">
                <!-- What You'll Learn -->
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-lift">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">What you'll learn</h2>
                    <div class="grid md:grid-cols-2 gap-6">
                        @if($course->objectives)
                            @php
                                $objectives = array_slice(explode("\n", $course->objectives), 0, 8);
                            @endphp
                            @foreach($objectives as $objective)
                                @if(trim($objective))
                                    <div class="flex items-start group">
                                        <div class="bg-green-100 rounded-full p-2 mr-4 group-hover:bg-green-200 transition duration-200">
                                            <i class="fas fa-check text-green-600 text-sm"></i>
                                        </div>
                                        <span class="text-gray-700 leading-relaxed">{{ trim($objective) }}</span>
                                    </div>
                                @endif
                            @endforeach
                        @else
                            <div class="col-span-2 text-center py-8">
                                <i class="fas fa-bullseye text-gray-300 text-4xl mb-3"></i>
                                <p class="text-gray-500">Learning objectives coming soon</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Course Content -->
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-lift">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
                        <h2 class="text-3xl font-bold text-gray-900 mb-4 sm:mb-0">Course Content</h2>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 bg-gray-100 px-3 py-1 rounded-full">
                                {{ $course->modules->count() }} sections • 
                                {{ $course->modules->sum(fn($module) => $module->attachments->count()) }} lectures • 
                                {{ $course->duration ?? 'All' }} total length
                            </span>
                            <button onclick="toggleAllModules()" class="text-indigo-600 hover:text-indigo-800 text-sm font-medium transition duration-200">
                                Expand all
                            </button>
                        </div>
                    </div>

                    <!-- Modules -->
                    <div class="space-y-4">
                        @foreach($course->modules->sortBy('order') as $module)
                        <div class="border border-gray-200 rounded-xl overflow-hidden hover:border-gray-300 transition duration-200">
                            <!-- Module Header -->
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-4 flex justify-between items-center cursor-pointer group"
                                 onclick="toggleModule({{ $module->id }})">
                                <div class="flex items-center space-x-4">
                                    <div class="transform group-hover:scale-110 transition duration-200">
                                        <i class="fas fa-caret-down text-gray-500 text-lg" id="icon-{{ $module->id }}"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-semibold text-xl">
                                            <span class="text-blue-600">Module {{ $loop->iteration }}:</span> 
                                            <span class="text-gray-900">{{ $module->title }}</span>
                                        </h3>
                                        @if($module->description)
                                            <p class="text-sm text-gray-600 mt-1">{{ Str::limit($module->description, 80) }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center space-x-3">
                                    <span class="text-sm text-gray-600 bg-white px-3 py-1 rounded-full">
                                        {{ $module->attachments->count() }} lectures • {{ calculateModuleDuration($module) }} min
                                    </span>
                                    @if($module->is_free)
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            <i class="fas fa-unlock mr-1"></i> Free Preview
                                        </span>
                                    @elseif(!$userHasPurchased)
                                        <span class="bg-gray-100 text-gray-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            <i class="fas fa-lock mr-1"></i> Locked
                                        </span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-3 py-1 rounded-full">
                                            <i class="fas fa-check mr-1"></i> Unlocked
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Module Content -->
                            <div class="module-content collapsed bg-white" id="module-{{ $module->id }}">
                                <div class="p-6">
                                    @if($module->attachments->count() > 0)
                                    <div class="space-y-3">
                                        @foreach($module->attachments->sortBy('order') as $index => $attachment)
                                        <div class="flex items-start p-4 rounded-lg border border-gray-100 attachment-card hover:border-indigo-200 transition-all duration-200 group">
                                            <div class="flex-shrink-0 mr-4 relative">
                                                @if($attachment->file_type === 'external_video' && $attachment->video_url)
                                                    <!-- YouTube/External Video Thumbnail -->
                                                    @php
                                                        $youtubeId = getYoutubeId($attachment->video_url);
                                                        $thumbnailUrl = $youtubeId ? "https://img.youtube.com/vi/{$youtubeId}/hqdefault.jpg" : ($attachment->thumbnail_url ?? asset('storage/default-video-thumbnail.jpg'));
                                                    @endphp
                                                    <div class="youtube-thumbnail w-32 h-20 bg-gray-800 rounded-lg overflow-hidden shadow-md" 
                                                         @if($module->is_free || $userHasPurchased) 
                                                         onclick="openExternalVideoModal('{{ $attachment->video_url }}', '{{ $attachment->title }}')"
                                                         @else
                                                         onclick="showPurchaseMessage()"
                                                         @endif>
                                                        <img src="{{ $thumbnailUrl }}" alt="{{ $attachment->title }}" class="w-full h-full object-cover">
                                                        <div class="absolute inset-0 flex items-center justify-center">
                                                            <i class="fas fa-play text-white text-xl bg-red-600 rounded-full p-3 shadow-lg"></i>
                                                        </div>
                                                        @if(!$module->is_free && !$userHasPurchased)
                                                            <div class="purchase-overlay rounded-lg">
                                                                <i class="fas fa-shopping-cart text-xl mb-2"></i>
                                                                <span class="text-xs font-semibold">Purchase to access</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                                    <div class="video-thumbnail w-32 h-20 bg-gray-800 rounded-lg overflow-hidden shadow-md" 
                                                         @if($module->is_free || $userHasPurchased) 
                                                         onclick="openVideoModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')"
                                                         @else
                                                         onclick="showPurchaseMessage()"
                                                         @endif>
                                                        @if($module->is_free || $userHasPurchased)
                                                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-purple-600 to-blue-600">
                                                                <i class="fas fa-play text-white text-xl"></i>
                                                            </div>
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center bg-gray-700">
                                                                <i class="fas fa-lock text-gray-400 text-xl"></i>
                                                            </div>
                                                            <div class="purchase-overlay rounded-lg">
                                                                <i class="fas fa-shopping-cart text-xl mb-2"></i>
                                                                <span class="text-xs font-semibold">Purchase to access</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif($attachment->file_type === 'pdf')
                                                    <div class="w-32 h-20 bg-red-50 rounded-lg flex items-center justify-center relative border border-red-200 shadow-md group-hover:border-red-300 transition duration-200"
                                                         @if(!$module->is_free && !$userHasPurchased) onclick="showPurchaseMessage()" @endif>
                                                        <i class="fas fa-file-pdf text-red-500 text-3xl"></i>
                                                        @if(!$module->is_free && !$userHasPurchased)
                                                            <div class="purchase-overlay rounded-lg">
                                                                <i class="fas fa-shopping-cart text-xl mb-2"></i>
                                                                <span class="text-xs font-semibold">Purchase to access</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                                    <div class="w-32 h-20 bg-gray-200 rounded-lg overflow-hidden relative shadow-md"
                                                         @if(!$module->is_free && !$userHasPurchased) onclick="showPurchaseMessage()" @endif>
                                                        @if($module->is_free || $userHasPurchased)
                                                            <img src="{{ asset('storage/' . $attachment->file_path) }}" alt="{{ $attachment->title }}" class="w-full h-full object-cover">
                                                        @else
                                                            <div class="w-full h-full flex items-center justify-center bg-gray-300">
                                                                <i class="fas fa-lock text-gray-500 text-xl"></i>
                                                            </div>
                                                            <div class="purchase-overlay rounded-lg">
                                                                <i class="fas fa-shopping-cart text-xl mb-2"></i>
                                                                <span class="text-xs font-semibold">Purchase to access</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="w-32 h-20 bg-gray-100 rounded-lg flex items-center justify-center relative border border-gray-200 shadow-md group-hover:border-gray-300 transition duration-200"
                                                         @if(!$module->is_free && !$userHasPurchased) onclick="showPurchaseMessage()" @endif>
                                                        <i class="fas fa-file text-gray-600 text-3xl"></i>
                                                        @if(!$module->is_free && !$userHasPurchased)
                                                            <div class="purchase-overlay rounded-lg">
                                                                <i class="fas fa-shopping-cart text-xl mb-2"></i>
                                                                <span class="text-xs font-semibold">Purchase to access</span>
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="flex-grow">
                                                <div class="flex justify-between items-start">
                                                    <div class="flex-1">
                                                        <h4 class="font-semibold text-gray-900 group-hover:text-indigo-700 transition duration-200">
                                                            {{ $index + 1 }}. {{ $attachment->title }}
                                                        </h4>
                                                        <p class="text-sm text-gray-600 mt-1 flex items-center">
                                                            @if($attachment->file_type === 'external_video')
                                                                <i class="fab fa-youtube mr-2 text-red-500"></i> YouTube Video
                                                            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                                                <i class="fas fa-play-circle mr-2 text-purple-500"></i> Video
                                                            @elseif($attachment->file_type === 'pdf')
                                                                <i class="fas fa-file-pdf mr-2 text-red-500"></i> PDF Document
                                                            @elseif($attachment->file_type === 'doc' || $attachment->file_type === 'docx')
                                                                <i class="fas fa-file-word mr-2 text-blue-500"></i> Word Document
                                                            @elseif($attachment->file_type === 'zip')
                                                                <i class="fas fa-file-archive mr-2 text-yellow-500"></i> ZIP Archive
                                                            @elseif(in_array($attachment->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                                                <i class="fas fa-image mr-2 text-green-500"></i> Image
                                                            @else
                                                                <i class="fas fa-file mr-2 text-gray-500"></i> File
                                                            @endif
                                                            <span class="mx-2">•</span>
                                                            <span>{{ getFileDuration($attachment) }}</span>
                                                        </p>
                                                        @if($attachment->description)
                                                            <p class="text-sm text-gray-500 mt-2 leading-relaxed">{{ Str::limit($attachment->description, 120) }}</p>
                                                        @endif
                                                    </div>
                                                    
                                                    <div class="flex items-center space-x-2">
                                                        @if($module->is_free)
                                                            @if($attachment->file_type === 'external_video')
                                                                <button onclick="openExternalVideoModal('{{ $attachment->video_url }}', '{{ $attachment->title }}')" 
                                                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 font-medium text-sm">
                                                                    <i class="fas fa-play-circle mr-2"></i> Preview
                                                                </button>
                                                            @elseif(in_array($attachment->file_type, ['mp4', 'mov', 'avi', 'mkv']))
                                                                <button onclick="openVideoModal('{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')" 
                                                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 font-medium text-sm">
                                                                    <i class="fas fa-play-circle mr-2"></i> Preview
                                                                </button>
                                                            @else
                                                                <button onclick="openContentModal('{{ $attachment->file_type }}', '{{ asset('storage/' . $attachment->file_path) }}', '{{ $attachment->title }}')" 
                                                                        class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 font-medium text-sm">
                                                                    <i class="fas fa-eye mr-2"></i> Preview
                                                                </button>
                                                            @endif
                                                        @elseif($userHasPurchased)
                                                            <a href="{{ route('userdashboard', $course->id) }}" 
                                                               class="bg-green-600 text-white px-4 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-medium text-sm">
                                                                <i class="fas fa-sign-in-alt mr-2"></i> Access
                                                            </a>
                                                        @else
                                                            <span class="text-gray-400 bg-gray-100 px-3 py-2 rounded-lg">
                                                                <i class="fas fa-lock"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center py-8 text-gray-500">
                                        <i class="fas fa-folder-open text-4xl mb-3 text-gray-300"></i>
                                        <p>No content available in this module yet.</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Requirements -->
                @if($course->requirements)
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-lift">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Requirements</h2>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($course->requirements)) !!}
                    </div>
                </div>
                @endif

                <!-- Description -->
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-lift">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Description</h2>
                    <div class="prose max-w-none text-gray-700 leading-relaxed">
                        {!! nl2br(e($course->description)) !!}
                    </div>
                </div>

                <!-- Instructor Section -->
                @include('components.instructor-profile')
                @include('components.related-courses')

               
                <!-- Student Reviews -->
                <div class="bg-white rounded-2xl shadow-lg p-8 hover-lift" id="reviews">
                    <h2 class="text-3xl font-bold text-gray-900 mb-6">Student Reviews</h2>
                    
              
                <!-- Overall Rating Summary -->
                <div class="grid md:grid-cols-3 gap-8 mb-8" id="ratingSummary">
                    <!-- Average Rating -->
                    <div class="text-center">
                        <div class="text-5xl font-bold text-gray-900 mb-2" id="averageRating">
                            {{ number_format($course->average_rating, 1) }}
                        </div>
                        <div class="rating-stars justify-center mb-2">
                            @php
                                $averageRating = $course->average_rating ?? 0;
                                $totalReviews = $course->total_reviews ?? 0;
                            @endphp
                            <div class="flex justify-center" id="averageStars">
                                @for($i = 1; $i <= 5; $i++)
                                    @if($i <= floor($averageRating))
                                        <i class="fas fa-star text-yellow-400 text-xl"></i>
                                    @elseif($i - 0.5 <= $averageRating)
                                        <i class="fas fa-star-half-alt text-yellow-400 text-xl"></i>
                                    @else
                                        <i class="far fa-star text-yellow-400 text-xl"></i>
                                    @endif
                                @endfor
                            </div>
                        </div>
                        <p class="text-gray-600 text-sm" id="totalReviewsText">
                            Course Rating • <span id="totalReviews">{{ number_format($totalReviews) }}</span> reviews
                        </p>
                    </div>

                    <!-- Rating Breakdown -->
                    <div class="md:col-span-2" id="ratingBreakdown">
                        @php
                            // Calculate breakdown directly from database for accuracy
                            $approvedReviews = $course->approvedReviews;
                            $totalReviews = $approvedReviews->count();
                            $averageRating = $totalReviews > 0 ? $approvedReviews->avg('rating') : 0;
                            
                            $breakdown = [];
                            for ($i = 1; $i <= 5; $i++) {
                                $count = $approvedReviews->where('rating', $i)->count();
                                $percentage = $totalReviews > 0 ? round(($count / $totalReviews) * 100) : 0;
                                $breakdown[$i] = ['count' => $count, 'percentage' => $percentage];
                            }
                        @endphp
                        
                        @for($i = 5; $i >= 1; $i--)
                            <div class="flex items-center mb-2 rating-bar" data-rating="{{ $i }}">
                                <div class="w-20 flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">{{ $i }} star</span>
                                    <span class="text-xs text-gray-400 rating-count">({{ $breakdown[$i]['count'] }})</span>
                                </div>
                                <div class="flex-1 mx-3">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5">
                                        <div class="bg-yellow-400 h-2.5 rounded-full transition-all duration-500 ease-out rating-progress" 
                                            style="width: {{ $breakdown[$i]['percentage'] }}%"></div>
                                    </div>
                                </div>
                                <div class="w-16 text-sm text-gray-600 text-right rating-percentage">
                                    {{ $breakdown[$i]['percentage'] }}%
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>


                <!-- Write Review Section -->       
                @auth
                    @php
                        $userReview = auth()->user()->reviews()->where('course_id', $course->id)->first();
                    @endphp

                    @if($userHasPurchased)
                        <div class="bg-gray-50 rounded-xl p-6 mb-8 border border-gray-200" id="reviewFormSection">
                            <h3 class="text-xl font-semibold text-gray-900 mb-4" id="reviewFormTitle">
                                {{ $userReview ? 'Edit Your Review' : 'Write a Review' }}
                            </h3>
                            
                            <!-- Your review form here -->
                            <form id="reviewForm" 
                                action="{{ $userReview ? route('reviews.update', $userReview) : route('reviews.store', $course) }}" 
                                method="POST">
                                @csrf
                                @if($userReview)
                                    @method('PUT')
                                @endif

                                <!-- Star Rating -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 mb-2">
                                        How would you rate this course?
                                    </label>
                                    <div class="flex space-x-1" id="starRating">
                                        @for($i = 1; $i <= 5; $i++)
                                            <button type="button" 
                                                    class="text-2xl focus:outline-none star-btn {{ $userReview && $userReview->rating >= $i ? 'text-yellow-400' : 'text-gray-300' }}"
                                                    data-rating="{{ $i }}">
                                                <i class="fas fa-star"></i>
                                            </button>
                                        @endfor
                                    </div>
                                    <input type="hidden" name="rating" id="selectedRating" value="{{ $userReview->rating ?? '' }}" required>
                                    <p class="text-red-500 text-sm mt-1 hidden" id="ratingError">Please select a rating</p>
                                </div>

                                <!-- Review Comment -->
                                <div class="mb-4">
                                    <label for="comment" class="block text-sm font-medium text-gray-700 mb-2">
                                        Your Review
                                    </label>
                                    <textarea name="comment" id="comment" rows="4" 
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                                            placeholder="Share your experience with this course... What did you like? What could be improved?"
                                            required>{{ old('comment', $userReview->comment ?? '') }}</textarea>
                                    <div class="flex justify-between text-sm text-gray-500 mt-1">
                                        <span>Minimum 10 characters</span>
                                        <span id="charCount">{{ strlen(old('comment', $userReview->comment ?? '')) }}/1000</span>
                                    </div>
                                </div>

                                <!-- Submit Button -->
                                <div class="flex justify-end space-x-3">
                                    @if($userReview)
                                        <button type="button" 
                                                onclick="confirmDelete({{ $userReview->id }})"
                                                class="px-4 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition duration-200">
                                            Delete Review
                                        </button>
                                    @endif
                                    <button type="submit" 
                                            class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold">
                                        {{ $userReview ? 'Update Review' : 'Submit Review' }}
                                    </button>
                                </div>
                            </form>

                            <!-- Delete Form -->
                            @if($userReview)
                                <form id="deleteForm-{{ $userReview->id }}" 
                                    action="{{ route('reviews.destroy', $userReview) }}" 
                                    method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="bg-blue-50 border border-blue-200 rounded-xl p-6 mb-8 text-center">
                            <i class="fas fa-graduation-cap text-blue-500 text-4xl mb-3"></i>
                            <h3 class="text-lg font-semibold text-blue-900 mb-2">Purchase to Review</h3>
                            <p class="text-blue-700 mb-4">You need to enroll in this course to leave a review.</p>
                        
                            @if($course->is_premium)
                                <form action="{{ route('payment.initiate', $course) }}" method="POST">
                                    @csrf
                                    <button type="submit" 
                                            class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                                        Enroll Now - ${{ number_format($course->price, 2) }}
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('quick.purchase', $course) }}" 
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                                    Enroll For Free
                                </a>
                            @endif
                            
                            <p class="mt-3 text-center text-sm text-gray-600 mb-4">30-Day Money-Back Guarantee</p>
                        </div>
                    @endif
                @else
                    <div class="bg-gray-50 border border-gray-200 rounded-xl p-6 mb-8 text-center">
                        <i class="fas fa-user-plus text-gray-500 text-4xl mb-3"></i>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Join to Review</h3>
                        <p class="text-gray-700 mb-4">Sign in to share your thoughts about this course.</p>
                        <a href="{{ route('login') }}" 
                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold inline-flex items-center">
                            <i class="fas fa-sign-in-alt mr-2"></i> Sign In
                        </a>
                    </div>
                @endauth

        <!-- Reviews List -->
        <div class="space-y-6" id="reviewsList">
            <h3 class="text-xl font-semibold text-gray-900 mb-4">
                Student Reviews (<span id="reviewsCount">{{ $course->approvedReviews->count() }}</span>)
            </h3>

            <div id="reviewsContainer">
                @if($course->approvedReviews->count() > 0)
                    @foreach($course->approvedReviews->sortByDesc('created_at') as $review)
                        @include('partials.review-item', ['review' => $review])
                    @endforeach
                @else
                    <!-- No Reviews State -->
                    <div class="text-center py-12" id="noReviews">
                        <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
                        <h3 class="text-xl font-semibold text-gray-600 mb-2">No reviews yet</h3>
                        <p class="text-gray-500 mb-4">Be the first to review this course!</p>
                        @auth
                            @if($userHasPurchased) {{-- Changed from $hasPurchased to $userHasPurchased --}}
                                <button onclick="document.getElementById('reviewForm').scrollIntoView({ behavior: 'smooth' })" 
                                        class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold">
                                    Write a Review
                                </button>
                            @else
                                @if($course->is_premium)
                                    <form action="{{ route('payment.initiate', $course) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold">
                                            Enroll Now - ${{ number_format($course->price, 2) }}
                                        </button>
                                    </form>
                                @else
                                    <a href="{{ route('quick.purchase', $course) }}" 
                                    class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200 font-semibold">
                                        Enroll For Free
                                    </a>
                                @endif
                                
                            @endif
                        @else
                            <div class="space-y-3">
                                <a href="{{ route('login') }}" 
                                class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold inline-block">
                                    <i class="fas fa-sign-in-alt mr-2"></i>Sign In to Review
                                </a>
                                <p class="text-sm text-gray-500">Sign in and enroll to share your experience</p>
                            </div>
                        @endauth
                    </div>
                @endif
            </div>

            <!-- Load More Button -->
            @if($course->approvedReviews->count() > 5)
                <div class="text-center mt-8">
                    <button class="bg-white border border-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-50 transition duration-200 font-semibold">
                        Load More Reviews
                    </button>
                </div>
            @endif
        </div>
        </div>
            </div>

            <!-- Sidebar -->
            <div class="lg:w-1/3">
                <div class="space-y-6 sticky top-[70px]">
                    @include('components.course-sidebar')
                </div>
            </div>
        </div>
    </div>

    <!-- Video Modal -->
    <div id="videoModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden fade-in">
        <div class="bg-white rounded-2xl overflow-hidden w-full max-w-4xl mx-4 shadow-2xl">
            <div class="relative pt-[56.25%]">
                <div id="modalVideoContainer" class="absolute inset-0 w-full h-full bg-black rounded-t-2xl">
                    <!-- Content will be dynamically inserted here -->
                </div>
            </div>
            <div class="p-6 flex justify-between items-center bg-white">
                <h3 id="videoTitle" class="text-xl font-semibold text-gray-900"></h3>
                <button onclick="closeVideoModal()" class="text-gray-500 hover:text-gray-700 transition duration-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Content Modal -->
    <div id="contentModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden fade-in">
        <div class="bg-white rounded-2xl overflow-hidden w-full max-w-4xl mx-4 max-h-[90vh] shadow-2xl">
            <div class="p-6 flex justify-between items-center border-b border-gray-200 bg-white">
                <h3 id="contentTitle" class="text-xl font-semibold text-gray-900"></h3>
                <button onclick="closeContentModal()" class="text-gray-500 hover:text-gray-700 transition duration-200">
                    <i class="fas fa-times text-2xl"></i>
                </button>
            </div>
            <div class="p-6 max-h-[70vh] overflow-auto bg-gray-50">
                <div id="contentContainer"></div>
            </div>
        </div>
    </div>

    <!-- Purchase Message Toast -->
    <div id="purchaseToast" class="custom-toast toast-info hidden">
        <div class="flex items-start space-x-3 p-4">
            <i class="fas fa-info-circle text-lg mt-0.5 flex-shrink-0"></i>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-sm leading-tight">Please purchase the course to access this content</p>
            </div>
            <button type="button" onclick="document.getElementById('purchaseToast').classList.add('hidden')" 
                    class="flex-shrink-0 ml-4 text-white hover:text-gray-200 transition-colors duration-200">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>
    </div>

    <script>
    // Module Toggle Functions
    function toggleModule(moduleId) {
        const moduleContent = document.getElementById(`module-${moduleId}`);
        const icon = document.getElementById(`icon-${moduleId}`);
        
        if (moduleContent.classList.contains('collapsed')) {
            moduleContent.classList.remove('collapsed');
            moduleContent.classList.add('expanded');
            icon.classList.remove('fa-caret-down');
            icon.classList.add('fa-caret-up');
        } else {
            moduleContent.classList.remove('expanded');
            moduleContent.classList.add('collapsed');
            icon.classList.remove('fa-caret-up');
            icon.classList.add('fa-caret-down');
        }
    }

    function toggleAllModules() {
        const modules = document.querySelectorAll('.module-content');
        const allCollapsed = Array.from(modules).every(module => module.classList.contains('collapsed'));
        
        modules.forEach((module, index) => {
            const moduleId = module.id.split('-')[1];
            const icon = document.getElementById(`icon-${moduleId}`);
            
            if (allCollapsed) {
                module.classList.remove('collapsed');
                module.classList.add('expanded');
                icon.classList.remove('fa-caret-down');
                icon.classList.add('fa-caret-up');
            } else {
                module.classList.remove('expanded');
                module.classList.add('collapsed');
                icon.classList.remove('fa-caret-up');
                icon.classList.add('fa-caret-down');
            }
        });
    }

    // Extract YouTube ID from URL
    function extractYouTubeId(url) {
        const regExp = /^.*((youtu.be\/)|(v\/)|(\/u\/\w\/)|(embed\/)|(watch\?))\??v?=?([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[7].length === 11) ? match[7] : null;
    }

    // Modal Functions
    function openExternalVideoModal(videoUrl, title) {
        const modal = document.getElementById('videoModal');
        const videoContainer = document.getElementById('modalVideoContainer');
        const videoTitle = document.getElementById('videoTitle');
        
        videoContainer.innerHTML = '';
        
        const youtubeId = extractYouTubeId(videoUrl);
        if (youtubeId) {
            const iframe = document.createElement('iframe');
            iframe.src = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&rel=0&modestbranding=1`;
            iframe.className = 'w-full h-full rounded-t-2xl';
            iframe.allowFullscreen = true;
            iframe.frameBorder = '0';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            videoContainer.appendChild(iframe);
        } else {
            videoContainer.innerHTML = `
                <div class="w-full h-full flex items-center justify-center bg-gray-800 rounded-t-2xl">
                    <div class="text-center text-white p-8">
                        <i class="fas fa-external-link-alt text-5xl mb-4"></i>
                        <h3 class="text-2xl font-semibold mb-3">External Video Content</h3>
                        <p class="text-lg mb-6">This video is hosted on an external platform.</p>
                        <a href="${videoUrl}" target="_blank" class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition duration-200 font-semibold text-lg">
                            Watch on External Site
                        </a>
                    </div>
                </div>
            `;
        }
        
        videoTitle.textContent = title;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        document.addEventListener('keydown', function handleEscape(e) {
            if (e.key === 'Escape') {
                closeVideoModal();
                document.removeEventListener('keydown', handleEscape);
            }
        });
    }

    function openVideoModal(videoSrc, title) {
        const modal = document.getElementById('videoModal');
        const videoContainer = document.getElementById('modalVideoContainer');
        const videoTitle = document.getElementById('videoTitle');
        
        videoContainer.innerHTML = '';
        
        const video = document.createElement('video');
        video.src = videoSrc;
        video.controls = true;
        video.className = 'w-full h-full rounded-t-2xl';
        video.controlsList = 'nodownload';
        
        videoContainer.appendChild(video);
        videoTitle.textContent = title;
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        setTimeout(() => {
            video.play().catch(e => console.log('Autoplay prevented:', e));
        }, 300);
        
        document.addEventListener('keydown', function handleEscape(e) {
            if (e.key === 'Escape') {
                closeVideoModal();
                document.removeEventListener('keydown', handleEscape);
            }
        });

        video.addEventListener('contextmenu', function(e) {
            e.preventDefault();
        });
    }

    function openContentModal(fileType, fileSrc, title) {
        const modal = document.getElementById('contentModal');
        const container = document.getElementById('contentContainer');
        const contentTitle = document.getElementById('contentTitle');
        
        contentTitle.textContent = title;
        
        if (fileType === 'pdf') {
            container.innerHTML = `<iframe src="${fileSrc}#toolbar=0" class="w-full h-[70vh] rounded-lg" frameborder="0"></iframe>`;
        } else if (['doc', 'docx'].includes(fileType)) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-file-word text-blue-500 text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Word Document</h4>
                    <p class="text-gray-600 mb-6">Preview not available in browser</p>
                    <a href="${fileSrc}" download 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200 font-semibold inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download Document
                    </a>
                </div>
            `;
        } else if (['mp4', 'mov', 'avi', 'mkv'].includes(fileType)) {
            container.innerHTML = `<video src="${fileSrc}" controls class="w-full h-96 bg-black rounded-lg" controlsList="nodownload"></video>`;
        } else if (fileType === 'zip') {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-file-archive text-yellow-500 text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">ZIP Archive</h4>
                    <p class="text-gray-600 mb-6">Download the ZIP file to view its contents.</p>
                    <a href="${fileSrc}" download 
                       class="bg-yellow-600 text-white px-6 py-3 rounded-lg hover:bg-yellow-700 transition duration-200 font-semibold inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download ZIP
                    </a>
                </div>
            `;
        } else if (['mp3', 'wav', 'ogg'].includes(fileType)) {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-music text-purple-500 text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">Audio File</h4>
                    <audio src="${fileSrc}" controls class="w-full h-24 mt-4 bg-gray-800 rounded-lg" controlsList="nodownload"></audio>
                </div>
            `;
        } else if (['jpg', 'jpeg', 'png', 'gif'].includes(fileType)) {
            container.innerHTML = `<img src="${fileSrc}" alt="${title}" class="max-w-full mx-auto rounded-lg shadow-lg">`;
        } else {
            container.innerHTML = `
                <div class="text-center py-12">
                    <i class="fas fa-file text-gray-400 text-6xl mb-4"></i>
                    <h4 class="text-xl font-semibold text-gray-800 mb-2">File Preview</h4>
                    <p class="text-gray-600 mb-6">Preview not available for this file type</p>
                    <a href="${fileSrc}" download 
                       class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition duration-200 font-semibold inline-flex items-center">
                        <i class="fas fa-download mr-2"></i> Download File
                    </a>
                </div>
            `;
        }
        
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
        
        document.addEventListener('keydown', function handleEscape(e) {
            if (e.key === 'Escape') {
                closeContentModal();
                document.removeEventListener('keydown', handleEscape);
            }
        });
    }

    function closeVideoModal() {
        const modal = document.getElementById('videoModal');
        const videoContainer = document.getElementById('modalVideoContainer');
        
        videoContainer.innerHTML = '';
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function closeContentModal() {
        const modal = document.getElementById('contentModal');
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }

    function showPurchaseMessage() {
        const toast = document.getElementById('purchaseToast');
        toast.classList.remove('hidden');
        
        // Auto hide after 5 seconds
        setTimeout(() => {
            toast.classList.add('hidden');
        }, 5000);
        
        // Also allow click to dismiss
        toast.addEventListener('click', function(e) {
            if (e.target === this || e.target.closest('button')) {
                this.classList.add('hidden');
            }
        });
    }

    // Review System AJAX Functions
    document.addEventListener('DOMContentLoaded', function() {
        initializeReviewSystem();
    });

    function initializeReviewSystem() {
        // Check if review form exists on this page
        const reviewForm = document.getElementById('reviewForm');
        if (!reviewForm) {
            console.log('Review form not found on this page, skipping review system initialization');
            return;
        }

        console.log('Review system initializing...');

        // Star Rating Interaction
        const starButtons = document.querySelectorAll('.star-btn');
        const selectedRating = document.getElementById('selectedRating');
        const ratingError = document.getElementById('ratingError');
        
        console.log('Star buttons found:', starButtons.length);
        console.log('Selected rating element:', selectedRating);
        console.log('Rating error element:', ratingError);

        starButtons.forEach(button => {
            button.addEventListener('click', function() {
                console.log('Star clicked, rating:', this.getAttribute('data-rating'));
                const rating = parseInt(this.getAttribute('data-rating'));
                selectedRating.value = rating;
                
                // Update star display
                starButtons.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.remove('text-gray-300');
                        star.classList.add('text-yellow-400');
                    } else {
                        star.classList.remove('text-yellow-400');
                        star.classList.add('text-gray-300');
                    }
                });
                
                // Hide error if shown
                if (ratingError) {
                    ratingError.classList.add('hidden');
                }
            });
            
            // Hover effect
            button.addEventListener('mouseenter', function() {
                const rating = parseInt(this.getAttribute('data-rating'));
                starButtons.forEach((star, index) => {
                    if (index < rating) {
                        star.classList.add('text-yellow-300');
                    }
                });
            });
            
            button.addEventListener('mouseleave', function() {
                const currentRating = parseInt(selectedRating.value) || 0;
                starButtons.forEach((star, index) => {
                    if (index >= currentRating) {
                        star.classList.remove('text-yellow-300');
                    }
                });
            });
        });

        // Character count for review text
        const commentTextarea = document.getElementById('comment');
        const charCount = document.getElementById('charCount');
        
        if (commentTextarea && charCount) {
            commentTextarea.addEventListener('input', function() {
                const length = this.value.length;
                charCount.textContent = `${length}/1000`;
                
                if (length > 1000) {
                    charCount.classList.add('text-red-500');
                } else {
                    charCount.classList.remove('text-red-500');
                }
            });
            
            // Initialize character count
            charCount.textContent = `${commentTextarea.value.length}/1000`;
        }

        // AJAX form submission - REMOVE THE DUPLICATE reviewForm DECLARATION HERE
        // const reviewForm = document.getElementById('reviewForm'); // ← REMOVE THIS LINE
        if (reviewForm) {
            reviewForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                if (!selectedRating.value) {
                    if (ratingError) {
                        ratingError.classList.remove('hidden');
                    }
                    document.getElementById('starRating').scrollIntoView({ 
                        behavior: 'smooth', 
                        block: 'center' 
                    });
                    return;
                }

                submitReviewForm(this);
            });
        }

        // Helpful votes functionality
        document.querySelectorAll('.helpful-btn').forEach(button => {
            button.addEventListener('click', function() {
                const reviewId = this.getAttribute('data-review-id');
                toggleHelpfulVote(this, reviewId);
            });
        });

        console.log('Review system initialized successfully!');
    }
    function submitReviewForm(form) {
        // Check if form exists and is valid
        if (!form) {
            console.error('Form is null or undefined');
            showCustomToast('Form error. Please refresh the page and try again.', 'error');
            return;
        }

        const formData = new FormData(form);
        const url = form.action;
        const method = form.method;
        const submitBtn = form.querySelector('button[type="submit"]');
        
        // Check if submit button exists
        if (!submitBtn) {
            console.error('Submit button not found');
            showCustomToast('Form error. Please refresh the page and try again.', 'error');
            return;
        }

        const originalText = submitBtn.innerHTML;

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Submitting...';
        submitBtn.disabled = true;

        fetch(url, {
            method: method,
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            }
        })
        .then(response => {
            console.log('Response status:', response.status);
            
            if (!response.ok) {
                // If it's a validation error, parse the JSON for error messages
                if (response.status === 422) {
                    return response.json().then(data => {
                        throw new Error(JSON.stringify(data.errors));
                    });
                }
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Success response:', data);
            if (data.success) {
                showCustomToast(data.message, 'success');
                updateReviewUI(data);
            } else {
                // Handle server-side errors
                let errorMessage = data.message || 'An error occurred';
                if (data.errors) {
                    errorMessage = 'Please fix the following errors: ' + Object.values(data.errors).join(', ');
                }
                showCustomToast(errorMessage, 'error');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            let errorMessage = 'An error occurred. Please try again.';
            
            try {
                // Try to parse validation errors
                const errorData = JSON.parse(error.message);
                if (typeof errorData === 'object') {
                    errorMessage = 'Please fix the following errors: ' + Object.values(errorData).flat().join(', ');
                }
            } catch (e) {
                // If not JSON, use the original error message
                if (error.message.includes('HTTP error')) {
                    errorMessage = 'Network error. Please check your connection and try again.';
                }
            }
            
            showCustomToast(errorMessage, 'error');
        })
        .finally(() => {
            // Reset button state safely
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    }

    function updateReviewUI(data) {
        console.log('Updating UI with data:', data);
        
        // Update rating summary and bars in real-time
        if (data.stats) {
            updateRatingSummary(data.stats);
            updateHeroRatings(data.stats);
        } else {
            console.warn('No stats data received');
        }
        
        // Update reviews list
        if (data.review) {
            addOrUpdateReview(data.review);
            updateReviewFormForEdit(data.review);
        } else {
            console.warn('No review data received');
        }
        
        // Update reviews count
        const reviewsCountElement = document.getElementById('reviewsCount');
        if (reviewsCountElement && data.stats) {
            reviewsCountElement.textContent = data.stats.total_reviews;
            console.log('Updated reviews count to:', data.stats.total_reviews);
        }
        
        console.log('Review UI updated successfully');
    }

    function updateReviewFormForEdit(review) {
        const form = document.getElementById('reviewForm');
        const formTitle = document.getElementById('reviewFormTitle');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        if (formTitle) {
            formTitle.textContent = 'Edit Your Review';
        }
        
        if (submitBtn) {
            submitBtn.textContent = 'Update Review';
        }
        
        // Update form action for PUT method
        form.action = "{{ route('reviews.update', ':id') }}".replace(':id', review.id);
        
        // Ensure method spoofing is present
        let methodInput = form.querySelector('input[name="_method"]');
        if (!methodInput) {
            methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            form.appendChild(methodInput);
        }
        methodInput.value = 'PUT';
        
        // Update delete button to show the correct review ID
        let deleteBtn = form.querySelector('button[type="button"][onclick*="confirmDelete"]');
        if (!deleteBtn) {
            // Create delete button if it doesn't exist
            deleteBtn = document.createElement('button');
            deleteBtn.type = 'button';
            deleteBtn.className = 'px-4 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 transition duration-200';
            deleteBtn.textContent = 'Delete Review';
            form.querySelector('.flex.justify-end.space-x-3').insertBefore(deleteBtn, submitBtn);
        }
        deleteBtn.setAttribute('onclick', `confirmDelete(${review.id})`);
        
        // Ensure form is visible
        const formSection = document.getElementById('reviewFormSection');
        if (formSection) {
            formSection.style.display = 'block';
        }
    }

    

    // Remove this duplicate function (the second one)
    function hideReviewForm() {
        const reviewFormSection = document.querySelector('.bg-gray-50.rounded-xl.p-6.mb-8.border.border-gray-200');
        if (reviewFormSection) {
            reviewFormSection.style.display = 'none';
            console.log('Review form section hidden');
        } else {
            console.warn('Review form section not found');
        }
        
        // Also hide the "Write a Review" button in the no reviews section
        const writeReviewBtn = document.querySelector('button[onclick*="reviewForm"]');
        if (writeReviewBtn) {
            writeReviewBtn.style.display = 'none';
        }
    }
    function updateRatingSummary(stats) {
        // Ensure average_rating is a number
        const averageRating = parseFloat(stats.average_rating) || 0;
        const totalReviews = parseInt(stats.total_reviews) || 0;
        
        console.log('Updating rating summary:', { averageRating, totalReviews });
        
        // Update average rating
        const averageRatingElement = document.getElementById('averageRating');
        const totalReviewsElement = document.getElementById('totalReviews');
        
        if (averageRatingElement) {
            averageRatingElement.textContent = averageRating.toFixed(1);
        }
        if (totalReviewsElement) {
            totalReviewsElement.textContent = totalReviews;
        }

        // Update average stars
        const averageStars = document.getElementById('averageStars');
        if (averageStars) {
            averageStars.innerHTML = '';
            for (let i = 1; i <= 5; i++) {
                const star = document.createElement('i');
                if (i <= Math.floor(averageRating)) {
                    star.className = 'fas fa-star text-yellow-400 text-xl';
                } else if (i - 0.5 <= averageRating) {
                    star.className = 'fas fa-star-half-alt text-yellow-400 text-xl';
                } else {
                    star.className = 'far fa-star text-yellow-400 text-xl';
                }
                averageStars.appendChild(star);
            }
        }

        // Update rating breakdown bars in real-time
        if (stats.rating_breakdown) {
            for (let i = 5; i >= 1; i--) {
                const ratingData = stats.rating_breakdown[i] || { count: 0, percentage: 0 };
                const bar = document.querySelector(`.rating-bar[data-rating="${i}"]`);
                if (bar) {
                    const countElement = bar.querySelector('.rating-count');
                    const progressElement = bar.querySelector('.rating-progress');
                    const percentageElement = bar.querySelector('.rating-percentage');
                    
                    if (countElement) {
                        countElement.textContent = `(${ratingData.count})`;
                    }
                    if (progressElement) {
                        // Use setTimeout to ensure CSS transition works
                        setTimeout(() => {
                            progressElement.style.width = `${ratingData.percentage}%`;
                        }, 10);
                    }
                    if (percentageElement) {
                        percentageElement.textContent = `${ratingData.percentage}%`;
                    }
                }
            }
        }
    }

    function updateHeroRatings(stats) {
        // Ensure average_rating is a number
        const averageRating = parseFloat(stats.average_rating) || 0;
        const totalReviews = parseInt(stats.total_reviews) || 0;
        
        document.getElementById('heroAverageRating').textContent = averageRating.toFixed(1);
        document.getElementById('heroTotalRatings').textContent = totalReviews;

        const heroStars = document.getElementById('heroStars');
        heroStars.innerHTML = '';
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('i');
            if (i <= Math.floor(averageRating)) {
                star.className = 'fas fa-star text-yellow-400 text-lg';
            } else if (i - 0.5 <= averageRating) {
                star.className = 'fas fa-star-half-alt text-yellow-400 text-lg';
            } else {
                star.className = 'far fa-star text-yellow-400 text-lg';
            }
            heroStars.appendChild(star);
        }
    }

    function addOrUpdateReview(review) {
        console.log('Adding/updating review:', review);
        
        const reviewsContainer = document.getElementById('reviewsContainer');
        const noReviews = document.getElementById('noReviews');
        const existingReview = document.querySelector(`.review-item[data-review-id="${review.id}"]`);

        // Remove no reviews message if it exists
        if (noReviews) {
            noReviews.remove();
            console.log('Removed "no reviews" message');
        }

        if (existingReview) {
            // Update existing review
            const commentElement = existingReview.querySelector('.review-comment');
            const starsElement = existingReview.querySelector('.rating-stars');
            const helpfulCount = existingReview.querySelector('.helpful-count');
            
            if (commentElement) {
                commentElement.textContent = review.comment;
            }
            if (starsElement) {
                starsElement.innerHTML = generateStarRating(review.rating);
            }
            if (helpfulCount) {
                helpfulCount.textContent = review.helpful_votes || 0;
            }
            console.log('Updated existing review');
        } else {
            // Add new review at the top
            const reviewHtml = generateReviewHTML(review);
            if (reviewsContainer.children.length > 0) {
                reviewsContainer.insertAdjacentHTML('afterbegin', reviewHtml);
                console.log('Added new review at top');
            } else {
                reviewsContainer.innerHTML = reviewHtml;
                console.log('Added first review');
            }
            
            // Re-initialize helpful buttons for the new review
            const newHelpfulBtn = reviewsContainer.querySelector(`.helpful-btn[data-review-id="${review.id}"]`);
            if (newHelpfulBtn) {
                newHelpfulBtn.addEventListener('click', function() {
                    const reviewId = this.getAttribute('data-review-id');
                    toggleHelpfulVote(this, reviewId);
                });
            }
        }
    }
    function generateReviewHTML(review) {
        const userInitial = review.user.name.charAt(0).toUpperCase();
        const verifiedBadge = review.is_verified ? 
            '<span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full flex items-center">' +
            '<i class="fas fa-check mr-1 text-xs"></i> Verified Student</span>' : '';

        return `
            <div class="border border-gray-200 rounded-xl p-6 hover:border-gray-300 transition duration-200 review-item" data-review-id="${review.id}">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center space-x-3">
                        ${review.user.avatar ? 
                            `<img src="{{ asset('storage/') }}/${review.user.profile_path}" alt="${review.user.name}" class="w-10 h-10 rounded-full">` :
                            `<div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center">
                                <span class="text-indigo-600 font-semibold text-sm">${userInitial}</span>
                            </div>`
                        }
                        <div>
                            <h4 class="font-semibold text-gray-900">${review.user.name}</h4>
                            <div class="flex items-center space-x-2 text-sm text-gray-500">
                                <div class="rating-stars">
                                    ${generateStarRating(review.rating)}
                                </div>
                                <span>•</span>
                                <span>${new Date(review.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
                                ${verifiedBadge}
                            </div>
                        </div>
                    </div>
                    <div class="flex space-x-2 review-actions">
                        <button onclick="editReview(${review.id})" class="text-gray-400 hover:text-indigo-600 transition duration-200">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button onclick="confirmDelete(${review.id})" class="text-gray-400 hover:text-red-600 transition duration-200">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p class="text-gray-700 leading-relaxed review-comment">${review.comment}</p>
                <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                    <div class="flex items-center space-x-4">
                        <button class="flex items-center space-x-1 text-gray-500 hover:text-gray-700 transition duration-200 helpful-btn" data-review-id="${review.id}">
                            <i class="far fa-thumbs-up"></i>
                            <span>Helpful (<span class="helpful-count">${review.helpful_votes || 0}</span>)</span>
                        </button>
                    </div>
                    <span class="text-sm text-indigo-600 font-medium">Your Review</span>
                </div>
            </div>
        `;
    }

    function generateStarRating(rating) {
        let stars = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                stars += '<i class="fas fa-star text-yellow-400 text-sm"></i>';
            } else {
                stars += '<i class="far fa-star text-yellow-400 text-sm"></i>';
            }
        }
        return stars;
    }

   

    function confirmDelete(reviewId) {
        if (confirm('Are you sure you want to delete this review? This action cannot be undone.')) {
            deleteReview(reviewId);
        }
    }

    function deleteReview(reviewId) {
        const url = "{{ route('reviews.destroy', ':id') }}".replace(':id', reviewId);
        
        fetch(url, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showCustomToast(data.message, 'success');
                removeReviewFromUI(reviewId);
                updateRatingSummary(data.stats);
                document.getElementById('reviewsCount').textContent = data.stats.total_reviews;
                resetReviewFormForNew();
            } else {
                showCustomToast(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showCustomToast('An error occurred. Please try again.', 'error');
        });
    }

    function removeReviewFromUI(reviewId) {
        const reviewElement = document.querySelector(`.review-item[data-review-id="${reviewId}"]`);
        if (reviewElement) {
            reviewElement.remove();
        }
        
        // Show no reviews message if no reviews left
        const reviewsContainer = document.getElementById('reviewsContainer');
        if (reviewsContainer.children.length === 0) {
            reviewsContainer.innerHTML = `
                <div class="text-center py-12" id="noReviews">
                    <i class="fas fa-comments text-gray-300 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No reviews yet</h3>
                    <p class="text-gray-500 mb-4">Be the first to review this course!</p>
                </div>
            `;
        }
    }

    function resetReviewFormForNew() {
    const form = document.getElementById('reviewForm');
    const formTitle = document.getElementById('reviewFormTitle');
    const submitBtn = form.querySelector('button[type="submit"]');
    const deleteBtn = form.querySelector('button[type="button"][onclick*="confirmDelete"]');
    
    // Reset form title and button text
    if (formTitle) {
        formTitle.textContent = 'Write a Review';
    }
    
    if (submitBtn) {
        submitBtn.textContent = 'Submit Review';
    }
    
    // Remove delete button if it exists
    if (deleteBtn) {
        deleteBtn.remove();
    }
    
    // Reset form values
    form.reset();
    form.action = "{{ route('reviews.store', $course) }}";
    
    // Remove method spoofing
    const methodInput = form.querySelector('input[name="_method"]');
    if (methodInput) {
        methodInput.remove();
    }
    
    // Reset stars
    const starButtons = document.querySelectorAll('.star-btn');
    const selectedRating = document.getElementById('selectedRating');
    
    starButtons.forEach(star => {
        star.classList.remove('text-yellow-400');
        star.classList.add('text-gray-300');
    });
    
    if (selectedRating) {
        selectedRating.value = '';
    }
    
    // Reset character count
    const charCount = document.getElementById('charCount');
    if (charCount) {
        charCount.textContent = '0/1000';
    }
    
    // Ensure form is visible
    const formSection = document.getElementById('reviewFormSection');
    if (formSection) {
        formSection.style.display = 'block';
    }
}

    function editReview(reviewId) {
        const reviewElement = document.querySelector(`.review-item[data-review-id="${reviewId}"]`);
        
        // Check if review element exists
        if (!reviewElement) {
            console.warn('Review element not found for ID:', reviewId);
            return;
        }

        const commentElement = reviewElement.querySelector('.review-comment');
        const stars = reviewElement.querySelectorAll('.rating-stars .fas.fa-star');
        
        // Check if required elements exist
        if (!commentElement || stars.length === 0) {
            console.warn('Required review elements not found');
            return;
        }

        const comment = commentElement.textContent;
        const rating = stars.length;

        // Populate form safely
        const commentTextarea = document.getElementById('comment');
        const selectedRating = document.getElementById('selectedRating');
        
        if (commentTextarea) {
            commentTextarea.value = comment;
        }
        
        if (selectedRating) {
            selectedRating.value = rating;
        }
        
        // Update stars safely
        const starButtons = document.querySelectorAll('.star-btn');
        if (starButtons.length > 0) {
            starButtons.forEach((star, index) => {
                if (index < rating) {
                    star.classList.remove('text-gray-300');
                    star.classList.add('text-yellow-400');
                } else {
                    star.classList.remove('text-yellow-400');
                    star.classList.add('text-gray-300');
                }
            });
        }

        // Update form action
        const form = document.getElementById('reviewForm');
        if (form) {
            form.action = "{{ route('reviews.update', ':id') }}".replace(':id', reviewId);
            
            // Add method spoofing for PUT
            let methodInput = form.querySelector('input[name="_method"]');
            if (!methodInput) {
                methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                form.appendChild(methodInput);
            }
            methodInput.value = 'PUT';
            
            // Update button text
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = 'Update Review';
            }

            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Store current review ID for future reference
        window.currentReviewId = reviewId;
    }

    function toggleHelpfulVote(button, reviewId) {
        // Implement helpful votes functionality here
        button.classList.toggle('text-indigo-600');
        button.classList.toggle('text-gray-500');
        
        const icon = button.querySelector('i');
        if (icon.classList.contains('far')) {
            icon.classList.remove('far');
            icon.classList.add('fas');
        } else {
            icon.classList.remove('fas');
            icon.classList.add('far');
        }
        
        // Show temporary feedback
        const originalText = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check"></i> <span>Thank you for your feedback!</span>';
        
        setTimeout(() => {
            button.innerHTML = originalText;
        }, 2000);
    }

    function showCustomToast(message, type = 'info') {
        console.log('Showing toast:', type, message);
        
        // Remove any existing toasts to prevent stacking
        const existingToasts = document.querySelectorAll('.custom-toast:not(#purchaseToast)');
        existingToasts.forEach(toast => {
            toast.remove();
        });

        const toast = document.createElement('div');
        const toastClass = type === 'success' ? 'toast-success' : 
                        type === 'error' ? 'toast-error' : 'toast-info';
        
        const toastIcon = type === 'success' ? 'fa-check-circle' : 
                        type === 'error' ? 'fa-exclamation-circle' : 'fa-info-circle';

        toast.className = `custom-toast ${toastClass}`;
        toast.style.cssText = `
            position: fixed;
            top: 100px;
            right: 20px;
            z-index: 10000;
            min-width: 300px;
            max-width: 400px;
            border-radius: 8px;
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
            animation: slideInRight 0.3s ease-out;
        `;
        
        toast.innerHTML = `
            <div class="flex items-start space-x-3 p-4">
                <i class="fas ${toastIcon} text-lg mt-0.5 flex-shrink-0"></i>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-sm leading-tight">${message}</p>
                </div>
                <button type="button" onclick="this.closest('.custom-toast').remove()" 
                        class="flex-shrink-0 ml-4 text-white hover:text-gray-200 transition-colors duration-200">
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>
        `;
        
        document.body.appendChild(toast);
        console.log('Toast added to DOM');
        
        // Force reflow to ensure animation works
        toast.offsetHeight;
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
                console.log('Toast auto-removed');
            }
        }, 5000);
        
        // Add click to dismiss
        toast.addEventListener('click', function(e) {
            if (e.target === this || e.target.closest('button')) {
                this.remove();
                console.log('Toast manually dismissed');
            }
        });
        
        return toast;
    }

    // Close modals when clicking outside
    document.getElementById('videoModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });

    document.getElementById('contentModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeContentModal();
        }
    });

    // Expand first module by default
    document.addEventListener('DOMContentLoaded', function() {
        const firstModule = document.querySelector('.module-content');
        if (firstModule) {
            const firstModuleId = firstModule.id.split('-')[1];
            setTimeout(() => toggleModule(firstModuleId), 500);
        }
        
        // Add fade-in animation to all cards
        const cards = document.querySelectorAll('.hover-lift');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.1}s`;
            card.classList.add('fade-in');
        });
    });

    // Smooth scroll to reviews section
    function scrollToReviews() {
        document.getElementById('reviews').scrollIntoView({ 
            behavior: 'smooth' 
        });
    }
</script>
<style>
        /* Enhanced Styles */
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .sticky-sidebar {
            position: sticky;
            top: 2rem;
        }
        
        .course-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .rating-stars {
            display: inline-flex;
            align-items: center;
        }
        
        .progress-ring {
            transform: rotate(-90deg);
        }
        
        .attachment-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        
        .video-thumbnail, .youtube-thumbnail {
            position: relative;
            cursor: pointer;
        }
        
        .video-thumbnail::after, .youtube-thumbnail::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0.7;
            transition: opacity 0.3s;
        }
        
        .video-thumbnail:hover::after, .youtube-thumbnail:hover::after {
            opacity: 0.9;
        }
        
        .purchase-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            color: white;
            opacity: 0;
            transition: opacity 0.3s;
            z-index: 10;
        }
        
        .purchase-overlay:hover {
            opacity: 1;
        }
        
        .module-content {
            transition: max-height 0.3s ease-out;
            overflow: hidden;
        }
        
        .collapsed {
            max-height: 0;
        }
        
        .expanded {
            max-height: 5000px;
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }

        /* Review System Styles */
        .review-fade-in {
            animation: reviewFadeIn 0.6s ease-in;
        }

        @keyframes reviewFadeIn {
            from { 
                opacity: 0; 
                transform: translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0); 
            }
        }

        .star-btn {
            transition: all 0.2s ease;
        }

        .star-btn:hover {
            transform: scale(1.2);
        }

        .helpful-btn {
            transition: all 0.2s ease;
        }

        .helpful-btn:hover {
            transform: translateY(-1px);
        }

        /* Review card hover effects */
        .review-card {
            transition: all 0.3s ease;
        }

        .review-card:hover {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        /* Progress bar animations */
        .rating-progress {
            transition: width 0.8s ease-in-out;
        }

        /* Verified student badge */
        .verified-badge {
            position: relative;
        }

        .verified-badge::before {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: #10B981;
            border-radius: 50%;
            border: 2px solid white;
        }

       
        .fixed-alert {
            position: fixed;
            top: 200px; 
            right: 20px;
            z-index: 100;
            animation: slideInRight 0.3s ease-out;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Toast Notification Styles */
    .custom-toast {
        position: fixed;
        top: 100px;
        right: 20px;
        z-index: 10000;
        min-width: 300px;
        max-width: 400px;
        border-radius: 8px;
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3);
        animation: slideInRight 0.3s ease-out;
    }

    .toast-success {
        background: #10B981;
        color: white;
        border: 1px solid #059669;
    }

    .toast-error {
        background: #EF4444;
        color: white;
        border: 1px solid #DC2626;
    }

    .toast-info {
        background: #3B82F6;
        color: white;
        border: 1px solid #2563EB;
    }

    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* Ensure toasts are above everything */
    .custom-toast {
        z-index: 10000 !important;
    }
        
    </style>
@endsection
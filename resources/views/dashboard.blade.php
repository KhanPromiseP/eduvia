@extends('layouts.app')

@section('content')
<body class="font-sans antialiased bg-[#FDFDFC] text-[#1b1b18] flex flex-col min-h-screen">

    <style>
        /* Custom CSS for enhanced responsiveness */
        :root {
            --hero-min-height: 500px;
            --hero-max-height: 800px;
        }
        
        .hero-swiper {
            min-height: var(--hero-min-height);
            max-height: var(--hero-max-height);
            height: 70vh; /* Use viewport height for better scaling */
        }
        
        /* Improved animations with reduced motion support */
        @media (prefers-reduced-motion: no-preference) {
            .animate-fade-in {
                opacity: 0;
                transform: translateY(20px);
                animation: fadeInUp 0.8s forwards;
            }
            .animate-fade-in.delay-300 { animation-delay: 0.3s; }
            .animate-fade-in.delay-600 { animation-delay: 0.6s; }
            .animate-fade-in.delay-900 { animation-delay: 0.9s; }

            @keyframes fadeInUp {
                to { opacity: 1; transform: translateY(0); }
            }
        }
        
        /* For users who prefer reduced motion */
        @media (prefers-reduced-motion: reduce) {
            .animate-fade-in {
                opacity: 1;
                transform: none;
                animation: none;
            }
        }
        
        .swiper-pagination-bullet-active {
            background: white !important;
            transform: scale(1.2);
        }
        
        /* Custom scrollbar for browsers that support it */
        .hero-swiper::-webkit-scrollbar {
            display: none;
        }
        
        /* Improved image loading */
        .lazyload,
        .lazyloading {
            opacity: 0;
            transition: opacity 0.3s;
        }
        .lazyloaded {
            opacity: 1;
        }
        
        /* Enhanced button hover effects */
        .btn-hover-effect {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Better mobile navigation */
      @media (max-width: 640px) {
        .swiper-button-next,
        .swiper-button-prev {
            width: 32px !important;
            height: 32px !important;
            font-size: 16px !important;
            margin-top: 340px !important;
            z-index: 50;
        }
            
            .hero-swiper {
                height: 80vh; /* More height on mobile for content */
            }
        }
        
        /* Fluid typography */
        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
        }
        
        .hero-subtitle {
            font-size: clamp(1rem, 2.5vw, 1.25rem);
        }
        
        /* Better image aspect ratios */
        .hero-image-container {
            aspect-ratio: 16 / 9;
        }
        
        @media (min-width: 1024px) {
            .hero-image-container {
                aspect-ratio: auto;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="relative w-full overflow-hidden bg-gradient-to-br from-gray-900 to-indigo-900 rounded-2xl">
        <!-- Swiper Container -->
        <div class="swiper hero-swiper w-full">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="flex flex-col lg:flex-row h-full w-full">
                        <!-- Text Content -->
                        <div class="w-full lg:w-1/2 flex flex-col justify-center px-4 sm:px-6 md:px-8 lg:px-12 xl:px-16 py-8 sm:py-10 lg:py-0 order-2 lg:order-1 bg-gradient-to-r from-indigo-900/90 via-purple-800/80 to-transparent">
                            <div class="max-w-lg mx-auto lg:mx-0 w-full">
                                <span class="inline-block px-3 py-1 sm:px-4 sm:py-1.5 bg-yellow-400/20 text-yellow-300 rounded-full text-xs sm:text-sm font-medium mb-3 sm:mb-4 animate-fade-in">
                                    🎓 Free Learning Access
                                </span>
                                <h1 class="hero-title font-bold text-white mb-4 sm:mb-6 leading-tight animate-fade-in">
                                    Access <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-300 to-yellow-500">Free Courses</span> Today
                                </h1>
                                <p class="hero-subtitle text-gray-200 mb-6 sm:mb-8 leading-relaxed animate-fade-in delay-300">
                                    Explore thousands of high-quality courses, develop in-demand skills, and advance your career with our free learning platform.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 animate-fade-in delay-600">
                                    <a href="#featured-courses" 
                                       class="px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 flex items-center justify-center btn-hover-effect">
                                        <span class="text-sm sm:text-base">Start Learning Free</span>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                    <a href="#how-it-works" 
                                       class="px-6 py-3 sm:px-8 sm:py-4 bg-white/10 text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 flex items-center justify-center btn-hover-effect text-sm sm:text-base">
                                        How It Works
                                    </a>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center mt-8 sm:mt-10 text-gray-300 animate-fade-in delay-900 gap-2 sm:gap-0">
                                    <div class="flex items-center sm:mr-6">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm sm:text-base">100% Free Access</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm sm:text-base">Certificate Available</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Content -->
                        <div class="w-full lg:w-1/2 hero-image-container order-1 lg:order-2 relative overflow-hidden">
                            <!-- Sideward gradient overlay on image -->
                            <div class="absolute inset-0 bg-gradient-to-l from-pink-900/50 via-indigo-900/50 to-transparent z-10"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/20 to-transparent z-10"></div>
                            <img src="./assets/hero-image-3.jpeg" 
                                 alt="Students learning together in a modern classroom"
                                 class="w-full h-full object-cover lazyload" loading="lazy">
                        </div>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="flex flex-col lg:flex-row h-full w-full">
                        <!-- Text Content -->
                        <div class="w-full lg:w-1/2 flex flex-col justify-center px-4 sm:px-6 md:px-8 lg:px-12 xl:px-16 py-8 sm:py-10 lg:py-0 order-2 lg:order-1 bg-gradient-to-r from-emerald-900/90 via-teal-800/80 to-transparent">
                            <div class="max-w-lg mx-auto lg:mx-0 w-full">
                                <span class="inline-block px-3 py-1 sm:px-4 sm:py-1.5 bg-green-400/20 text-green-300 rounded-full text-xs sm:text-sm font-medium mb-3 sm:mb-4 animate-fade-in">
                                    ⭐ Premium Experience
                                </span>
                                <h1 class="hero-title font-bold text-white mb-4 sm:mb-6 leading-tight animate-fade-in">
                                    Unlock <span class="text-transparent bg-clip-text bg-gradient-to-r from-green-300 to-emerald-400">Premium Courses</span>
                                </h1>
                                <p class="hero-subtitle text-gray-200 mb-6 sm:mb-8 leading-relaxed animate-fade-in delay-300">
                                    Access expert-led courses, advanced resources, and personalized learning paths to accelerate your career growth.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 animate-fade-in delay-600">
                                    <a href="#premium-courses" 
                                       class="px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-green-400 to-emerald-500 text-white font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 flex items-center justify-center btn-hover-effect">
                                        <span class="text-sm sm:text-base">Explore Premium</span>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                    <a href="#premium-benefits" 
                                       class="px-6 py-3 sm:px-8 sm:py-4 bg-white/10 text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 flex items-center justify-center btn-hover-effect text-sm sm:text-base">
                                        View Benefits
                                    </a>
                                </div>
                                <div class="grid grid-cols-1 xs:grid-cols-2 gap-3 sm:gap-4 mt-8 sm:mt-10 animate-fade-in delay-900">
                                    <div class="flex items-center text-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm sm:text-base">Expert Instructors</span>
                                    </div>
                                    <div class="flex items-center text-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm sm:text-base">Career Certificates</span>
                                    </div>
                                    <div class="flex items-center text-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm sm:text-base">Project Resources</span>
                                    </div>
                                    <div class="flex items-center text-gray-300">
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="text-sm sm:text-base">1-on-1 Support</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Image Content -->
                        <div class="w-full lg:w-1/2 hero-image-container order-1 lg:order-2 relative overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-l from-emerald-900/90 to-transparent z-10"></div>
                            <img src="./assets/hero-image-2.jpeg" 
                                 alt="Professional working on laptop with online course"
                                 class="w-full h-full object-cover lazyload" loading="lazy">
                        </div>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="flex flex-col lg:flex-row h-full w-full">
                        <!-- Text Content -->
                        <div class="w-full lg:w-1/2 flex flex-col justify-center px-4 sm:px-6 md:px-8 lg:px-12 xl:px-16 py-8 sm:py-10 lg:py-0 order-2 lg:order-1 bg-gradient-to-r from-blue-900/90 via-indigo-800/80 to-transparent">
                            <div class="max-w-lg mx-auto lg:mx-0 w-full">
                                <span class="inline-block px-3 py-1 sm:px-4 sm:py-1.5 bg-blue-400/20 text-blue-300 rounded-full text-xs sm:text-sm font-medium mb-3 sm:mb-4 animate-fade-in">
                                    👨‍🏫 World-Class Instructors
                                </span>
                                <h1 class="hero-title font-bold text-white mb-4 sm:mb-6 leading-tight animate-fade-in">
                                    Learn From <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-300 to-cyan-400">Industry Experts</span>
                                </h1>
                                <p class="hero-subtitle text-gray-200 mb-6 sm:mb-8 leading-relaxed animate-fade-in delay-300">
                                    Connect with top educators, industry leaders, and mentors who are passionate about sharing their knowledge and expertise.
                                </p>
                                <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 animate-fade-in delay-600">
                                    <a href="#instructors" 
                                       class="px-6 py-3 sm:px-8 sm:py-4 bg-gradient-to-r from-blue-400 to-cyan-500 text-white font-bold rounded-xl shadow-2xl hover:shadow-3xl transition-all duration-300 transform hover:-translate-y-1 hover:scale-105 flex items-center justify-center btn-hover-effect">
                                        <span class="text-sm sm:text-base">Meet Instructors</span>
                                        <svg class="w-4 h-4 sm:w-5 sm:h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                        </svg>
                                    </a>
                                    <a href="#become-instructor" 
                                       class="px-6 py-3 sm:px-8 sm:py-4 bg-white/10 text-white font-bold rounded-xl border border-white/20 hover:bg-white/20 transition-all duration-300 flex items-center justify-center btn-hover-effect text-sm sm:text-base">
                                        Teach With Us
                                    </a>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center mt-8 sm:mt-10 text-gray-300 animate-fade-in delay-900 gap-2 sm:gap-0">
                                    <div class="flex -space-x-2 mr-0 sm:mr-4 mb-2 sm:mb-0">
                                        <img src="/assets/instructor-1.jpg" alt="Instructor Sarah Johnson" 
                                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover border-2 border-gray-900">
                                        <img src="/assets/instructor-2.jpeg" alt="Instructor Michael Chen" 
                                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover border-2 border-gray-900">
                                        <img src="/assets/instructor-3.jpeg" alt="Instructor Maria Rodriguez" 
                                            class="w-6 h-6 sm:w-8 sm:h-8 rounded-full object-cover border-2 border-gray-900">
                                    </div>
                                    <span class="text-sm sm:text-base">Join 5,000+ expert instructors worldwide</span>
                                </div>
                            </div>
                        </div>
                        
                         <!-- Image Content -->
                        <div class="w-full lg:w-1/2 hero-image-container order-1 lg:order-2 relative overflow-hidden">
                            <!-- Sideward gradient overlay on image -->
                            <div class="absolute inset-0 bg-gradient-to-l from-blue-900/50 via-indigo-900/50 to-transparent z-10"></div>
                            <div class="absolute inset-0 bg-gradient-to-t from-gray-900/20 to-transparent z-10"></div>
                            <img src="./assets/hero-image-1.jpeg" 
                                 alt="Instructor teaching online course"
                                 class="w-full h-full object-cover lazyload" loading="lazy">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation & Pagination -->
            <div class="swiper-button-next text-white bg-black/20 hover:bg-black/40 backdrop-blur-sm rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center transition-all duration-300"></div>
            <div class="swiper-button-prev text-white bg-black/20 hover:bg-black/40 backdrop-blur-sm rounded-full w-10 h-10 sm:w-12 sm:h-12 flex items-center justify-center transition-all duration-300"></div>
            <div class="swiper-pagination !bottom-4 sm:!bottom-6"></div>
        </div>
    </section>

    <!-- Swiper JS Configuration -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
          
            const heroSwiper = new Swiper('.hero-swiper', {
                loop: true,
                autoplay: { 
                    delay: 8000, 
                    disableOnInteraction: false,
                },
                speed: 1000,
                effect: 'fade',
                fadeEffect: { crossFade: true },
                pagination: { 
                    el: '.swiper-pagination', 
                    clickable: true,
                    renderBullet: function (index, className) {
                        return '<span class="' + className + ' !w-2 !h-2 sm:!w-3 sm:!h-3 !bg-white/50 !opacity-100 hover:!bg-white !mx-1 transition-all duration-300"></span>';
                    },
                },
                navigation: { 
                    nextEl: '.swiper-button-next', 
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 0
                    },
                    640: {
                        slidesPerView: 1,
                        spaceBetween: 0
                    },
                    1024: {
                        slidesPerView: 1,
                        spaceBetween: 0
                    }
                }
            });
            
            // Pause autoplay on hover
            const swiperContainer = document.querySelector('.hero-swiper');
            swiperContainer.addEventListener('mouseenter', function() {
                heroSwiper.autoplay.stop();
            });
            swiperContainer.addEventListener('mouseleave', function() {
                heroSwiper.autoplay.start();
            });
            
            // Touch gesture improvements for mobile
            let startX = 0;
            let endX = 0;
            
            swiperContainer.addEventListener('touchstart', (e) => {
                startX = e.changedTouches[0].screenX;
            });
            
            swiperContainer.addEventListener('touchend', (e) => {
                endX = e.changedTouches[0].screenX;
                handleSwipe();
            });
            
            function handleSwipe() {
                if (startX - endX > 50) {
                    // Swipe left - next slide
                    heroSwiper.slideNext();
                }
                
                if (endX - startX > 50) {
                    // Swipe right - previous slide
                    heroSwiper.slidePrev();
                }
            }
        });
    </script>

   <!-- Featured Courses Carousel -->
<section id="featured-courses" 
    class="py-10 px-4 sm:px-6 lg:px-8 bg-white"
    x-data="{ 
        activeCategory: 'All', 
        setCategory(cat) { this.activeCategory = cat; },
    }"
>
    <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16 px-4 sm:px-6 lg:px-0">
    <!-- Section Heading -->
    <h2 class="text-3xl sm:text-4xl md:text-5xl font-extrabold bg-clip-text text-transparent 
               bg-gradient-to-r from-indigo-400 via-purple-500 to-pink-500 
               animate-fade-in">
        Featured Courses
    </h2>
    <!-- Subtitle -->
    <p class="mt-4 text-lg sm:text-xl text-gray-500 max-w-2xl mx-auto animate-fade-in delay-300">
        Handpicked courses to kickstart your learning journey on Eduvia
    </p>
    <!-- Decorative underline -->
    <div class="mt-6 flex justify-center">
        <span class="block w-20 h-1 rounded-full bg-gradient-to-r from-indigo-400 via-purple-500 to-pink-500 animate-pulse"></span>
    </div>
</div>

<!-- Tailwind Fade-In Animation -->
<style>
    @keyframes fadeIn {
        0% { opacity: 0; transform: translateY(20px); }
        100% { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.8s forwards;
    }
    .animate-fade-in.delay-300 { animation-delay: 0.3s; }
</style>

<!-- Category Filter Carousel -->
<div class="relative py-4 mb-12">
    <!-- Left blur -->
    <!-- Left blur with indigo-pink gradient -->
<div class="absolute left-0 top-0 h-full w-12 rounded-xl lg:w-20 
            bg-gradient-to-r from-indigo-500/40 via-pink-400/30 to-transparent 
            pointer-events-none z-10">
</div>

<!-- Right blur with indigo-pink gradient -->
<div class="absolute right-0 top-0 h-full rounded-xl w-12 lg:w-20 
            bg-gradient-to-l from-indigo-500/40 via-pink-400/30 to-transparent 
            pointer-events-none z-10">
</div>

    <div class="overflow-x-auto scrollbar-none py-2">
        <div class="flex gap-4 min-w-max px-4 sm:px-6 lg:px-12">
            <!-- All Courses Button -->
            <button 
                @click="setCategory('All')" 
                :class="activeCategory === 'All' 
                    ? 'bg-blue-600 text-white shadow-lg scale-105' 
                    : 'border-2 border-gray-300 text-gray-700 hover:border-blue-600 hover:text-blue-600'"
                class="px-6 py-2 rounded-full font-semibold transition-all duration-300 flex-shrink-0 whitespace-nowrap">
                All Courses
            </button>

            <!-- Dynamic Categories -->
            @foreach($categories->take(6) as $category)
                <button 
                    @click="setCategory('{{ $category->name }}')" 
                    :class="activeCategory === '{{ $category->name }}' 
                        ? 'bg-blue-600 text-white shadow-lg scale-105' 
                        : 'border-2 border-gray-300 text-gray-700 hover:border-blue-600 hover:text-blue-600'"
                    class="px-6 py-2 rounded-full font-semibold transition-all duration-300 flex-shrink-0 whitespace-nowrap">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>
</div>

<!-- Tailwind Custom Scrollbar & Smooth Scrolling -->
<style>
.scrollbar-none::-webkit-scrollbar { display: none; }
.scrollbar-none { -ms-overflow-style: none; scrollbar-width: none; }
.overflow-x-auto { scroll-behavior: smooth; }
</style>

<!-- Courses Display with infinite opposing scroll lines -->
<div class="relative mb-16">
    <!-- Left gradient overlay -->
    <div class="absolute left-0 top-0 h-full w-12 lg:w-20 bg-gradient-to-r from-white/50 via-white/10 to-transparent pointer-events-none z-10"></div>
    <!-- Right gradient overlay -->
    <div class="absolute right-0 top-0 h-full w-12 lg:w-20 bg-gradient-to-l from-white/50 via-white/10 to-transparent pointer-events-none z-10"></div>

    <!-- First row infinite scroll left -->
    <div class="relative overflow-hidden">
        <div class="flex gap-8 animate-scroll-left infinite-row">
            @foreach($courses as $course)
                @if($course->is_published)
                    <div 
                        x-show="activeCategory === 'All' || activeCategory === '{{ $course->category->name ?? 'Uncategorized' }}'"
                        class="min-w-[250px] flex-shrink-0"
                        x-transition
                    >
                        @include('components.course-card', ['course' => $course, 'featured' => true])
                    </div>
                @endif
            @endforeach
            <!-- Duplicate for infinite effect -->
            @foreach($courses as $course)
                @if($course->is_published)
                    <div 
                        x-show="activeCategory === 'All' || activeCategory === '{{ $course->category->name ?? 'Uncategorized' }}'"
                        class="min-w-[250px] flex-shrink-0"
                        x-transition
                    >
                        @include('components.course-card', ['course' => $course, 'featured' => true])
                    </div>
                @endif
            @endforeach
        </div>
    </div>

    <!-- Second row infinite scroll right -->
    <div class="relative overflow-hidden mt-6">
        <div class="flex gap-8 animate-scroll-right infinite-row">
            @foreach($courses as $course)
                @if($course->is_published)
                    <div 
                        x-show="activeCategory === 'All' || activeCategory === '{{ $course->category->name ?? 'Uncategorized' }}'"
                        class="min-w-[250px] flex-shrink-0"
                        x-transition
                    >
                        @include('components.course-card', ['course' => $course, 'featured' => true])
                    </div>
                @endif
            @endforeach
            <!-- Duplicate for infinite effect -->
            @foreach($courses as $course)
                @if($course->is_published)
                    <div 
                        x-show="activeCategory === 'All' || activeCategory === '{{ $course->category->name ?? 'Uncategorized' }}'"
                        class="max-w-[150px] flex-shrink-0"
                        x-transition
                    >
                        @include('components.course-card', ['course' => $course, 'featured' => true])
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>

<style>
/* Infinite scroll keyframes */
@keyframes scroll-left {
    0% { transform: translateX(0); }
    100% { transform: translateX(-50%); }
}
@keyframes scroll-right {
    0% { transform: translateX(-50%); }
    100% { transform: translateX(0); }
}

/* Infinite scrolling rows */
.infinite-row {
    display: flex;
    gap: 2rem;
    width: max-content;
}
.animate-scroll-left {
    animation: scroll-left 60s linear infinite;
}
.animate-scroll-right {
    animation: scroll-right 60s linear infinite;
}

/* Scrollable overflow for manual scroll */
.relative.overflow-hidden > .infinite-row {
    overflow-x: auto;
    scroll-behavior: smooth;
}

/* Hide native scrollbars */
.infinite-row::-webkit-scrollbar {
    display: none;
}
.infinite-row {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

/* Responsive padding for large screens */
@media (min-width: 1024px) {
    .infinite-row {
        padding-left: 50px;
        padding-right: 50px;
    }
}
</style>




    <!-- Learning Paths Section -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Structured Learning Paths</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Follow curated paths to achieve your Destiny goals</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-line text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Web-Dev mean stack</h3>
                    <p class="text-gray-600 mb-6">Learn strategies to build and grow your wealth systematically</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">5 courses • 20 hours</span>
                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Explore →</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-usd text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Graphic Designing</h3>
                    <p class="text-gray-600 mb-6">Master stock market, real estate, and alternative investments</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">8 courses • 35 hours</span>
                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Explore →</a>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-8 shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-piggy-bank text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Cyber Security Center</h3>
                    <p class="text-gray-600 mb-6">Achieve financial independence through smart planning</p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">6 courses • 25 hours</span>
                        <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Explore →</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Courses Grid -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-12">
                <div>
                    <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Most Popular Courses</h2>
                    <p class="text-lg text-gray-600">Courses loved by our community of learners</p>
                </div>
                <div class="flex gap-4 mt-4 md:mt-0">
                    <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option>Sort by: Popularity</option>
                        <option>Sort by: Newest</option>
                        <option>Sort by: Price</option>
                        <option>Sort by: Rating</option>
                    </select>
                    <button class="px-4 py-2 border border-gray-300 rounded-lg hover:bg-gray-50">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($courses as $course)
                    @if($course->is_published)
                        @include('components.course-card', ['course' => $course, 'popular' => true])
                    @endif
                @endforeach
            </div>

            <div class="text-center mt-12">
                <a href="{{ route('courses.index') }}" class="inline-flex items-center px-8 py-3 bg-blue-600 text-white font-semibold rounded-full hover:bg-blue-700 transition-all duration-300 transform hover:-translate-y-1">
                    View All Courses
                    <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Why Choose Us Section -->
    <section class="py-20 bg-gradient-to-r from-blue-900 to-purple-900 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold mb-4">Why Choose Our Platform?</h2>
                <p class="text-xl text-blue-200 max-w-2xl mx-auto">Experience learning that transforms your future</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-graduation-cap text-3xl text-blue-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Expert Instructors</h3>
                    <p class="text-blue-200">Learn from industry professionals with years of experience</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-certificate text-3xl text-green-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Certified Content</h3>
                    <p class="text-blue-200">Industry-recognized certifications and accreditations</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-users text-3xl text-purple-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Community Support</h3>
                    <p class="text-blue-200">Join a thriving community of like-minded learners</p>
                </div>

                <div class="text-center">
                    <div class="w-20 h-20 bg-white/10 rounded-2xl flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-mobile-alt text-3xl text-yellow-300"></i>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Mobile Learning</h3>
                    <p class="text-blue-200">Learn anywhere, anytime on any device</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Carousel -->
    <section class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Success Stories</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Hear from our students who have transformed their life on Eduvia</p>
            </div>

            <div class="swiper testimonials-swiper">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                    ED
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-xl font-bold text-gray-800">Emily Davis</h4>
                                    <p class="text-blue-600">Organization Manager</p>
                                    <div class="flex text-yellow-400 mt-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-lg">"Eduvia has completely change everything about e-learning. I have great experties in management thanks to the unbeatable teachings I took on Eduvia!"</p>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-green-500 to-teal-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                    MB
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-xl font-bold text-gray-800">Michael Brown</h4>
                                    <p class="text-blue-600">Entrepreneur</p>
                                    <div class="flex text-yellow-400 mt-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-lg">"The financial planning course gave me the confidence to start my own business. I'm now generating six figures in revenue! All these thanks to Eduvia an it great team of instructors."</p>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="bg-white p-8 rounded-2xl shadow-lg">
                            <div class="flex items-center mb-6">
                                <div class="w-16 h-16 bg-gradient-to-r from-purple-500 to-pink-500 rounded-full flex items-center justify-center text-white font-bold text-xl">
                                    SJ
                                </div>
                                <div class="ml-6">
                                    <h4 class="text-xl font-bold text-gray-800">Sarah Johnson</h4>
                                    <p class="text-blue-600">Computer Engineer</p>
                                    <div class="flex text-yellow-400 mt-1">
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                        <i class="fas fa-star"></i>
                                    </div>
                                </div>
                            </div>
                            <p class="text-gray-600 text-lg">"Now, I am supper proud of my engineering skills from planning to delivery thanks to Eduvia powerfull complete detailed webdevelopment mastery course!"</p>
                        </div>
                    </div>
                </div>
                <div class="swiper-pagination"></div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-gradient-to-r from-blue-600 to-purple-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">Ready to Transform your Future?</h2>
            <p class="text-xl text-blue-100 mb-10">Join thousands of students who have already started their journey to a bright furture on Eduvia</p>
            
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('courses.index') }}" class="px-8 py-4 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1">
                    Browse All Courses
                </a>
                <a href="#services" class="px-8 py-4 border-2 border-white text-white font-bold rounded-full hover:bg-white hover:text-blue-600 transition-all duration-300">
                    Get Personalized Help
                </a>
            </div>
        </div>
    </section>

    <!-- Blog Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Latest Insights</h2>
                <p class="text-lg text-gray-600">Stay updated with the latest Educational trends and strategies</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-r from-blue-500 to-purple-500 relative">
                        <span class="absolute top-4 left-4 px-3 py-1 bg-white text-blue-600 text-sm font-semibold rounded-full">Programming</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">2026 Programming Jobs</h3>
                        <p class="text-gray-600 mb-4">Discover the most effective strategies to acheive a great Job as a Programmer in this coming year</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Oct 15, 2025</span>
                            <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-r from-green-500 to-teal-500 relative">
                        <span class="absolute top-4 left-4 px-3 py-1 bg-white text-green-600 text-sm font-semibold rounded-full">Investing</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">AI in Investment Analysis</h3>
                        <p class="text-gray-600 mb-4">How artificial intelligence is revolutionizing investment decision making</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Jan 12, 2025</span>
                            <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="h-48 bg-gradient-to-r from-purple-500 to-pink-500 relative">
                        <span class="absolute top-4 left-4 px-3 py-1 bg-white text-purple-600 text-sm font-semibold rounded-full">Budgeting</span>
                    </div>
                    <div class="p-6">
                        <h3 class="text-xl font-bold text-gray-800 mb-3">Zero-Based Budgeting Mastery</h3>
                        <p class="text-gray-600 mb-4">Learn how to implement zero-based budgeting for maximum financial control</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Sept 8, 2025</span>
                            <a href="#" class="text-blue-600 font-semibold hover:text-blue-800">Read More →</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="py-20 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 mb-4">Personalized Learning Services</h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Get one-on-one guidance from our industry expert instructors</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-chart-pie text-blue-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Wealth Management</h3>
                    <p class="text-gray-600 mb-6">Comprehensive wealth management strategies tailored to your goals</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Portfolio optimization
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Risk assessment
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Tax planning
                        </li>
                    </ul>
                    <a href="/contact" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-green-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-hand-holding-usd text-green-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Investment Advisory</h3>
                    <p class="text-gray-600 mb-6">Expert guidance on building and managing your investment portfolio</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Stock selection
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Market analysis
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Performance tracking
                        </li>
                    </ul>
                    <a href="/contact" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>

                <div class="bg-white p-8 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-2">
                    <div class="w-16 h-16 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                        <i class="fas fa-piggy-bank text-purple-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4">Retirement Planning</h3>
                    <p class="text-gray-600 mb-6">Secure your financial future with comprehensive retirement planning</p>
                    <ul class="space-y-2 mb-6">
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Retirement savings
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Pension planning
                        </li>
                        <li class="flex items-center text-gray-600">
                            <i class="fas fa-check-circle text-green-500 mr-2"></i>
                            Estate planning
                        </li>
                    </ul>
                    <a href="/contact" class="inline-flex items-center text-blue-600 font-semibold hover:text-blue-800">
                        Get Started
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section class="py-20 bg-blue-600 text-white">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl sm:text-4xl font-bold mb-6">Stay Updated with Financial Insights</h2>
            <p class="text-xl text-blue-100 mb-8">Get weekly tips and strategies delivered to your inbox</p>
            
            <form class="max-w-md mx-auto">
                <div class="flex flex-col sm:flex-row gap-4">
                    <input type="email" placeholder="Enter your email" class="flex-1 px-6 py-3 rounded-full text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-300">
                    <button type="submit" class="px-8 py-3 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition-all duration-300">
                        Subscribe
                    </button>
                </div>
            </form>
            <p class="text-sm text-blue-200 mt-4">No spam. Unsubscribe at any time.</p>
        </div>
    </section>

    <!-- JavaScript for animations and carousels -->
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Swiper carousels
            const featuredSwiper = new Swiper('.featured-courses-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
                breakpoints: {
                    640: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });

            const testimonialsSwiper = new Swiper('.testimonials-swiper', {
                slidesPerView: 1,
                spaceBetween: 30,
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                breakpoints: {
                    768: { slidesPerView: 2 },
                    1024: { slidesPerView: 3 }
                }
            });

            // Intersection Observer for animations
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-fade-in-up');
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.animate-on-scroll').forEach(el => {
                observer.observe(el);
            });
        });
    </script>

    <style>
        .animate-pulse-slow {
            animation: pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animate-pulse-slower {
            animation: pulse 6s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .swiper-button-next, .swiper-button-prev {
            color: #3b82f6;
            background: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .swiper-button-next:after, .swiper-button-prev:after {
            font-size: 20px;
        }
    </style>
</body>
@endsection
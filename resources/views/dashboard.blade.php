@extends('layouts.app')

@section('content')
   <body class="font-sans antialiased bg-[#FDFDFC] text-[#1b1b18] flex flex-col min-h-screen">
   

    <!-- Hero Section -->
    <main class="flex-1 flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#FDFDFC] to-gray-100">
        <div class="max-w-5xl mx-auto text-center" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 300)" :class="{ 'opacity-0 translate-y-10': !visible, 'opacity-100 translate-y-0': visible }" class="transition-all duration-700 ease-out">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-6">
                Achieve Financial Excellence with Our Digital Products & Expert Services
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
                Explore courses on wealth building, read our blog for actionable financial tips, and contact us for personalized services to elevate your success.
            </p>
            
        </div>
    </main>

    <!-- Digital Products Section -->
   <section id="products" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
    <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Boost Your Business Knowledge</h2>
     {{-- Courses Grid --}}
    <div id="coursesGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($courses as $course)
        <div class="course-card border rounded-lg overflow-hidden hover:shadow-xl transition bg-white" 
             data-level="{{ $course->level }}" data-price="{{ $course->price == 0 ? 'free' : 'paid' }}">

            <div class="relative h-48 bg-gray-200">
                @if($course->image)
                    <img src="{{ asset('storage/' . $course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center bg-indigo-100">
                        <i class="fas fa-book text-4xl text-indigo-600"></i>
                    </div>
                @endif

                {{-- Level Badge --}}
                <span class="absolute top-2 left-2 bg-indigo-600 text-white px-3 py-1 rounded-full text-sm font-semibold">
                    @if($course->level == 1) Beginner
                    @elseif($course->level == 2) Intermediate
                    @else Advanced
                    @endif
                </span>

                {{-- Already Purchased Badge --}}
                @if(auth()->check() && auth()->user()->hasPurchased($course))
                    <span class="absolute top-2 right-2 bg-green-500 text-white px-2 py-1 rounded-full text-xs font-semibold">
                        <i class="fas fa-check-circle mr-1"></i> Enrolled
                    </span>
                @endif
            </div>

            {{-- Course Content --}}
            <div class="p-4 flex flex-col justify-between h-56">
                <div>
                    <h3 class="font-bold text-xl mb-2 text-gray-800">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-3">{{ Str::limit($course->description, 120) }}</p>
                </div>

                {{-- Price + Buttons --}}
                <div class="flex justify-between items-center mt-auto gap-2">
                    <span class="text-indigo-600 font-bold text-lg">${{ number_format($course->price, 2) }}</span>
                    <div class="flex gap-2">
                        <a href="{{ route('courses.show', $course) }}" class="bg-gray-800 text-white px-3 py-2 rounded-lg font-medium hover:bg-gray-900 transition">
                            View Details
                        </a>
                        @if(auth()->check() && auth()->user()->hasPurchased($course))
                                {{-- Show Access Course button for enrolled users --}}
                                <a href="{{ route('userdashboard', ['course' => $course->id]) }}" class="bg-green-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-green-700 transition flex items-center">
                                    <i class="fas fa-check-circle mr-2"></i> Access
                                </a>
                            @else
                                {{-- Show Enroll Now button for non-enrolled users --}}
                                <form action="{{ route('payment.initiate', $course) }}" method="POST" class="flex-shrink-0">
                                    @csrf
                                    <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-lg font-medium hover:bg-indigo-700 transition">
                                        Enroll Now
                                    </button>
                                </form>
                            @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- View More Button -->
    <div class="flex justify-center mt-8">
        <a href="{{ route('courses.index') }}" 
           class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-full shadow-lg transition-colors duration-300">
            View More Courses
        </a>
    </div>
</section>


    <!-- Blog Teaser Section -->
<section id="blog" class="py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
    <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-6">
            Insights on Financial Excellence
        </h2>
        <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">
            Read our blog for tips on wealth building, investment strategies, and achieving financial success.
        </p>

        <!-- Blog Teasers -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            
            <!-- Blog Card 1 -->
            <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-blue-100 text-blue-600 text-xs font-semibold rounded-full mb-3">
                        Wealth
                    </span>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        2025 Wealth Building Strategies
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Discover strategies to grow your finances this year.
                    </p>
                </div>
            </div>

            <!-- Blog Card 2 -->
            <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-green-100 text-green-600 text-xs font-semibold rounded-full mb-3">
                        Investing
                    </span>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        Beginner's Guide to Smart Investing
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Step-by-step tips for starting your investment journey.
                    </p>
                </div>
            </div>

            <!-- Blog Card 3 -->
            <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 flex flex-col justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-purple-100 text-purple-600 text-xs font-semibold rounded-full mb-3">
                        Budgeting
                    </span>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">
                        Budgeting Secrets for Financial Freedom
                    </h3>
                    <p class="text-gray-600 mb-4">
                        Learn how to manage your money effectively.
                    </p>
                </div>
            </div>

        </div>

        <!-- View More Button -->
        <div class="flex justify-center mt-12">
            <a href="{{ route('blog.index') }}" 
               class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-full shadow-lg transition-colors duration-300">
                Explore Our Blogs
            </a>
        </div>
    </div>
</section>



        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Our Financial Services</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Get personalized advice on wealth management, investments, and financial planning. Contact us to get started.</p>
             <div class="flex items-center justify-center">
                <a href="/contact" 
                class="flex items-center justify-center px-8 py-3 bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-full shadow-lg transition-colors duration-300">
                    Contact Us
                </a>
          

        </div>
  

    <!-- Testimonials Section -->
    <section class="py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">What Our Customers Say</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"The financial courses transformed my approach to wealth building!"</p>
                    <div class="flex items-center">
                       
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Emily Davis</h4>
                            <p class="text-gray-500 text-sm">Investor</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"Excellent blog posts on investment strategies—highly recommend!"</p>
                    <div class="flex items-center">
                       
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Michael Brown</h4>
                            <p class="text-gray-500 text-sm">Entrepreneur</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"Personalized services helped me achieve financial goals faster."</p>
                    <div class="flex items-center">
                     
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Sarah Johnson</h4>
                            <p class="text-gray-500 text-sm">Client</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

</body>
@endsection

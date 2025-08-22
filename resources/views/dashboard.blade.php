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
    <!-- Product Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-0">
        @foreach($courses->shuffle()->take(3) as $course)
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col">
                
                <!-- Image -->
                @if($course->image)
                    <div class="overflow-hidden">
                        <img src="{{ asset('storage/'.$course->image) }}" 
                             alt="{{ $course->title }}" 
                             class="w-full h-48 object-cover transform transition-transform duration-300 hover:scale-105">
                    </div>
                @else
                    <div class="w-full h-48 bg-gray-200 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" 
                             viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                @endif

                <!-- Course Info -->
                <div class="p-4 flex flex-col flex-grow">
                    <h3 class="font-bold text-lg mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-3 line-clamp-2">
                        {{ Str::limit($course->description, 100) }}
                    </p>

                    <div class="flex justify-between items-center mt-auto">
                        <span class="text-indigo-600 font-bold">${{ number_format($course->price, 2) }}</span>
                        <a href="{{ route('courses.show', $course) }}" 
                           class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                            View Details
                        </a>
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
                        <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/48" alt="User">
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Emily Davis</h4>
                            <p class="text-gray-500 text-sm">Investor</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"Excellent blog posts on investment strategies—highly recommend!"</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/48" alt="User">
                        <div class="ml-4">
                            <h4 class="text-gray-800 font-semibold">Michael Brown</h4>
                            <p class="text-gray-500 text-sm">Entrepreneur</p>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <p class="text-gray-600 mb-4">"Personalized services helped me achieve financial goals faster."</p>
                    <div class="flex items-center">
                        <img class="w-12 h-12 rounded-full" src="https://via.placeholder.com/48" alt="User">
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

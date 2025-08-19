<x-app-layout>
   <body class="font-sans antialiased bg-[#FDFDFC] text-[#1b1b18] flex flex-col min-h-screen">
   

    <!-- Hero Section -->
    <main class="flex-1 flex items-center justify-center py-24 px-4 sm:px-6 lg:px-8 bg-gradient-to-b from-[#FDFDFC] to-gray-100">
        <div class="max-w-5xl mx-auto text-center" x-data="{ visible: false }" x-init="setTimeout(() => visible = true, 300)" :class="{ 'opacity-0 translate-y-10': !visible, 'opacity-100 translate-y-0': visible }" class="transition-all duration-700 ease-out">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-bold text-gray-800 leading-tight mb-6">
                Achieve Financial Excellence with Our Digital Products & Expert Services
            </h1>
            <p class="text-lg sm:text-xl text-gray-600 mb-10 max-w-3xl mx-auto">
                Explore courses and eBooks on wealth building, read our blog for actionable financial tips, and contact us for personalized services to elevate your success.
            </p>
            
        </div>
    </main>

    <!-- Digital Products Section -->
    <section id="products" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
        <!-- Product Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 px-4 sm:px-0">
    @foreach($products->shuffle()->take(3) as $product)
    <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-xl transition-shadow duration-300 flex flex-col mx-2 sm:mx-0">

        <!-- Image -->
        @if($product->thumbnail)
            <div class="overflow-hidden">
                <img src="{{ asset('storage/'.$product->thumbnail) }}" 
                     alt="{{ $product->title }}" 
                     class="w-full h-48 object-cover transform transition-transform duration-300 hover:scale-105">
            </div>
        @else
            <div class="w-full h-48 bg-gray-200 flex items-center justify-center rounded-xl">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" 
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
            </div>
        @endif

        <!-- Product Info -->
        <div class="p-5 flex flex-col flex-grow">
            <h3 class="text-lg font-semibold text-gray-900">{{ $product->title }}</h3>
            <p class="mt-2 text-sm text-gray-500 line-clamp-3 flex-grow">{{ $product->description }}</p>
            <div class="mt-4 flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-2 sm:space-y-0">
                <span class="text-lg font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                <div class="flex space-x-2">
                    <a href="{{ route('products.show', $product) }}" class="bg-gray-200 text-gray-800 px-4 py-2 rounded-md hover:bg-gray-300 transition-colors font-medium">
                        View
                    </a>
                    <a href="{{ route('products.show', $product) }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700 transition-colors font-medium">
                        Purchase
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- View More Button -->
<div class="flex justify-center mt-8">
    <a href="{{ route('products.index') }}" 
       class="px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white text-lg font-semibold rounded-full shadow-lg transition-colors duration-300">
        View More Products
    </a>
</div>

    </section>

    <!-- Blog Teaser Section -->
    <section id="blog" class="py-24 px-4 sm:px-6 lg:px-8 bg-gray-50">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Insights on Financial Excellence</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Read our blog for tips on wealth building, investment strategies, and achieving financial success.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Blog Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Wealth+Tips" alt="Blog Post">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">5 Ways to Build Wealth in 2025</h3>
                        <p class="text-gray-600 mb-4">Discover strategies to grow your finances this year.</p>
                       
                    </div>
                </div>
                <!-- Blog Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Investment+Guide" alt="Blog Post">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Beginner's Guide to Smart Investing</h3>
                        <p class="text-gray-600 mb-4">Step-by-step tips for starting your investment journey.</p>
                        
                    </div>
                </div>
                <!-- Blog Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Budgeting+Secrets" alt="Blog Post">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Budgeting Secrets for Financial Freedom</h3>
                        <p class="text-gray-600 mb-4">Learn how to manage your money effectively.</p>
                        
                    </div>
                </div>
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
</x-app-layout> 

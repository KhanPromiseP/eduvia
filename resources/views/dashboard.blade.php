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
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Our Digital Products for Financial Mastery</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Discover courses and eBooks designed to help you excel financially, from investment strategies to wealth building fundamentals.</p>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Product Card 1 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Wealth+Building+Course" alt="Product">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Wealth Building Course</h3>
                        <p class="text-gray-600 mb-4">Learn proven strategies to build and grow your wealth in 2025.</p>
                        <p class="text-indigo-600 font-bold mb-4">$49.99</p>
                       
                    </div>
                </div>
                <!-- Product Card 2 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Investment+eBook" alt="Product">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Investment Essentials eBook</h3>
                        <p class="text-gray-600 mb-4">Expert tips on investing for long-term financial success.</p>
                        <p class="text-indigo-600 font-bold mb-4">$19.99</p>
                        
                    </div>
                </div>
                <!-- Product Card 3 -->
                <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow duration-300 overflow-hidden">
                    <img class="w-full h-48 object-cover rounded-t-2xl" src="https://via.placeholder.com/400x200?text=Budgeting+Masterclass" alt="Product">
                    <div class="p-4">
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">Budgeting Masterclass</h3>
                        <p class="text-gray-600 mb-4">Master budgeting to achieve financial freedom.</p>
                        <p class="text-indigo-600 font-bold mb-4">$29.99</p>
                        
                    </div>
                </div>
            </div>
            
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

    <!-- Services Section with Contact Form -->
    <section id="services" class="py-24 px-4 sm:px-6 lg:px-8 bg-[#FDFDFC]">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-gray-800 text-center mb-16">Our Financial Services</h2>
            <p class="text-lg text-gray-600 text-center mb-12 max-w-3xl mx-auto">Get personalized advice on wealth management, investments, and financial planning. Contact us to get started.</p>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Personalized Financial Consulting</h3>
                    <p class="text-gray-600 mb-6">Tailored strategies to help you achieve financial goals.</p>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Investment Advisory</h3>
                    <p class="text-gray-600 mb-6">Expert guidance on smart investments for growth.</p>
                    <h3 class="text-2xl font-semibold text-gray-800 mb-4">Wealth Building Workshops</h3>
                    <p class="text-gray-600">Interactive sessions for financial literacy.</p>
                </div>
                <div class="bg-white p-8 rounded-2xl shadow-lg">
                    <h3 class="text-2xl font-semibold text-gray-800 mb-6">Contact Us for Services</h3>
                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                            <input type="text" id="name" name="name" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                            <input type="email" id="email" name="email" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors">
                        </div>
                        <div>
                            <label for="message" class="block text-gray-700 font-medium mb-2">Message</label>
                            <textarea id="message" name="message" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:border-indigo-600 focus:outline-none transition-colors"></textarea>
                        </div>
                        <button type="submit" class="w-full px-6 py-3 bg-indigo-600 text-white font-semibold rounded-full hover:bg-indigo-700 transition-colors shadow-md hover:shadow-lg">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

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

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-lg sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-8">

                        <!-- Left Column: Image & Download -->
                        <div class="md:w-1/3 flex flex-col items-center">
                            @if($product->thumbnail)
                                <img src="{{ asset('storage/'.$product->thumbnail) }}" 
                                     alt="{{ $product->title }}" 
                                     class="w-full h-auto rounded-lg shadow-md object-cover">
                            @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Right Column: Product Info -->
                        <div class="md:w-2/3 flex flex-col justify-between">
                            <div>
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h1 class="text-3xl font-bold text-gray-900">{{ $product->title }}</h1>
                                        <div class="mt-2">
                                            <span class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                        </div>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            Available
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-6">
                                    <h3 class="text-lg font-medium text-gray-900">Description</h3>
                                    <div class="mt-2 prose max-w-none text-gray-500">
                                        {!! nl2br(e($product->description)) !!}
                                    </div>
                                </div>
                            </div>

                            <!-- Buy Now Button -->
                            <div class="mt-8 flex justify-start">
                                @if(!auth()->user()?->hasPaid($product->id))
                                    <a href="{{ route('checkout', $product) }}"
                                       class="inline-flex justify-center rounded-md border border-transparent bg-yellow-500 py-3 px-6 text-base font-medium text-white shadow-sm hover:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:ring-offset-2 transition duration-150">
                                        Buy Now
                                    </a>
                                @else
                                    <a href="{{ route('products.download', $product) }}"
                                       class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-3 px-6 text-base font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition duration-150">
                                        Download Now
                                    </a>
                                @endif
                            </div>

                            <div class="mt-8 border-t border-gray-200 pt-6">
                                <h3 class="text-lg font-medium text-gray-900">Details</h3>
                                <div class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <span class="text-sm text-gray-500">Category</span>
                                        <span class="mt-1 block text-sm font-medium text-gray-900">Digital Product</span>
                                    </div>
                                    <div>
                                        <span class="text-sm text-gray-500">Last Updated</span>
                                        <span class="mt-1 block text-sm font-medium text-gray-900">{{ $product->updated_at->format('M d, Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

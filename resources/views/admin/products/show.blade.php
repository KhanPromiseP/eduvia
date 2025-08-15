@extends('layouts.admin')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product Details') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="flex flex-col md:flex-row gap-8">
                        <div class="md:w-1/3">
                            @if($product->thumbnail)
                                <img src="{{ asset('storage/'.$product->thumbnail) }}" alt="{{ $product->title }}" class="w-full h-auto rounded-lg shadow-md">
                            @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                </div>
                            @endif

                            @if($product->file_path)
                                <div class="mt-4">
                                    <a href="{{ asset('storage/'.$product->file_path) }}" download
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        Download Product File
                                    </a>
                                </div>
                            @endif
                        </div>

                        <div class="md:w-2/3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->title }}</h1>
                                    <div class="mt-2 flex items-center">
                                        <span class="text-3xl font-bold text-gray-900">${{ number_format($product->price, 2) }}</span>
                                        <span class="ml-3 px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $product->status === 'published' ? 'bg-green-100 text-green-800' : 
                                               ($product->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                                            {{ ucfirst($product->status) }}
                                        </span>
                                        <span class="ml-2 px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800' }}">
                                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.products.edit', $product) }}" 
                                        class="inline-flex items-center px-3 py-1 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        Edit
                                    </a>
                                    <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Are you sure?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="inline-flex items-center px-3 py-1 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div class="mt-6">
                                <h3 class="text-lg font-medium text-gray-900">Description</h3>
                                <div class="mt-2 prose max-w-none text-gray-500">
                                    {!! nl2br(e($product->description)) !!}
                                </div>
                            </div>

                            <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Created</h3>
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ $product->created_at->format('M d, Y \a\t h:i A') }}
                                    </div>
                                </div>
                                <div>
                                    <h3 class="text-lg font-medium text-gray-900">Last Updated</h3>
                                    <div class="mt-1 text-sm text-gray-500">
                                        {{ $product->updated_at->format('M d, Y \a\t h:i A') }}
                                    </div>
                                </div>
                            </div>

                            @if($product->metadata)
                                <div class="mt-6">
                                    <h3 class="text-lg font-medium text-gray-900">Metadata</h3>
                                    <div class="mt-2 bg-gray-50 p-4 rounded-lg">
                                        <pre class="text-sm text-gray-500 overflow-x-auto">{{ json_encode($product->metadata, JSON_PRETTY_PRINT) }}</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
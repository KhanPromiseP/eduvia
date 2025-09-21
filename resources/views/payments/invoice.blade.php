@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Invoice Header -->
        <div class="bg-indigo-600 text-white px-6 py-4">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-2xl font-bold">Payment Invoice</h1>
                    <p class="text-indigo-100">Transaction #{{ $payment->transaction_id }}</p>
                </div>
                <div class="text-right">
                    <span class="bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        Paid
                    </span>
                </div>
            </div>
        </div>

        <!-- Invoice Details -->
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">From:</h2>
                    <p class="text-gray-600">{{ config('app.name') }}</p>
                    <p class="text-gray-600">Education Platform</p>
                    <p class="text-gray-600">{{ config('app.url') }}</p>
                </div>
                
                <div>
                    <h2 class="text-lg font-semibold text-gray-800 mb-2">To:</h2>
                    <p class="text-gray-600">{{ $payment->user->name }}</p>
                    <p class="text-gray-600">{{ $payment->user->email }}</p>
                    <p class="text-gray-600">Customer since: {{ $payment->user->created_at->format('M d, Y') }}</p>
                </div>
            </div>

            <!-- Invoice Summary -->
            <div class="border-t border-b border-gray-200 py-4 mb-6">
                <div class="flex justify-between items-center">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800">Invoice Details</h3>
                        <p class="text-gray-600">Date: {{ $payment->completed_at->format('M d, Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-gray-600">Payment Method: {{ $payment->payment_method_display }}</p>
                        <p class="text-gray-600">Status: <span class="text-green-600 font-semibold">Completed</span></p>
                    </div>
                </div>
            </div>

            <!-- Course Details -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Course Purchased</h3>
                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="font-semibold text-gray-800">{{ $payment->course->title }}</h4>
                            <p class="text-gray-600 text-sm">{{ $payment->course->description }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-gray-800 font-semibold">{{ $payment->formatted_amount }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Payment Summary</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Course Price:</span>
                        <span class="text-gray-800">{{ $payment->formatted_amount }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax:</span>
                        <span class="text-gray-800">$0.00</span>
                    </div>
                    <div class="flex justify-between border-t border-gray-200 pt-2">
                        <span class="text-lg font-semibold text-gray-800">Total:</span>
                        <span class="text-lg font-semibold text-indigo-600">{{ $payment->formatted_amount }}</span>
                    </div>
                </div>
            </div>

            <!-- Transaction Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Transaction Details</h3>
                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Transaction ID:</span>
                        <span class="text-gray-800 font-mono">{{ $payment->transaction_id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Date:</span>
                        <span class="text-gray-800">{{ $payment->completed_at->format('M d, Y h:i A') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Payment Method:</span>
                        <span class="text-gray-800">{{ $payment->payment_method_display }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex justify-between items-center">
                {{-- resources/views/payments/invoice.blade.php --}}
                <a href="{{ route('userdashboard', ['course' => $payment->course->id]) }}" 
                class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    Start Learning
                </a>
                <a href="{{ route('payment.invoice.download', $payment) }}" 
                   class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700 transition">
                    Download Invoice
                </a>
            </div>
        </div>
    </div>

    <!-- Support Information -->
    <div class="mt-6 text-center text-gray-600">
        <p>Need help? Contact our support team at <a href="mailto:support@example.com" class="text-indigo-600 hover:underline">support@example.com</a></p>
        <p class="text-sm mt-2">Invoice generated on {{ now()->format('M d, Y h:i A') }}</p>
    </div>
</div>
@endsection
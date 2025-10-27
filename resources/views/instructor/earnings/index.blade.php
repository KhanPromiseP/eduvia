@extends('layouts.admin')

@section('content')
@php
    $totalEarnings = $totalEarnings ?? 0;
    $monthlyEarnings = $monthlyEarnings ?? collect([]);
    $courseEarnings = $courseEarnings ?? collect([]);
    $recentTransactions = $recentTransactions ?? collect([]);
    $platformStats = $platformStats ?? [];
    $pendingEarnings = $pendingEarnings ?? 0;
    $totalPaidOut = $totalPaidOut ?? 0;
@endphp

<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Earnings Dashboard</h1>
            <p class="text-gray-600 mt-1">Track your revenue and performance</p>
        </div>
        <div class="mt-4 lg:mt-0 text-right">
            <p class="text-sm text-gray-600">Next Payout Date</p>
            <p class="text-lg font-semibold text-blue-600">
                {{ \Carbon\Carbon::now()->endOfMonth()->format('M j, Y') }}
            </p>
        </div>
    </div>

    <!-- Earnings Overview Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Earnings -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-green-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Earnings</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalEarnings, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-green-600">
                <i class="fas fa-trending-up mr-1"></i>
                <span>All time revenue</span>
            </div>
        </div>

        <!-- Pending Payout -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payout</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($pendingEarnings, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-yellow-600">
                <i class="fas fa-calendar-alt mr-1"></i>
                <span>Paid end of month</span>
            </div>
        </div>

        <!-- Total Paid Out -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Paid Out</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($totalPaidOut, 2) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-blue-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-blue-600">
                <i class="fas fa-history mr-1"></i>
                <span>Historical payouts</span>
            </div>
        </div>

        <!-- Average per Student -->
        <div class="bg-white rounded-xl shadow-sm p-6 border-l-4 border-purple-500">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg/Student</p>
                    <p class="text-2xl font-bold text-gray-900">
                        ${{ $platformStats['total_students'] > 0 ? number_format($totalEarnings / $platformStats['total_students'], 2) : '0.00' }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-purple-600 text-xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center text-sm text-purple-600">
                <i class="fas fa-chart-pie mr-1"></i>
                <span>Revenue per student</span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <!-- Platform Statistics -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Platform Statistics</h3>
                <div class="space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total approved Courses</span>
                        <span class="font-semibold text-gray-900">{{ $platformStats['total_courses'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Students</span>
                        <span class="font-semibold text-gray-900">{{ $platformStats['total_students'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-gray-100">
                        <span class="text-gray-600">Total Enrollments</span>
                        <span class="font-semibold text-gray-900">{{ $platformStats['total_enrollments'] }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-gray-600">Completion Rate</span>
                        <span class="font-semibold text-green-600">{{ $platformStats['completion_rate'] }}%</span>
                    </div>
                </div>
            </div>

            <!-- Payout Information -->
            @if($payout)
            <div class="bg-white rounded-xl shadow-sm p-6 mt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Payout Settings</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Method:</span>
                        <span class="font-medium">{{ $payout->payout_method_display }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Account:</span>
                        <span class="font-medium">{{ $payout->masked_account_number }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Auto Payout:</span>
                        <span class="font-medium {{ $payout->auto_payout ? 'text-green-600' : 'text-yellow-600' }}">
                            {{ $payout->auto_payout ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                    @if($payout->auto_payout)
                    <div class="flex justify-between">
                        <span class="text-gray-600">Threshold:</span>
                        <span class="font-medium">${{ number_format($payout->payout_threshold, 2) }}</span>
                    </div>
                    @endif
                </div>
                <a href="{{ route('instructor.payout.setup') }}" 
                   class="w-full mt-4 inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 transition">
                    <i class="fas fa-cog mr-2"></i>
                    Update Payout Settings
                </a>
            </div>
            @endif
        </div>

        <!-- Course Performance -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-semibold text-gray-900">Course Performance</h3>
                    <span class="text-sm text-gray-500">{{ $courseEarnings->count() }} courses</span>
                </div>
                
                @if($courseEarnings->count() > 0)
                <div class="space-y-4">
                    @foreach($courseEarnings as $course)
                    <div class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-4">
                            @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" 
                                 class="w-12 h-12 rounded-lg object-cover">
                            @else
                            <div class="w-12 h-12 bg-gray-200 rounded-lg flex items-center justify-center">
                                <i class="fas fa-book text-gray-400"></i>
                            </div>
                            @endif
                            <div>
                                <h4 class="font-medium text-gray-900">{{ Str::limit($course->title, 40) }}</h4>
                                <p class="text-sm text-gray-500">
                                    {{ $course->enrollments_count }} enrollments
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900">
                                ${{ number_format($course->total_earnings ?? 0, 2) }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $course->enrollments_count > 0 ? number_format(($course->total_earnings ?? 0) / $course->enrollments_count, 2) : '0.00' }}/student
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-chart-bar text-gray-400 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No Course Earnings Yet</h3>
                    <p class="text-gray-500">Revenue will appear here when students purchase your courses.</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Monthly Breakdown -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Recent Transactions</h3>
            </div>
            <div class="p-6">
                @if($recentTransactions->count() > 0)
                <div class="space-y-4">
                    @foreach($recentTransactions as $transaction)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-shopping-cart text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">
                                    {{ $transaction->user->name ?? 'Student' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $transaction->course->title ?? 'Course' }}
                                </p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-gray-900 text-sm">
                                ${{ number_format($transaction->amount, 2) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                {{ $transaction->completed_at->format('M j, Y') }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-receipt text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No recent transactions</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Monthly Breakdown -->
        <div class="bg-white rounded-xl shadow-sm">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Monthly Breakdown</h3>
            </div>
            <div class="p-6">
                @if($monthlyEarnings->count() > 0)
                <div class="space-y-4">
                    @foreach($monthlyEarnings as $earning)
                    <div class="flex items-center justify-between py-3 border-b border-gray-100 last:border-0">
                        <div>
                            <p class="font-medium text-gray-900">
                                {{ \Carbon\Carbon::createFromDate($earning->year, $earning->month, 1)->format('F Y') }}
                            </p>
                            <p class="text-sm text-gray-500">
                                {{ $earning->transactions_count ?? 'Multiple' }} sales
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="font-semibold text-green-600">
                                ${{ number_format($earning->total, 2) }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Your share: ${{ number_format($earning->total * 0.7, 2) }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <i class="fas fa-calendar text-4xl text-gray-300 mb-3"></i>
                    <p class="text-gray-500">No monthly earnings data</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Revenue Share Information -->
    <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
        <div class="flex flex-col md:flex-row items-center justify-between">
            <div class="mb-4 md:mb-0">
                <h3 class="text-lg font-semibold text-blue-800">Revenue Share Breakdown</h3>
                <p class="text-blue-700 mt-1">Understand how your earnings are calculated</p>
            </div>
            <div class="flex space-x-6 text-center">
                <div>
                    <p class="text-2xl font-bold text-green-600">70%</p>
                    <p class="text-sm text-blue-700">Your Share</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-600">30%</p>
                    <p class="text-sm text-blue-700">Platform Fee</p>
                </div>
            </div>
        </div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-blue-700">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <span>Automatic monthly payouts</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-shield-alt text-blue-500 mr-2"></i>
                <span>Secure payment processing</span>
            </div>
            <div class="flex items-center">
                <i class="fas fa-chart-line text-purple-500 mr-2"></i>
                <span>Real-time analytics</span>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
@endpush

@push('scripts')
<script>
// Simple chart for monthly earnings (you can integrate Chart.js for better visuals)
document.addEventListener('DOMContentLoaded', function() {
    // Add any JavaScript for interactive charts here
    console.log('Earnings dashboard loaded');
});
</script>
@endpush
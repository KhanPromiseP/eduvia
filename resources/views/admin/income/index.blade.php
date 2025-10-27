@extends('layouts.admin')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Platform Income Management</h1>
            <p class="text-gray-600 mt-2">Comprehensive overview of platform revenue, analytics, and income distribution</p>
        </div>
        
        <!-- Period Filter & Actions -->
        <div class="flex items-center space-x-4 mt-4 lg:mt-0">
            <!-- Period Filter -->
            <div class="relative">
                <select id="periodFilter" class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="daily" {{ $period == 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ $period == 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ $period == 'monthly' ? 'selected' : '' }}>Monthly</option>
                    <option value="quarterly" {{ $period == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                    <option value="yearly" {{ $period == 'yearly' ? 'selected' : '' }}>Yearly</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <i class="fas fa-chevron-down"></i>
                </div>
            </div>

            <!-- Export Button -->
            <button onclick="exportReport()" 
                    class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-md">
                <i class="fas fa-file-export mr-2"></i>
                Export Report
            </button>
        </div>
    </div>

    <!-- Main Statistics Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Revenue -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Revenue</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($platformStats['total_revenue'], 2) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-lg">
                    <i class="fas fa-dollar-sign text-blue-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4 flex items-center">
                <span class="text-sm font-medium {{ $platformStats['revenue_growth'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    <i class="fas fa-arrow-{{ $platformStats['revenue_growth'] >= 0 ? 'up' : 'down' }} mr-1"></i>
                    {{ number_format(abs($platformStats['revenue_growth']), 1) }}%
                </span>
                <span class="text-sm text-gray-500 ml-2">vs previous period</span>
            </div>
        </div>

        <!-- Platform Commission -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Platform Commission</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($platformStats['platform_commission'], 2) }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-lg">
                    <i class="fas fa-percentage text-purple-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @php
                    $platformPercentage = $platformStats['total_revenue'] > 0 ? 
                        ($platformStats['platform_commission'] / $platformStats['total_revenue']) * 100 : 0;
                @endphp
                <span class="text-sm text-gray-500">{{ number_format($platformPercentage, 1) }}% of revenue</span>
            </div>
        </div>

        <!-- Instructor Payouts -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Instructor Payouts</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($platformStats['instructor_payouts'], 2) }}</p>
                </div>
                <div class="p-3 bg-green-100 rounded-lg">
                    <i class="fas fa-hand-holding-usd text-green-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @php
                    $payoutsPercentage = $platformStats['total_revenue'] > 0 ? 
                        ($platformStats['instructor_payouts'] / $platformStats['total_revenue']) * 100 : 0;
                @endphp
                <span class="text-sm text-gray-500">{{ number_format($payoutsPercentage, 1) }}% of revenue</span>
            </div>
        </div>

        <!-- Net Profit -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Net Profit</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">${{ number_format($platformStats['net_profit'], 2) }}</p>
                </div>
                <div class="p-3 bg-indigo-100 rounded-lg">
                    <i class="fas fa-chart-line text-indigo-600 text-2xl"></i>
                </div>
            </div>
            <div class="mt-4">
                @php
                    $profitMargin = $platformStats['total_revenue'] > 0 ? 
                        ($platformStats['net_profit'] / $platformStats['total_revenue']) * 100 : 0;
                @endphp
                <span class="text-sm text-gray-500">{{ number_format($profitMargin, 1) }}% margin</span>
            </div>
        </div>
    </div>

    <!-- Secondary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 rounded-lg mr-4">
                    <i class="fas fa-receipt text-orange-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($platformStats['total_transactions']) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-teal-100 rounded-lg mr-4">
                    <i class="fas fa-calculator text-teal-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Avg. Transaction</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($platformStats['avg_transaction_value'], 2) }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-pink-100 rounded-lg mr-4">
                    <i class="fas fa-clock text-pink-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Pending Payouts</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($platformStats['pending_payouts'], 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts and Analytics Section -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8 mb-8">
        <!-- Revenue Chart -->
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-lg font-semibold text-gray-900">Revenue Analytics</h2>
                <div class="text-sm text-gray-500">
                    @if(isset($dateRange) && isset($dateRange['start']) && isset($dateRange['end']))
                        {{ $dateRange['start']->format('M j, Y') }} - {{ $dateRange['end']->format('M j, Y') }}
                    @else
                        Date range not available
                    @endif
                </div>
            </div>
            <div class="h-80">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>

        <!-- Income Distribution -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-6">Income Distribution</h2>
            <div class="space-y-6">
                <!-- By Category -->
                <div>
                    <h3 class="text-sm font-medium text-gray-700 mb-3">By Category</h3>
                    <div class="space-y-2">
                        @forelse($incomeDistribution['by_category'] as $category)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600">{{ $category->category }}</span>
                            <span class="text-sm font-medium text-gray-900">${{ number_format($category->revenue, 2) }}</span>
                        </div>
                        @php
                            $categoryPercentage = $platformStats['total_revenue'] > 0 ? 
                                ($category->revenue / $platformStats['total_revenue']) * 100 : 0;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-blue-500 h-2 rounded-full" 
                                 style="width: {{ $categoryPercentage }}%"></div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-gray-500">
                            <i class="fas fa-chart-pie text-gray-300 text-2xl mb-2"></i>
                            <p class="text-sm">No category data available</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- By Payment Method -->
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="text-sm font-medium text-gray-700 mb-3">By Payment Method</h3>
                    <div class="space-y-2">
                        @forelse($incomeDistribution['by_payment_method'] as $method)
                        <div class="flex justify-between items-center">
                            <span class="text-sm text-gray-600 capitalize">{{ $method->payment_method }}</span>
                            <span class="text-sm font-medium text-gray-900">
                                ${{ number_format($method->amount, 2) }} ({{ $method->count }})
                            </span>
                        </div>
                        @empty
                        <div class="text-center py-2 text-gray-500 text-sm">
                            No payment method data available
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Performers Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Top Courses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-trophy text-yellow-500 mr-2"></i>
                    Top Performing Courses
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($topPerformers['courses'] as $index => $course)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">{{ Str::limit($course->title, 40) }}</h4>
                                <p class="text-xs text-gray-500">{{ $course->enrollments }} enrollments</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">${{ number_format($course->revenue, 2) }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-book text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm">No course data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Instructors -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-crown text-purple-500 mr-2"></i>
                    Top Earning Instructors
                </h2>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    @forelse($topPerformers['instructors'] as $index => $instructor)
                    <div class="flex items-center justify-between p-3 border border-gray-200 rounded-lg hover:bg-gray-50 transition">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center text-purple-600 font-bold text-sm">
                                {{ $index + 1 }}
                            </div>
                            <div>
                                <h4 class="font-medium text-gray-900 text-sm">{{ $instructor->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $instructor->courses_count }} courses</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="font-bold text-gray-900">${{ number_format($instructor->revenue, 2) }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-8 text-gray-500">
                        <i class="fas fa-chalkboard-teacher text-gray-300 text-2xl mb-2"></i>
                        <p class="text-sm">No instructor data available</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Transactions & Payout Summary -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        <!-- Recent Transactions -->
        <div class="xl:col-span-2 bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-exchange-alt text-green-500 mr-2"></i>
                    Recent Transactions
                </h2>
            </div>
            <div class="p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Student</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Course</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Amount</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Date</th>
                                <th class="text-left py-3 text-sm font-medium text-gray-600">Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr class="border-b border-gray-100 hover:bg-gray-50">
                                <td class="py-3 text-sm text-gray-900">{{ $transaction->user->name ?? 'N/A' }}</td>
                                <td class="py-3 text-sm text-gray-600">
                                    @if($transaction->userCourse && $transaction->userCourse->course)
                                        {{ Str::limit($transaction->userCourse->course->title, 30) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="py-3 text-sm font-medium text-gray-900">${{ number_format($transaction->amount, 2) }}</td>
                                <td class="py-3 text-sm text-gray-500">{{ $transaction->completed_at->format('M j, H:i') }}</td>
                                <td class="py-3 text-sm text-gray-500 capitalize">{{ $transaction->payment_method }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-8 text-center text-gray-500">
                                    <i class="fas fa-receipt text-gray-300 text-2xl mb-2"></i>
                                    <p class="text-sm">No recent transactions</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Payout Summary -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                    <i class="fas fa-money-check-alt text-blue-500 mr-2"></i>
                    Payout Summary
                </h2>
            </div>
            <div class="p-6 space-y-6">
                <div class="text-center">
                    <div class="text-3xl font-bold text-gray-900">${{ number_format($payoutSummary['total_payouts'], 2) }}</div>
                    <p class="text-sm text-gray-500 mt-1">Total Payouts</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-lg font-bold text-gray-900">{{ number_format($payoutSummary['payout_count']) }}</div>
                        <p class="text-xs text-gray-500">Payouts Processed</p>
                    </div>
                    <div class="text-center p-3 bg-gray-50 rounded-lg">
                        <div class="text-lg font-bold text-gray-900">${{ number_format($payoutSummary['avg_payout'], 2) }}</div>
                        <p class="text-xs text-gray-500">Average Payout</p>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-200">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-sm font-medium text-gray-700">Pending Payouts</span>
                        <span class="text-lg font-bold text-yellow-600">${{ number_format($payoutSummary['pending_payouts'], 2) }}</span>
                    </div>
                    <button class="w-full mt-3 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                        Process Payouts
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Period Filter
document.getElementById('periodFilter').addEventListener('change', function() {
    const period = this.value;
    const url = new URL(window.location.href);
    url.searchParams.set('period', period);
    window.location.href = url.toString();
});

// Export Report
function exportReport() {
    const period = document.getElementById('periodFilter').value;
    window.location.href = `/admin/income/export?period=${period}`;
}

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');

// Prepare chart data safely
const chartLabels = @json(array_column($timeAnalytics, 'label') ?? []);
const revenueData = @json(array_column($timeAnalytics, 'revenue') ?? []);
const transactionData = @json(array_column($timeAnalytics, 'transactions') ?? []);

const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: chartLabels,
        datasets: [{
            label: 'Revenue ($)',
            data: revenueData,
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }, {
            label: 'Transactions',
            data: transactionData,
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y1'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        interaction: {
            mode: 'index',
            intersect: false,
        },
        scales: {
            y: {
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Revenue ($)'
                },
                grid: {
                    drawOnChartArea: true,
                },
                beginAtZero: true
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Transactions'
                },
                grid: {
                    drawOnChartArea: false,
                },
                beginAtZero: true
            },
        },
        plugins: {
            legend: {
                position: 'top',
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.dataset.label || '';
                        if (label) {
                            label += ': ';
                        }
                        if (context.dataset.yAxisID === 'y1') {
                            label += context.parsed.y + ' transactions';
                        } else {
                            label += '$' + context.parsed.y.toFixed(2);
                        }
                        return label;
                    }
                }
            }
        }
    }
});
</script>

<style>
.hover-lift:hover {
    transform: translateY(-2px);
    transition: all 0.2s ease;
}

.chart-container {
    position: relative;
    height: 300px;
    width: 100%;
}
</style>
@endpush
@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Ads Analytics Dashboard</h1>
    
    <!-- Date Range Filter -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Date Range</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dashboard') }}">
                <div class="form-row align-items-center">
                    <div class="col-md-3">
                        <label for="start_date">Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" value="{{ $startDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date">End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" value="{{ $endDate }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_range">Quick Range</label>
                        <select class="form-control" id="date_range" name="date_range">
                            @foreach($dateRanges as $value => $label)
                                <option value="{{ $value }}" {{ request('date_range') == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary mt-md-4">Apply Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Overview Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Views</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['overview']['total_views']) }}</div>
                            <div class="mt-2 text-muted">
                                @if($analytics['overview']['views_change'] > 0)
                                    <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $analytics['overview']['views_change'] }}%</span>
                                @elseif($analytics['overview']['views_change'] < 0)
                                    <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($analytics['overview']['views_change']) }}%</span>
                                @else
                                    <span class="text-muted">No change</span>
                                @endif
                                from previous period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Total Clicks</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($analytics['overview']['total_clicks']) }}</div>
                            <div class="mt-2 text-muted">
                                @if($analytics['overview']['clicks_change'] > 0)
                                    <span class="text-success"><i class="fas fa-arrow-up"></i> {{ $analytics['overview']['clicks_change'] }}%</span>
                                @elseif($analytics['overview']['clicks_change'] < 0)
                                    <span class="text-danger"><i class="fas fa-arrow-down"></i> {{ abs($analytics['overview']['clicks_change']) }}%</span>
                                @else
                                    <span class="text-muted">No change</span>
                                @endif
                                from previous period
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mouse-pointer fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">CTR</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['overview']['ctr'] }}%</div>
                            <div class="mt-2 text-muted">
                                Click-through rate
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percent fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Avg. Time Spent</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $analytics['overview']['avg_time_spent'] }}s</div>
                            <div class="mt-2 text-muted">
                                Per view
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Performance Trends Chart -->
    <div class="row">
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Performance Trends</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="performanceChart" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Device Breakdown</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="deviceChart" height="250"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($analytics['device_breakdown'] as $device => $data)
                            <span class="mr-2">
                                <i class="fas fa-circle 
                                    @if($device == 'Desktop') text-primary
                                    @elseif($device == 'Mobile') text-success
                                    @else text-info
                                    @endif"></i> {{ $device }} ({{ $data['percentage'] }}%)
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Additional sections for geo distribution, top performers, etc. -->
    <!-- You would add more chart sections here -->
    
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Performance Trends Chart
    const performanceCtx = document.getElementById('performanceChart');
    
    if (performanceCtx) {
        const performanceChart = new Chart(performanceCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($analytics['performance_trends']['views'])) !!},
                datasets: [
                    {
                        label: "Views",
                        tension: 0.3,
                        backgroundColor: "rgba(78, 115, 223, 0.05)",
                        borderColor: "rgba(78, 115, 223, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointBorderColor: "rgba(78, 115, 223, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(78, 115, 223, 1)",
                        pointHoverBorderColor: "rgba(78, 115, 223, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: {!! json_encode(array_values($analytics['performance_trends']['views'])) !!},
                    },
                    {
                        label: "Clicks",
                        tension: 0.3,
                        backgroundColor: "rgba(28, 200, 138, 0.05)",
                        borderColor: "rgba(28, 200, 138, 1)",
                        pointRadius: 3,
                        pointBackgroundColor: "rgba(28, 200, 138, 1)",
                        pointBorderColor: "rgba(28, 200, 138, 1)",
                        pointHoverRadius: 5,
                        pointHoverBackgroundColor: "rgba(28, 200, 138, 1)",
                        pointHoverBorderColor: "rgba(28, 200, 138, 1)",
                        pointHitRadius: 10,
                        pointBorderWidth: 2,
                        data: {!! json_encode(array_values($analytics['performance_trends']['clicks'])) !!},
                    }
                ],
            },
            options: {
                maintainAspectRatio: false,
                layout: {
                    padding: {
                        left: 10,
                        right: 25,
                        top: 25,
                        bottom: 0
                    }
                },
                scales: {
                    x: {
                        type: 'time',
                        time: {
                            unit: 'day',
                            tooltipFormat: 'MMM d, yyyy'
                        },
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            maxTicksLimit: 7
                        }
                    },
                    y: {
                        ticks: {
                            maxTicksLimit: 5,
                            padding: 10,
                        },
                        grid: {
                            color: "rgb(234, 236, 244)",
                            drawBorder: false,
                            borderDash: [2],
                            zeroLineBorderDash: [2]
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        titleMarginBottom: 10,
                        titleColor: '#6e707e',
                        titleFont: {
                            size: 14
                        },
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        intersect: false,
                        mode: 'index',
                        caretPadding: 10,
                    }
                }
            }
        });
    }

    // Device Breakdown Chart
    const deviceCtx = document.getElementById('deviceChart');
    
    if (deviceCtx) {
        const deviceChart = new Chart(deviceCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode(array_keys($analytics['device_breakdown'])) !!},
                datasets: [{
                    data: {!! json_encode(array_column($analytics['device_breakdown'], 'percentage')) !!},
                    backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
                    hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
                    hoverBorderColor: "rgba(234, 236, 244, 1)",
                }],
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                    tooltip: {
                        backgroundColor: "rgb(255,255,255)",
                        bodyColor: "#858796",
                        borderColor: '#dddfeb',
                        borderWidth: 1,
                        padding: 15,
                        displayColors: false,
                        caretPadding: 10,
                    },
                    legend: {
                        display: false
                    }
                },
                cutout: '80%',
            },
        });
    }
});
</script>
@endpush
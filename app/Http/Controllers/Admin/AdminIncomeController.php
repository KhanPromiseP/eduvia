<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\InstructorEarning;
use App\Models\Course;
use App\Models\User;
use App\Models\Instructor;
use App\Models\UserCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminIncomeController extends Controller
{
    /**
     * Display platform income dashboard
     */
    public function index(Request $request)
    {
        // Get time period filter
        $period = $request->get('period', 'monthly');
        $dateRange = $this->getDateRange($period);

        // Overall Platform Statistics
        $platformStats = $this->getPlatformStats($dateRange);

        // Income Distribution
        $incomeDistribution = $this->getIncomeDistribution($dateRange);

        // Time-based analytics
        $timeAnalytics = $this->getTimeAnalytics($period, $dateRange);

        // Top Performers
        $topPerformers = $this->getTopPerformers($dateRange);

        // Recent Transactions
        $recentTransactions = $this->getRecentTransactions();

        // Payout Summary
        $payoutSummary = $this->getPayoutSummary($dateRange);

        return view('admin.income.index', compact(
            'platformStats',
            'incomeDistribution',
            'timeAnalytics',
            'topPerformers',
            'recentTransactions',
            'payoutSummary',
            'period',
            'dateRange' // Make sure dateRange is passed to the view
        ));
    }

    /**
     * Get date range based on period
     */
    private function getDateRange($period)
    {
        $now = Carbon::now();

        switch ($period) {
            case 'daily':
                return [
                    'start' => $now->copy()->startOfDay(),
                    'end' => $now->copy()->endOfDay()
                ];
            case 'weekly':
                return [
                    'start' => $now->copy()->startOfWeek(),
                    'end' => $now->copy()->endOfWeek()
                ];
            case 'monthly':
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
            case 'quarterly':
                return [
                    'start' => $now->copy()->startOfQuarter(),
                    'end' => $now->copy()->endOfQuarter()
                ];
            case 'yearly':
                return [
                    'start' => $now->copy()->startOfYear(),
                    'end' => $now->copy()->endOfYear()
                ];
            default:
                return [
                    'start' => $now->copy()->startOfMonth(),
                    'end' => $now->copy()->endOfMonth()
                ];
        }
    }

    /**
     * Get overall platform statistics
     */
    private function getPlatformStats($dateRange)
    {
        try {
            // Total Revenue
            $totalRevenue = Payment::where('status', 'completed')
                ->whereBetween('completed_at', [$dateRange['start'], $dateRange['end']])
                ->sum('amount') ?? 0;

            // Platform Commission (assuming 20% platform fee)
            $platformCommission = $totalRevenue * 0.20;

            // Instructor Payouts
            $instructorPayouts = InstructorEarning::where('status', InstructorEarning::STATUS_PAID_OUT)
                ->whereBetween('paid_out_at', [$dateRange['start'], $dateRange['end']])
                ->sum('amount') ?? 0;

            // Pending Payouts
            $pendingPayouts = InstructorEarning::where('status', InstructorEarning::STATUS_PROCESSED)
                ->sum('amount') ?? 0;

            // Total Transactions
            $totalTransactions = Payment::where('status', 'completed')
                ->whereBetween('completed_at', [$dateRange['start'], $dateRange['end']])
                ->count();

            // Average Transaction Value
            $avgTransactionValue = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

            // Growth compared to previous period
            $previousPeriod = [
                'start' => $dateRange['start']->copy()->subMonth(),
                'end' => $dateRange['end']->copy()->subMonth()
            ];

            $previousRevenue = Payment::where('status', 'completed')
                ->whereBetween('completed_at', [$previousPeriod['start'], $previousPeriod['end']])
                ->sum('amount') ?? 0;

            $revenueGrowth = $previousRevenue > 0 ? 
                (($totalRevenue - $previousRevenue) / $previousRevenue) * 100 : 
                ($totalRevenue > 0 ? 100 : 0);

            return [
                'total_revenue' => $totalRevenue,
                'platform_commission' => $platformCommission,
                'instructor_payouts' => $instructorPayouts,
                'pending_payouts' => $pendingPayouts,
                'total_transactions' => $totalTransactions,
                'avg_transaction_value' => $avgTransactionValue,
                'revenue_growth' => $revenueGrowth,
                'net_profit' => $platformCommission - $instructorPayouts
            ];

        } catch (\Exception $e) {
            \Log::error('Platform stats error: ' . $e->getMessage());
            
            return [
                'total_revenue' => 0,
                'platform_commission' => 0,
                'instructor_payouts' => 0,
                'pending_payouts' => 0,
                'total_transactions' => 0,
                'avg_transaction_value' => 0,
                'revenue_growth' => 0,
                'net_profit' => 0
            ];
        }
    }

    /**
     * Get income distribution data
     */
    private function getIncomeDistribution($dateRange)
    {
        try {
            // Revenue by course category
            $revenueByCategory = DB::table('payments')
                ->join('user_courses', function($join) {
                    $join->on('payments.id', '=', 'user_courses.payment_id')
                         ->orWhere(function($query) {
                             $query->where('payments.user_id', '=', 'user_courses.user_id')
                                   ->whereRaw('payments.created_at BETWEEN user_courses.created_at AND DATE_ADD(user_courses.created_at, INTERVAL 1 HOUR)');
                         });
                })
                ->join('courses', 'user_courses.course_id', '=', 'courses.id')
                ->leftJoin('categories', 'courses.category_id', '=', 'categories.id')
                ->where('payments.status', 'completed')
                ->whereBetween('payments.completed_at', [$dateRange['start'], $dateRange['end']])
                ->select(
                    DB::raw('COALESCE(categories.name, "Uncategorized") as category'),
                    DB::raw('SUM(payments.amount) as revenue')
                )
                ->groupBy('categories.id', 'categories.name')
                ->orderBy('revenue', 'desc')
                ->get();

            // Revenue by instructor
            $revenueByInstructor = DB::table('payments')
                ->join('user_courses', function($join) {
                    $join->on('payments.id', '=', 'user_courses.payment_id')
                         ->orWhere(function($query) {
                             $query->where('payments.user_id', '=', 'user_courses.user_id')
                                   ->whereRaw('payments.created_at BETWEEN user_courses.created_at AND DATE_ADD(user_courses.created_at, INTERVAL 1 HOUR)');
                         });
                })
                ->join('courses', 'user_courses.course_id', '=', 'courses.id')
                ->join('users', 'courses.user_id', '=', 'users.id')
                ->where('payments.status', 'completed')
                ->whereBetween('payments.completed_at', [$dateRange['start'], $dateRange['end']])
                ->select(
                    'users.name as instructor_name',
                    'users.id as instructor_id',
                    DB::raw('SUM(payments.amount) as revenue'),
                    DB::raw('COUNT(DISTINCT courses.id) as courses_count')
                )
                ->groupBy('users.id', 'users.name')
                ->orderBy('revenue', 'desc')
                ->take(10)
                ->get();

            // Payment method distribution
            $paymentMethodDistribution = Payment::where('status', 'completed')
                ->whereBetween('completed_at', [$dateRange['start'], $dateRange['end']])
                ->select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(amount) as amount'))
                ->groupBy('payment_method')
                ->get();

            return [
                'by_category' => $revenueByCategory,
                'by_instructor' => $revenueByInstructor,
                'by_payment_method' => $paymentMethodDistribution
            ];

        } catch (\Exception $e) {
            \Log::error('Income distribution query error: ' . $e->getMessage());
            
            return [
                'by_category' => collect(),
                'by_instructor' => collect(),
                'by_payment_method' => collect()
            ];
        }
    }

    /**
     * Get time-based analytics
     */
    private function getTimeAnalytics($period, $dateRange)
    {
        $analytics = [];

        try {
            switch ($period) {
                case 'daily':
                    for ($hour = 0; $hour < 24; $hour++) {
                        $hourStart = $dateRange['start']->copy()->addHours($hour);
                        $hourEnd = $hourStart->copy()->addHour();

                        $revenue = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$hourStart, $hourEnd])
                            ->sum('amount') ?? 0;

                        $transactions = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$hourStart, $hourEnd])
                            ->count();

                        $analytics[] = [
                            'label' => $hourStart->format('H:00'),
                            'revenue' => $revenue,
                            'transactions' => $transactions
                        ];
                    }
                    break;

                case 'weekly':
                    for ($day = 0; $day < 7; $day++) {
                        $dayStart = $dateRange['start']->copy()->addDays($day);
                        $dayEnd = $dayStart->copy()->endOfDay();

                        $revenue = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$dayStart, $dayEnd])
                            ->sum('amount') ?? 0;

                        $transactions = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$dayStart, $dayEnd])
                            ->count();

                        $analytics[] = [
                            'label' => $dayStart->format('D'),
                            'revenue' => $revenue,
                            'transactions' => $transactions
                        ];
                    }
                    break;

                case 'monthly':
                    $weeks = ceil($dateRange['start']->diffInDays($dateRange['end']) / 7);
                    for ($week = 0; $week < $weeks; $week++) {
                        $weekStart = $dateRange['start']->copy()->addWeeks($week);
                        $weekEnd = $weekStart->copy()->addWeek();

                        $revenue = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$weekStart, $weekEnd])
                            ->sum('amount') ?? 0;

                        $transactions = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$weekStart, $weekEnd])
                            ->count();

                        $analytics[] = [
                            'label' => 'Week ' . ($week + 1),
                            'revenue' => $revenue,
                            'transactions' => $transactions
                        ];
                    }
                    break;

                case 'yearly':
                    for ($month = 0; $month < 12; $month++) {
                        $monthStart = $dateRange['start']->copy()->addMonths($month);
                        $monthEnd = $monthStart->copy()->endOfMonth();

                        $revenue = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$monthStart, $monthEnd])
                            ->sum('amount') ?? 0;

                        $transactions = Payment::where('status', 'completed')
                            ->whereBetween('completed_at', [$monthStart, $monthEnd])
                            ->count();

                        $analytics[] = [
                            'label' => $monthStart->format('M'),
                            'revenue' => $revenue,
                            'transactions' => $transactions
                        ];
                    }
                    break;
            }
        } catch (\Exception $e) {
            \Log::error('Time analytics error: ' . $e->getMessage());
        }

        return $analytics;
    }

    /**
     * Get top performers
     */
    private function getTopPerformers($dateRange)
    {
        try {
            // Top courses by revenue
            $topCourses = DB::table('payments')
                ->join('user_courses', function($join) {
                    $join->on('payments.id', '=', 'user_courses.payment_id')
                         ->orWhere(function($query) {
                             $query->where('payments.user_id', '=', 'user_courses.user_id')
                                   ->whereRaw('payments.created_at BETWEEN user_courses.created_at AND DATE_ADD(user_courses.created_at, INTERVAL 1 HOUR)');
                         });
                })
                ->join('courses', 'user_courses.course_id', '=', 'courses.id')
                ->where('payments.status', 'completed')
                ->whereBetween('payments.completed_at', [$dateRange['start'], $dateRange['end']])
                ->select(
                    'courses.title',
                    'courses.id',
                    DB::raw('SUM(payments.amount) as revenue'),
                    DB::raw('COUNT(DISTINCT user_courses.id) as enrollments')
                )
                ->groupBy('courses.id', 'courses.title')
                ->orderBy('revenue', 'desc')
                ->take(5)
                ->get();

            // Top instructors by revenue
            $topInstructors = DB::table('payments')
                ->join('user_courses', function($join) {
                    $join->on('payments.id', '=', 'user_courses.payment_id')
                         ->orWhere(function($query) {
                             $query->where('payments.user_id', '=', 'user_courses.user_id')
                                   ->whereRaw('payments.created_at BETWEEN user_courses.created_at AND DATE_ADD(user_courses.created_at, INTERVAL 1 HOUR)');
                         });
                })
                ->join('courses', 'user_courses.course_id', '=', 'courses.id')
                ->join('users', 'courses.user_id', '=', 'users.id')
                ->where('payments.status', 'completed')
                ->whereBetween('payments.completed_at', [$dateRange['start'], $dateRange['end']])
                ->select(
                    'users.name',
                    'users.id',
                    DB::raw('SUM(payments.amount) as revenue'),
                    DB::raw('COUNT(DISTINCT courses.id) as courses_count')
                )
                ->groupBy('users.id', 'users.name')
                ->orderBy('revenue', 'desc')
                ->take(5)
                ->get();

        } catch (\Exception $e) {
            \Log::error('Top performers query error: ' . $e->getMessage());
            $topCourses = collect();
            $topInstructors = collect();
        }

        return [
            'courses' => $topCourses,
            'instructors' => $topInstructors
        ];
    }

    /**
     * Get recent transactions
     */
    private function getRecentTransactions()
    {
        try {
            return Payment::with(['user', 'userCourse.course'])
                ->where('status', 'completed')
                ->orderBy('completed_at', 'desc')
                ->take(10)
                ->get();
        } catch (\Exception $e) {
            \Log::error('Recent transactions error: ' . $e->getMessage());
            return collect();
        }
    }

    /**
     * Get payout summary
     */
    private function getPayoutSummary($dateRange)
    {
        try {
            $totalPayouts = InstructorEarning::where('status', InstructorEarning::STATUS_PAID_OUT)
                ->whereBetween('paid_out_at', [$dateRange['start'], $dateRange['end']])
                ->sum('amount') ?? 0;

            $pendingPayouts = InstructorEarning::where('status', InstructorEarning::STATUS_PROCESSED)
                ->sum('amount') ?? 0;

            $payoutCount = InstructorEarning::where('status', InstructorEarning::STATUS_PAID_OUT)
                ->whereBetween('paid_out_at', [$dateRange['start'], $dateRange['end']])
                ->count();

            $avgPayout = $payoutCount > 0 ? $totalPayouts / $payoutCount : 0;

            return [
                'total_payouts' => $totalPayouts,
                'pending_payouts' => $pendingPayouts,
                'payout_count' => $payoutCount,
                'avg_payout' => $avgPayout
            ];
        } catch (\Exception $e) {
            \Log::error('Payout summary error: ' . $e->getMessage());
            return [
                'total_payouts' => 0,
                'pending_payouts' => 0,
                'payout_count' => 0,
                'avg_payout' => 0
            ];
        }
    }

    /**
     * Export income report
     */
    public function exportReport(Request $request)
    {
        $period = $request->get('period', 'monthly');
        $dateRange = $this->getDateRange($period);

        // Generate CSV or PDF report
        // Implementation depends on export library
        return response()->json(['message' => 'Export functionality to be implemented']);
    }
}
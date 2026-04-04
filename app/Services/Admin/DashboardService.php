<?php

namespace App\Services\Admin;

use App\Models\AffiliateCommission;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    /**
     * Get aggregated statistics for the admin dashboard.
     * Refactored to avoid IDE inference limitations and improve modularity.
     */
    public function getAggregateStats()
    {
        return Cache::remember('admin_dashboard_stats', 30, function () {
            try {
                $today = Carbon::today();
                $successStatuses = ['success', 'captured'];

                // 1. Revenue & Payment Metrics
                $paymentStats = $this->getPaymentMetrics($successStatuses, $today);

                // 2. User & Student Metrics
                $userStats = $this->getUserMetrics($today);

                // 3. Course Metrics
                $courseMetrics = $this->getCourseMetrics();

                // 4. Bundle Performance (Distribution Chart Data)
                $bundleStats = $this->getBundlePerformance($successStatuses);

                // 5. Recent Activity
                $recentRegistrations = User::latest()->take(6)->get(['id', 'name', 'email', 'profile_picture', 'created_at']);
                $recentTransactions = Payment::latest()
                    ->take(6)
                    ->with('user:id,name,profile_picture')
                    ->get(['id', 'amount', 'status', 'created_at', 'user_id']);

                return array_merge(
                    $paymentStats,
                    $userStats,
                    $courseMetrics,
                    [
                        'recent_registrations' => $recentRegistrations,
                        'recent_transactions'  => $recentTransactions,
                        'bundle_stats'         => $bundleStats,
                    ],
                    // Profit Cards
                    [
                        'today_profit'        => $this->calculateProfit('today'),
                        'seven_days_profit'   => $this->calculateProfit('last_7_days'),
                        'thirty_days_profit'  => $this->calculateProfit('last_30_days'),
                        'all_time_profit'     => $this->calculateProfit('all_time'),
                    ]
                );

            } catch (\Exception $e) {
                Log::error('Dashboard Aggregation Total Failure: ' . $e->getMessage());
                return $this->getFallbackStats();
            }
        });
    }

    /**
     * Extract revenue and commission metrics.
     */
    private function getPaymentMetrics(array $statuses, Carbon $today): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

        $revenueThisMonth = (float) Payment::whereIn('status', $statuses)->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
        $revenueLastMonth = (float) Payment::whereIn('status', $statuses)->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');

        $growthPercentage = $revenueLastMonth > 0 ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100 : 0;

        return [
            'total_revenue'         => (float) Payment::whereIn('status', $statuses)->sum('amount'),
            'today_revenue'         => (float) Payment::whereIn('status', $statuses)->whereDate('created_at', $today)->sum('amount'),
            'seven_days_revenue'    => (float) Payment::whereIn('status', $statuses)->where('created_at', '>=', Carbon::now()->subDays(7))->sum('amount'),
            'thirty_days_revenue'   => (float) Payment::whereIn('status', $statuses)->where('created_at', '>=', Carbon::now()->subDays(30))->sum('amount'),
            'all_time_revenue'      => (float) Payment::whereIn('status', $statuses)->sum('amount'),
            'revenue_growth'        => round($growthPercentage, 1),
            'total_paid_commission' => (float) AffiliateCommission::paid()->sum('amount'),
            'pending_commission'    => (float) AffiliateCommission::whereIn('status', ['requested', 'processing'])->sum('amount'),
            'sales_today'           => Payment::where('status', 'success')->whereDate('created_at', $today)->count(),
        ];
    }

    /**
     * Extract user registration metrics.
     */
    private function getUserMetrics(Carbon $today): array
    {
        return [
            'total_users'     => User::role('Student')->count(),
            'new_users_today' => User::role('Student')->whereDate('created_at', $today)->count(),
            'active_students' => User::role('Student')->where('is_active', true)->count(),
        ];
    }

    /**
     * Extract course-related metrics.
     */
    private function getCourseMetrics(): array
    {
        try {
            $topCourses = Course::withCount('students')
                ->orderBy('students_count', 'desc')
                ->take(4)
                ->get(['id', 'title', 'website_price', 'thumbnail']);
        } catch (\Exception $e) {
            Log::error('Dashboard Course Metrics Error: ' . $e->getMessage());
            $topCourses = [];
        }

        return [
            'total_courses'  => Course::count(),
            'active_courses' => Course::where('is_published', true)->count(),
            'top_courses'    => $topCourses,
        ];
    }

    /**
     * Extract bundle distribution statistics.
     */
    private function getBundlePerformance(array $statuses): array
    {
        try {
            return Bundle::where('is_published', true)
                ->withCount(['payments' => function ($query) use ($statuses) {
                    $query->whereIn('status', $statuses);
                }])
                ->get()
                ->map(function ($bundle) use ($statuses) {
                    return [
                        'id'          => $bundle->id,
                        'title'       => $bundle->title,
                        'sales_count' => $bundle->payments_count,
                        'revenue'     => (float) Payment::where('bundle_id', $bundle->id)
                            ->whereIn('status', $statuses)
                            ->sum('amount'),
                    ];
                })
                ->sortByDesc('sales_count')
                ->values()
                ->toArray();
        } catch (\Exception $e) {
            Log::error('Dashboard Bundle Performance Error: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Safety fallback stats in case of total aggregation failure.
     */
    private function getFallbackStats(): array
    {
        return [
            'total_users' => 0, 'new_users_today' => 0, 'active_students' => 0,
            'total_courses' => 0, 'active_courses' => 0,
            'total_revenue' => 0, 'today_revenue' => 0, 'seven_days_revenue' => 0, 'thirty_days_revenue' => 0, 'all_time_revenue' => 0,
            'revenue_growth' => 0, 'total_paid_commission' => 0, 'pending_commission' => 0, 'sales_today' => 0,
            'today_profit' => 0, 'seven_days_profit' => 0, 'thirty_days_profit' => 0, 'all_time_profit' => 0,
            'recent_registrations' => [], 'top_courses' => [], 'bundle_stats' => [], 'recent_transactions' => [],
        ];
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Profit Calculation (GST-inclusive ÷ 1.18 + Affiliate Comm + 2% GW)
    // ─────────────────────────────────────────────────────────────────────────

    /**
     * Calculate net admin profit for a given period.
     *
     * Formula:
     *   Base Price    = Price ÷ 1.18        (extract base from GST-inclusive price)
     *   Gateway Fee   = Price × 0.02        (2% payment gateway fee)
     *   Commission    = SUM of affiliate_commissions in the window
     *                   (DB already stores effective amount: differential for upgrades)
     *   Net Profit    = Base Price − Commission − Gateway Fee
     */
    private function calculateProfit(string $period): float
    {
        try {
            $startDate = match ($period) {
                'today'        => Carbon::today(),
                'last_7_days'  => Carbon::now()->subDays(7),
                'last_30_days' => Carbon::now()->subDays(30),
                default        => null,
            };

            // 1. Total Revenue in period (Captured & Success)
            $paymentQuery = Payment::whereIn('status', ['success', 'captured']);
            if ($startDate) {
                $paymentQuery->where('created_at', '>=', $startDate);
            }
            $totalAmount = (float) $paymentQuery->sum('amount');
            if ($totalAmount <= 0) return 0.0;

            // 2. Extract base price (remove 18% GST from inclusive price)
            $basePrice = $totalAmount / 1.18;

            // 3. Gateway Fee (2% of total collected amount)
            $gatewayFee = $totalAmount * 0.02;

            // 4. Commission (already effective in DB: differential for upgrades, full for new)
            $commissionQuery = AffiliateCommission::query();
            if ($startDate) {
                $commissionQuery->where('created_at', '>=', $startDate);
            }
            $totalCommission = (float) $commissionQuery->sum('amount');

            // Net Profit = Base Price − Commission − Gateway Fee
            $netProfit = $basePrice - $totalCommission - $gatewayFee;

            return (float) round(max(0, $netProfit), 2);

        } catch (\Exception $e) {
            Log::error('DashboardService@calculateProfit: ' . $e->getMessage(), ['period' => $period]);
            return 0.0;
        }
    }


    public function getSalesChartData(string $period = 'month')
    {
        // ... (Your existing chart code remains unchanged)
        // Just ensure you cast results to float/int to avoid nulls

        $query = Payment::whereIn('status', ['success', 'captured']);
        $labels = [];
        $data = [];

        try {
            switch ($period) {
                case 'week':
                    $startDate = Carbon::now()->subDays(6)->startOfDay();
                    $stats = $query->where('created_at', '>=', $startDate)
                        ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                        ->groupBy('date')
                        ->pluck('total', 'date');

                    for ($i = 0; $i < 7; $i++) {
                        $date = $startDate->copy()->addDays($i)->format('Y-m-d');
                        $labels[] = Carbon::parse($date)->format('D');
                        $data[] = (float) ($stats[$date] ?? 0);
                    }
                    break;

                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $daysInMonth = Carbon::now()->daysInMonth;
                    $stats = $query->whereBetween('created_at', [$startDate, Carbon::now()->endOfMonth()])
                        ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
                        ->groupBy('date')
                        ->pluck('total', 'date');

                    for ($i = 1; $i <= $daysInMonth; $i++) {
                        $date = Carbon::createFromDate(null, null, $i)->format('Y-m-d');
                        $labels[] = (string) $i;
                        $data[] = (float) ($stats[$date] ?? 0);
                    }
                    break;

                case '6months':
                    $startDate = Carbon::now()->subMonths(5)->startOfMonth();
                    $stats = $query->where('created_at', '>=', $startDate)
                        ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, SUM(amount) as total')
                        ->groupBy('month')
                        ->pluck('total', 'month');

                    for ($i = 0; $i < 6; $i++) {
                        $date = $startDate->copy()->addMonths($i);
                        $key = $date->format('Y-m');
                        $labels[] = $date->format('M');
                        $data[] = (float) ($stats[$key] ?? 0);
                    }
                    break;
            }
        } catch (\Exception $e) {
            Log::error('Chart Data Error: ' . $e->getMessage());
            return ['labels' => [], 'data' => []];
        }

        return ['labels' => $labels, 'data' => $data];
    }
}

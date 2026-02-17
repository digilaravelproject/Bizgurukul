<?php

namespace App\Services\Admin;

use App\Models\AffiliateCommission;
use App\Models\Course;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function getAggregateStats()
    {
        // Cache for 5 minutes (300 seconds)
        return Cache::remember('admin_dashboard_stats', 300, function () {
            try {
                $today = Carbon::today();
                $startOfMonth = Carbon::now()->startOfMonth();
                $endOfMonth = Carbon::now()->endOfMonth();
                $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth();
                $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth();

                // Revenue Calculations
                $totalRevenue = (float) Payment::where('status', 'success')->sum('amount');
                $revenueThisMonth = (float) Payment::where('status', 'success')->whereBetween('created_at', [$startOfMonth, $endOfMonth])->sum('amount');
                $revenueLastMonth = (float) Payment::where('status', 'success')->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])->sum('amount');

                // Growth Calculation (Prevent Division by Zero)
                $growthPercentage = 0;
                if ($revenueLastMonth > 0) {
                    $growthPercentage = (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100;
                }

                // Top Courses (Handling the missing relationship)
                $topCourses = [];
                try {
                    // CHANGED: 'users' to 'students' (Ensure this exists in Course Model)
                    $topCourses = Course::withCount('students')
                        ->orderBy('students_count', 'desc')
                        ->take(4)
                        ->get(['id', 'title', 'website_price', 'thumbnail']); // changed 'price' to 'website_price'
                } catch (\Exception $e) {
                    Log::error('Dashboard Top Courses Error: ' . $e->getMessage());
                    $topCourses = []; // Return empty array if relation fails
                }

                return [
                    'total_users'           => User::role('Student')->count(),
                    'new_users_today'       => User::role('Student')->whereDate('created_at', $today)->count(),
                    'active_students'       => User::role('Student')->where('is_active', true)->count(),
                    'total_courses'         => Course::count(),
                    'active_courses'        => Course::where('is_published', true)->count(),
                    'total_revenue'         => $totalRevenue,
                    'revenue_growth'        => round($growthPercentage, 1),
                    'total_paid_commission' => (float) AffiliateCommission::paid()->sum('amount'),
                    'pending_commission'    => (float) AffiliateCommission::pending()->sum('amount'),
                    'sales_today'           => Payment::where('status', 'success')->whereDate('created_at', $today)->count(),
                    'recent_registrations'  => User::latest()->take(6)->get(['id', 'name', 'email', 'profile_picture', 'created_at']),
                    'top_courses'           => $topCourses,
                    'recent_transactions'   => Payment::latest()
                        ->take(6)
                        ->with('user:id,name,profile_picture')
                        ->get(['id', 'amount', 'status', 'created_at', 'user_id']),
                ];

            } catch (\Exception $e) {
                // FALLBACK: If anything critical fails, return 0s so dashboard loads
                Log::error('Dashboard Aggregation Critical Error: ' . $e->getMessage());

                return [
                    'total_users' => 0,
                    'new_users_today' => 0,
                    'active_students' => 0,
                    'total_courses' => 0,
                    'active_courses' => 0,
                    'total_revenue' => 0,
                    'revenue_growth' => 0,
                    'total_paid_commission' => 0,
                    'pending_commission' => 0,
                    'sales_today' => 0,
                    'recent_registrations' => [],
                    'top_courses' => [],
                    'recent_transactions' => [],
                ];
            }
        });
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

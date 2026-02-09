<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AffiliateCommission;
use App\Models\Setting;
use App\Models\User;
use App\Models\Course;
use App\Models\Bundle;
use Illuminate\Support\Facades\DB;
use App\Models\Payment;
use App\Models\VideoProgress;
use Carbon\Carbon;
use Exception;

class DashboardController extends Controller
{
    /**
     * Student Dashboard Index
     */
    // public function index()
    // {


    //     try {
    //         $user = Auth::user();
    //         if (!$user) {
    //             return redirect()->route('login')->with('error', 'Please login to access dashboard.');
    //         }
    //         $totalEarnings = $user->commissions()
    //             ->where('status', 'paid')
    //             ->sum('amount') ?? 0;

    //         $pendingEarnings = $user->commissions()
    //             ->where('status', 'pending')
    //             ->sum('amount') ?? 0;

    //         $totalReferrals = $user->referrals()->count();

    //         $recentReferrals = $user->referrals()
    //             ->select('id', 'name', 'email', 'created_at', 'is_active')
    //             ->latest()
    //             ->limit(5)
    //             ->get();

    //         $referralLink = $user->referral_code
    //             ? route('register', ['ref' => $user->referral_code])
    //             : 'Referral code not generated. Please contact support.';

    //         $commissionAmount = Setting::get('referral_commission_amount', 0);

    //         // Fetch Products for Link Generator
    //         $courses = Course::where('is_published', true)->select('id', 'title')->get();
    //         $bundles = Bundle::where('is_published', true)->select('id', 'title')->get();

    //         return view('dashboard', compact(
    //             'totalEarnings',
    //             'pendingEarnings',
    //             'totalReferrals',
    //             'recentReferrals',
    //             'referralLink',
    //             'commissionAmount',
    //             'user',
    //             'courses',
    //             'bundles'
    //         ));
    //     } catch (Exception $e) {
    //         Log::error("Dashboard Error for User ID " . Auth::id() . ": " . $e->getMessage());
    //         return response()->view('errors.500', [], 500);
    //     }
    // }

    public function index()
    {
        try {
            $user = Auth::user();

            // 1. Login Check
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login first.');
            }

            // ==========================================
            // PART 1: LEARNING DATA
            // ==========================================

            // Step A: Completed Lessons IDs
            // Check if VideoProgress table exists and fetch data
            $completedLessonIds = VideoProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            // Step B: Purchased Courses
            // Note: Hum sirf 'course_id' check kar rahe hain jo exist karta hai
            $myCourses = Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('course_id')
                ->with(['course.lessons', 'course.category'])
                ->latest()
                ->get()
                ->map(function ($payment) use ($completedLessonIds) {
                    $course = $payment->course;
                    if ($course) {
                        $totalLessons = $course->lessons->count();
                        $completedCount = $course->lessons->whereIn('id', $completedLessonIds)->count();

                        // Calculation
                        $course->progress_percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
                        $course->completed_lessons = $completedCount;
                        $course->total_lessons = $totalLessons;

                        return $course;
                    }
                    return null;
                })
                ->filter();

            // Step C: Purchased Bundles
            // FIX: 'bundle_id' column missing hone ki wajah se error aa raha tha.
            // Filhal hum empty collection bhej rahe hain taaki dashboard crash na ho.
            $myBundles = collect([]);

            /* // Jab aap 'bundle_id' column add kar lein, tab ise uncomment karein:
        $myBundles = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->whereNotNull('bundle_id')
            ->with('bundle')
            ->latest()
            ->get()
            ->map(function ($payment) {
                return $payment->bundle;
            })
            ->filter();
        */

            // ==========================================
            // PART 2: AFFILIATE DATA
            // ==========================================

            $totalEarnings = $user->commissions()->where('status', 'paid')->sum('amount') ?? 0;
            $pendingEarnings = $user->commissions()->where('status', 'pending')->sum('amount') ?? 0;
            $referralLink = $user->referral_code ? url('/register?ref=' . $user->referral_code) : '';

            // Chart Data (Last 7 Days)
            $chartLabels = [];
            $chartData = [];

            $earningsData = $user->commissions()
                ->where('created_at', '>=', Carbon::now()->subDays(6))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('total', 'date')
                ->toArray();

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $chartLabels[] = Carbon::now()->subDays($i)->format('d M');
                $chartData[] = isset($earningsData[$date]) ? (float) $earningsData[$date] : 0;
            }

            // Products for Link Generator
            $allCourses = Course::where('is_published', true)->select('id', 'title')->get();
            $allBundles = Bundle::where('is_published', true)->select('id', 'title')->get();

            return view('student.dashboard', compact(
                'user',
                'myCourses',
                'myBundles',
                'totalEarnings',
                'pendingEarnings',
                'referralLink',
                'chartLabels',
                'chartData',
                'allCourses',
                'allBundles'
            ));
        } catch (Exception $e) {
            // DEBUGGING: Ye screen par exact error dikhayega.
            // Kripya ye error message copy karke mujhe batayein.
            dd([
                'Error Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine()
            ]);

            Log::error("Dashboard Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard. Please contact support.');
        }
    }
}

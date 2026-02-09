<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Course;
use App\Models\Bundle;
use App\Models\Payment;
use App\Models\VideoProgress;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            /** @var User $user */
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login first.');
            }

            // --- LEARNING DATA ---
            $completedLessonIds = VideoProgress::where('user_id', $user->id)
                ->where('is_completed', true)
                ->pluck('lesson_id')
                ->toArray();

            $myCourses = Payment::where('user_id', $user->id)
                ->where('status', 'success')
                ->whereNotNull('course_id')
                ->with(['course.lessons', 'course.category'])
                ->latest()
                ->get()
                ->map(function ($payment) use ($completedLessonIds) {
                    $course = $payment->course;
                    if ($course) {
                        $total = $course->lessons->count();
                        $completed = $course->lessons->whereIn('id', $completedLessonIds)->count();
                        $course->progress_percent = $total > 0 ? round(($completed / $total) * 100) : 0;
                        $course->completed_lessons = $completed;
                        $course->total_lessons = $total;
                        return $course;
                    }
                })->filter();

            $myBundles = collect([]); // Placeholder

            // --- AFFILIATE DATA ---
            $totalEarnings = $user->commissions()->where('status', 'paid')->sum('amount') ?? 0;
            $pendingEarnings = $user->commissions()->where('status', 'pending')->sum('amount') ?? 0;
            $referralLink = $user->referral_code ? url('/register?ref=' . $user->referral_code) : '';

            // 7 Days Performance Chart
            $chartLabels = [];
            $chartData = [];
            $earningsData = $user->commissions()
                ->where('created_at', '>=', Carbon::now()->subDays(6))
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(amount) as total'))
                ->groupBy('date')
                ->pluck('total', 'date')->toArray();

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $chartLabels[] = Carbon::now()->subDays($i)->format('d M');
                $chartData[] = (float)($earningsData[$date] ?? 0);
            }

            $allCourses = Course::where('is_published', true)->select('id', 'title')->get();
            $allBundles = Bundle::where('is_published', true)->select('id', 'title')->get();

            return view('student.dashboard', compact(
                'user', 'myCourses', 'myBundles', 'totalEarnings', 'pendingEarnings',
                'referralLink', 'chartLabels', 'chartData', 'allCourses', 'allBundles'
            ));

        } catch (Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage());
            return abort(500, 'Something went wrong.');
        }
    }
}

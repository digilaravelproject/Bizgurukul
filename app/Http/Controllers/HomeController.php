<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LmsService;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    protected $lmsService;

    public function __construct(LmsService $lmsService)
    {
        $this->lmsService = $lmsService;
    }

    public function index(Request $request)
    {
        try {
            $courses = $this->lmsService->getFilteredCourses(['is_published' => 1]);
            return view('home', compact('courses'));
        } catch (Exception $e) {
            Log::error("HomeController Error [index]: " . $e->getMessage());
            return response()->view('errors.500', [], 500); // Or handle gracefully
        }
    }

    public function courses($id)
    {
        try {
            // Course ko fetch karein uske relations ke saath
            $course = $this->lmsService->getCourse($id);

            if (!$course) {
                abort(404, 'Course not found');
            }

            // Agar course kisi bundle se juda hai toh hum bundle information pass karenge
            $isBundleCourse = $course->bundles && $course->bundles->count() > 0;
            $bundle = $isBundleCourse ? $course->bundles->first() : null;

            return view('course_details', compact('course', 'isBundleCourse', 'bundle'));

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return abort(404, 'Course not found');
        } catch (Exception $e) {
            Log::error("HomeController Error [courses] for ID {$id}: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'Unable to load course details at this time.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LmsService;
use App\Services\BundleService;
use Illuminate\Support\Facades\Log;
use Exception;

class HomeController extends Controller
{
    protected $lmsService;
    protected $bundleService;

    public function __construct(LmsService $lmsService, BundleService $bundleService)
    {
        $this->lmsService = $lmsService;
        $this->bundleService = $bundleService;
    }

    public function index(Request $request)
    {
        try {
            $courses = $this->lmsService->getFilteredCourses(['is_published' => 1]);
            $bundles = $this->bundleService->getBundles(['is_published' => 1]);
            return view('web.home', compact('courses', 'bundles'));
        } catch (Exception $e) {
            Log::error("HomeController Error [index]: " . $e->getMessage());
            return response()->view('errors.500', [], 500); // Or handle gracefully
        }
    }

    public function variant(Request $request)
    {
        try {
            $courses = $this->lmsService->getFilteredCourses(['is_published' => 1]);
            $bundles = $this->bundleService->getBundles(['is_published' => 1]);
            return view('web.home-variant', compact('courses', 'bundles'));
        } catch (Exception $e) {
            Log::error("HomeController Error [variant]: " . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }

    public function about()
    {
        return view('web.about');
    }

    public function contact()
    {
        return view('web.contact');
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

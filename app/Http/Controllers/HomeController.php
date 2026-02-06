<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LmsService; // Service ko import karein

class HomeController extends Controller
{
    protected $lmsService;

    public function __construct(LmsService $lmsService)
    {
        $this->lmsService = $lmsService;
    }

    public function index(Request $request)
    {

        $courses = $this->lmsService->getFilteredCourses(['is_published' => 1]);

        return view('home', compact('courses'));
    }

    public function courses($id)
    {
        // Course ko fetch karein uske relations ke saath
        // Note: Humein check karna hoga ki 'bundles' relation aapke Course model mein defined hai ya nahi
        $course = $this->lmsService->getCourse($id);

        if (!$course) {
            abort(404, 'Course not found');
        }

        // Agar course kisi bundle se juda hai toh hum bundle information pass karenge
        // Maan lijiye aapka relation '$course->bundles' hai
        $isBundleCourse = $course->bundles && $course->bundles->count() > 0;
        $bundle = $isBundleCourse ? $course->bundles->first() : null;

        return view('course_details', compact('course', 'isBundleCourse', 'bundle'));
    }
}

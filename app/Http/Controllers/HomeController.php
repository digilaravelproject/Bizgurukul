<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LmsService;
use App\Services\BundleService;
use App\Models\Course;
use App\Models\Bundle;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Auth;

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

    public function terms()
    {
        return view('web.terms');
    }

    public function privacy()
    {
        return view('web.privacy');
    }

    public function refund()
    {
        return view('web.refund');
    }

    public function courseShow($slug)
    {
        try {
            // Find by slug or ID
            $course = Course::where('id', $slug)->first();

            // Re-fetch with relations
            if ($course) {
                $course = $this->lmsService->getCourse($course->id);
            }

            if (!$course) {
                abort(404, 'Course not found');
            }

            // check if linked to bundle for "Save More" offer
            $bundle = $course->bundles()->first();

            return view('web.course_details', compact('course', 'bundle'));

        } catch (Exception $e) {
            Log::error("HomeController Error [courseShow] for {$slug}: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'Unable to load course details.');
        }
    }

    public function bundleShow($slug)
    {
        try {
            $bundle = Bundle::where('slug', $slug)->orWhere('id', $slug)->first();

            if (!$bundle) {
                abort(404, 'Bundle not found');
            }

            // Load courses
            $bundle->load('courses');

            // Calculate effective price if user is logged in
            $user = Auth::user();
            $effectivePrice = $user ? $bundle->getEffectivePriceForUser($user) : $bundle->final_price;
            $isUpgrade = $user && $bundle->getUpgradeDiscountAmount($user) > 0;

            return view('web.bundle_details', compact('bundle', 'effectivePrice', 'isUpgrade'));

        } catch (Exception $e) {
            Log::error("HomeController Error [bundleShow] for {$slug}: " . $e->getMessage());
            return redirect()->route('home')->with('error', 'Unable to load bundle details.');
        }
    }

    public function courses($id)
    {
        return $this->courseShow($id);
    }
}

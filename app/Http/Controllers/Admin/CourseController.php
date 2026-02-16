<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LmsService;
use App\Services\CourseService; // Added
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    protected $lmsService;
    protected $courseService;

    public function __construct(LmsService $lmsService, CourseService $courseService)
    {
        $this->lmsService = $lmsService;
        $this->courseService = $courseService;
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courses = $this->courseService->getFilteredCourses($request->all());

                return view('admin.courses.partials.table', compact('courses'))->render();
            }
            $courses = $this->courseService->getFilteredCourses($request->all());
            $courses->appends($request->all());
            $categories = $this->lmsService->getCategories();

            return view('admin.courses.index', compact('courses', 'categories'));
        } catch (Exception $e) {
            return back()->with('error', 'Something went wrong.');
        }
    }

    public function create()
    {
        $categories = $this->lmsService->getCategories();

        return view('admin.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'website_price' => 'required|numeric|min:0',
            'affiliate_price' => 'required|numeric|lte:website_price',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
        ]);

        try {
            // Using CourseService
            $course = $this->courseService->createCourse($request->all());

            return redirect()->route('admin.courses.edit', ['course' => $course->id, 'tab' => 'lessons'])
                ->with('success', 'Course created successfully. Please add lessons.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $course = $this->courseService->getCourse($id);
            $categories = $this->lmsService->getCategories();
            $activeTab = $request->get('tab', 'basic');

            return view('admin.courses.edit', compact('course', 'categories', 'activeTab'));
        } catch (Exception $e) {
            return redirect()->route('admin.courses.index')->with('error', 'Course not found.');
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'website_price' => 'sometimes|numeric|min:0',
            'affiliate_price' => 'required|numeric|lte:website_price',
            'discount_value' => 'nullable|numeric|min:0',
            'discount_type' => 'nullable|in:fixed,percent',
            'commission_value' => 'nullable|numeric|min:0',
            'commission_type' => 'nullable|in:fixed,percent',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
            'demo_video' => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
            'is_published' => 'nullable|boolean',
            'certificate_enabled' => 'nullable|boolean',
            'certificate_type' => 'nullable|in:completion,quiz',
            'completion_percentage' => 'nullable|integer|min:0|max:100',
            'quiz_required' => 'nullable|boolean',
        ]);

        try {

            $data = $request->all();
            $data['is_published'] = $request->has('is_published') ? 1 : 0;
            $data['certificate_enabled'] = $request->has('certificate_enabled') ? 1 : 0;
            $data['quiz_required'] = $request->has('quiz_required') ? 1 : 0;

            $this->courseService->updateCourseDetails($id, $data);

           if ($request->redirect_tab === 'settings') {
            return redirect()->route('admin.courses.index')
                ->with('success', 'Course updated and finalized successfully!');
        }
        if ($request->has('redirect_tab')) {
            return redirect()->route('admin.courses.edit', ['id' => $id, 'tab' => $request->redirect_tab])
                ->with('success', 'Information updated.');
        }

            return back()->with('success', 'Course updated successfully.');
        } catch (Exception $e) {
            Log::info($e->getMessage());

            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->courseService->deleteCourse($id);

            return back()->with('success', 'Course and all related contents deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete course: '.$e->getMessage());
        }
    }

    public function getSubCategories($id)
    {
        return response()->json($this->lmsService->getSubCategories($id));
    }

    // --- LESSON & RESOURCE METHODS ---

    public function storeLesson(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|in:video,document',
            'video_file' => 'required_if:type,video|mimes:mp4,mov,avi,wmv|max:512000', // 500MB
            'document_file' => 'required_if:type,document|mimes:pdf,docx,zip|max:20480',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        try {
            $this->courseService->addLesson($id, $request->all());

            return redirect()->route('admin.courses.edit', ['course' => $id, 'tab' => 'lessons'])
                ->with('success', 'Lesson added. Video processing started in background.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Added: Destroy Lesson
    public function destroyLesson($id)
    {
        try {
            $this->courseService->deleteLesson($id);

            return back()->with('success', 'Lesson deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function storeResource(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|max:51200', // 50MB
        ]);

        try {
            $this->courseService->addResource($id, $request->all());

            return redirect()->route('admin.courses.edit', ['course' => $id, 'tab' => 'resources'])
                ->with('success', 'Resource added successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    // Added: Destroy Resource
    public function destroyResource($id)
    {
        try {
            $this->courseService->deleteResource($id);

            return back()->with('success', 'Resource deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\LmsService;
use Illuminate\Http\Request;
use Exception;

class CourseController extends Controller
{
    protected $lmsService;

    public function __construct(LmsService $lmsService)
    {
        $this->lmsService = $lmsService;
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                $courses = $this->lmsService->getFilteredCourses($request->all());
                return view('admin.courses.partials.table', compact('courses'))->render();
            }
            $courses = $this->lmsService->getFilteredCourses($request->all());
            $courses->appends($request->all());
            $categories = $this->lmsService->getCategories();
            return view('admin.courses.index', compact('courses', 'categories'));
        } catch (Exception $e) {
            return back()->with('error', "Something went wrong.");
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
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        try {
            $course = $this->lmsService->createCourse($request->all());
            return redirect()->route('admin.courses.edit', ['course' => $course->id, 'tab' => 'lessons'])
                ->with('success', 'Course created successfully. Please add lessons.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            $course = $this->lmsService->getCourse($id);
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
            'title'           => 'required|string|max:255',
            'category_id'     => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'discount_value'  => 'nullable|numeric|min:0',
            'discount_type'   => 'nullable|in:fixed,percent',
            'thumbnail'       => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'demo_video'      => 'nullable|mimes:mp4,mov,avi,wmv|max:51200',
            'is_published'    => 'nullable|boolean',
        ]);

        try {
            $data = $request->all();
            $data['is_published'] = $request->has('is_published') ? 1 : 0;
            $this->lmsService->updateCourseDetails($id, $data);
            return back()->with('success', 'Course updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->lmsService->deleteCourse($id);
            return back()->with('success', 'Course and all related contents deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete course: ' . $e->getMessage());
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
            'thumbnail' => 'nullable|image|max:1024',
        ]);

        try {
            $this->lmsService->addLesson($id, $request->all());
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
            $this->lmsService->deleteLesson($id);
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
            $this->lmsService->addResource($id, $request->all());
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
            $this->lmsService->deleteResource($id);
            return back()->with('success', 'Resource deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

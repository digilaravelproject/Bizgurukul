<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class CourseControllerOld extends Controller
{
    // public function index(Request $request)
    // {
    //     $query = Course::withCount('lessons')->orderBy('created_at', 'desc');
    //     if ($request->has('search') && $request->search != '') {
    //         $query->where('title', 'like', '%' . $request->search . '%');
    //     }
    //     $courses = $query->paginate(10);

    //     if ($request->ajax()) {
    //         return view('admin.lms.partials.table', compact('courses'))->render();
    //     }
    //     return view('admin.lms.index', compact('courses'));
    // }

    public function index(Request $request)
    {
        $search = $request->search;
        $tab = $request->tab; // Alpine.js se tab name aayega

        // 1. Single Courses
        $courseQuery = Course::withCount('lessons')->latest();
        if ($tab == 'courses' && $request->filled('search')) {
            $courseQuery->where('title', 'like', "%{$search}%");
        }
        $courses = $courseQuery->paginate(10, ['*'], 'courses_page');

        // 2. Bundles
        $bundleQuery = Bundle::with('courses')->latest();
        if ($tab == 'bundles' && $request->filled('search')) {
            $bundleQuery->where('title', 'like', "%{$search}%");
        }
        $bundles = $bundleQuery->paginate(10, ['*'], 'bundles_page');

        // 3. Lessons (Video HLS)
        $lessonQuery = Lesson::with('course')->latest();
        if ($tab == 'lessons' && $request->filled('search')) {
            $lessonQuery->where('title', 'like', "%{$search}%")
                ->orWhereHas('course', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
        }
        $lessons = $lessonQuery->paginate(10, ['*'], 'lessons_page');

        // AJAX Response: Sirf wahi partial bhejenge jiski zarurat hai
        if ($request->ajax()) {
            return response()->json([
                'courses' => view('admin.lms.partials.table', compact('courses'))->render(),
                'bundles' => view('admin.lms.partials.bundle_table', compact('bundles'))->render(),
                'lessons' => view('admin.lessons.partials.all_table', compact('lessons'))->render(),
            ]);
        }

        return view('admin.lms.index', compact('courses', 'bundles', 'lessons'));
    }

    /**
     * Store or Update Bundle Logic
     */
    /**
     * Bundle Store Logic with Exclusion Filter
     */
    /**
     * Store or Update Bundle
     */
    // Create Page: Sirf wahi courses jo kisi bundle mein nahi hain
    public function createBundle()
    {
        $availableCourses = Course::where('is_published', true)->get();
        return view('admin.lms.bundle_create', compact('availableCourses'));
    }

    // Edit Page: Isme bhi saare courses dikhenge
    public function editBundle($id)
    {
        $bundle = Bundle::with('courses')->findOrFail($id);
        $currentCourseIds = $bundle->courses->pluck('id')->toArray();
        $availableCourses = Course::where('is_published', true)->get();

        return view('admin.lms.bundle_edit', compact('bundle', 'availableCourses', 'currentCourseIds'));
    }

    // Store & Update Logic
    public function storeBundle(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        // Error logic: Agar edit mode mein saare courses hata diye
        if ($request->id && (!$request->has('course_ids') || count($request->course_ids) == 0)) {
            return back()
                ->withInput()
                ->with('error', 'Selection Required: You cannot remove all courses.');
        }

        DB::beginTransaction();
        try {
            $bundle = Bundle::updateOrCreate(['id' => $request->id], [
                'title' => $request->title,
                'price' => $request->price,
                'is_published' => $request->has('is_published'),
            ]);

            if ($request->has('course_ids')) {
                $bundle->courses()->sync($request->course_ids);
            }

            DB::commit();
            return redirect()->route('admin.courses.index')->with('success', 'Bundle updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }
    public function create()
    {
        $course = new Course();
        return view('admin.lms.create', compact('course'));
    }

    public function edit($id)
    {
        $course = Course::findOrFail($id);
        return view('admin.lms.create', compact('course'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'demo_video' => 'nullable|mimes:mp4,mov,avi|max:102400',
            'thumbnail' => 'nullable|image|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $data = [
                'title' => $request->title,
                'description' => $request->description,
                'price' => $request->price,
                'is_published' => $request->has('is_published'),
            ];

            $course = $request->id ? Course::findOrFail($request->id) : new Course();

            if ($request->hasFile('thumbnail')) {
                if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
                $data['thumbnail'] = $request->file('thumbnail')->store('courses/thumbnails', 'public');
            }

            if ($request->hasFile('demo_video')) {
                if ($course->demo_video_url) Storage::disk('public')->delete($course->demo_video_url);
                $data['demo_video_url'] = $request->file('demo_video')->store('courses/videos', 'public');
            }

            $course->fill($data)->save();
            DB::commit();

            return redirect()->route('admin.courses.index')->with('success', 'Course updated successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Course Store Error: " . $e->getMessage());
            return back()->withInput()->with('error', 'System Error: ' . $e->getMessage());
        }
    }

    /**
     * Delete a Bundle permanently.
     */
    public function deleteBundle($id)
    {
        try {
            // 1. Bundle find karein
            $bundle = Bundle::findOrFail($id);

            // 2. Pivot table data automatically delete ho jayega (cascade ki wajah se)
            // Aur bundle delete hote hi courses doosre bundles ke liye available ho jayenge.
            $bundle->delete();

            return redirect()->route('admin.courses.index')->with('success', 'Bundle removed successfully! Courses are now available for other packages.');
        } catch (\Exception $e) {
            // Agar koi error aati hai toh back bhejien message ke sath
            return back()->with('error', 'Delete Failed: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            $course = Course::findOrFail($id);
            if ($course->thumbnail) Storage::disk('public')->delete($course->thumbnail);
            if ($course->demo_video_url) Storage::disk('public')->delete($course->demo_video_url);
            $course->delete();
            return redirect()->route('admin.courses.index')->with('success', 'Course deleted successfully!');
        } catch (Exception $e) {
            return back()->with('error', 'Delete Failed: ' . $e->getMessage());
        }
    }
}

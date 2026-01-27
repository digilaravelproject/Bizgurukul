<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CourseController extends Controller
{
    /**
     * View the main LMS Management page (Index Table)
     */
    public function index(Request $request)
    {
        // Yahan .get() ki jagah .paginate(10) use karein
        $courses = Course::withCount('lessons')
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 results per page

        if ($request->ajax()) {
            return view('admin.lms.partials.table', compact('courses'))->render();
        }

        return view('admin.lms.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course (New Page)
     */
    public function create()
    {
        return view('admin.lms.create');
    }

    /**
     * STORE or UPDATE a Course (Redirect after save)
     */
    // storeCourse ko badal kar store kar dein
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
        ]);

        Course::updateOrCreate(
            ['id' => $request->id],
            [
                'title' => $request->title,
                'description' => $request->description,
            ]
        );

        return redirect()->route('admin.courses.index')->with('success', 'Course saved successfully!');
    }

    /**
     * FETCH all courses via AJAX (Keeping for table refresh if needed)
     */
    public function fetchCourses()
    {
        $courses = Course::withCount('lessons')->orderBy('created_at', 'desc')->get();
        return response()->json(['courses' => $courses]);
    }

    /**
     * DELETE a Course and its thumbnail
     */
    public function deleteCourse($id)
    {
        $course = Course::findOrFail($id);
        // Delete lessons associated with the course will happen via cascade in DB
        $course->delete();

        return response()->json(['success' => 'Course and its lessons deleted!']);
    }

    /**
     * FETCH Lessons for a specific course
     */
    public function fetchLessons($course_id)
    {
        $lessons = Lesson::where('course_id', $course_id)
            ->orderBy('order_column', 'asc')
            ->get();
        return response()->json(['lessons' => $lessons]);
    }

    /**
     * STORE or UPDATE a Lesson
     */
    public function storeLesson(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
        ]);

        $lesson = Lesson::updateOrCreate(
            ['id' => $request->id],
            [
                'course_id' => $request->course_id,
                'title' => $request->title,
                'order_column' => $request->order_column ?? 0,
            ]
        );

        return response()->json(['success' => 'Lesson saved successfully!', 'lesson' => $lesson]);
    }

    /**
     * DELETE a Lesson
     */
    public function deleteLesson($id)
    {
        $lesson = Lesson::findOrFail($id);

        // Delete files from storage if they exist
        if ($lesson->video_path) Storage::disk('public')->delete($lesson->video_path);
        if ($lesson->hls_path) Storage::disk('public')->deleteDirectory(dirname($lesson->hls_path));

        $lesson->delete();

        return response()->json(['success' => 'Lesson deleted!']);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;
use FFMpeg\Format\Video\X264;
use Exception;

class LessonController extends Controller
{
    public function allLessons(Request $request)
    {
        $lessons = Lesson::with('course')
            ->when($request->search, function ($query, $search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('course', function ($q) use ($search) {
                        $q->where('title', 'like', "%{$search}%");
                    });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        if ($request->ajax()) {
            return view('admin.lessons.partials.all_table', compact('lessons'))->render();
        }

        return view('admin.lessons.all_lessons', compact('lessons'));
    }

    public function create($course_id = 0)
    {
        $courses = Course::orderBy('title', 'asc')->get();
        // Agar ID 0 hai toh null pass hoga, warna specific course select hoga
        $selected_course = ($course_id != 0) ? Course::find($course_id) : null;

        return view('admin.lessons.create', compact('courses', 'selected_course'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'video' => 'nullable|mimes:mp4,mov,avi,wmv|max:102400',
        ]);

        try {
            // Log 1: Request Receive
            Log::info("Lesson creation started for Course ID: " . $request->course_id);

            $lesson = Lesson::updateOrCreate(
                ['id' => $request->id],
                [
                    'course_id' => $request->course_id,
                    'title' => $request->title,
                    'description' => $request->description,
                    'order_column' => $request->order_column ?? 0,
                ]
            );

            if ($request->hasFile('video')) {
                // Log 2: File Upload Start
                Log::info("Video upload detected for Lesson ID: " . $lesson->id);

                $video = $request->file('video');
                $filename = time() . '_' . $lesson->id;
                $originalPath = 'lessons/videos/' . $filename . '.' . $video->getClientOriginalExtension();

                Storage::disk('public')->put($originalPath, file_get_contents($video));
                $lesson->update(['video_path' => $originalPath]);

                // Log 3: FFmpeg Start
                Log::info("FFmpeg processing started for Lesson: " . $lesson->title);

                $hlsPath = 'lessons/hls/' . $filename . '/playlist.m3u8';

                FFMpeg::fromDisk('public')
                    ->open($originalPath)
                    ->export()
                    ->toDisk('public')
                    ->inFormat((new X264)->setKiloBitrate(500))
                    ->save($hlsPath);

                $lesson->update(['hls_path' => $hlsPath]);

                // Log 4: Success
                Log::info("HLS conversion successful for Lesson ID: " . $lesson->id);
            }

            return redirect()->route('admin.lessons.all')->with('success', 'Lesson saved and processed!');
        } catch (Exception $e) {
            // Log Error
            Log::error("Lesson Store Error: " . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $lesson = Lesson::findOrFail($id);
        $courses = Course::orderBy('title', 'asc')->get();
        $selected_course = $lesson->course;
        return view('admin.lessons.create', compact('lesson', 'courses', 'selected_course'));
    }

    public function destroy($id)
    {
        try {
            $lesson = Lesson::findOrFail($id);
            if ($lesson->video_path) Storage::disk('public')->delete($lesson->video_path);
            if ($lesson->hls_path) Storage::disk('public')->deleteDirectory(dirname($lesson->hls_path));
            $lesson->delete();

            return redirect()->route('admin.lessons.all')->with('success', 'Lesson deleted successfully!');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Delete failed!');
        }
    }
}

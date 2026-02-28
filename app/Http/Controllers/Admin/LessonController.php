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
                $video = $request->file('video');
                $filename = time() . '_' . $lesson->id;
                $originalPath = 'lessons/videos/' . $filename . '.' . $video->getClientOriginalExtension();

                Storage::disk('public')->put($originalPath, file_get_contents($video));
                $lesson->update(['video_path' => $originalPath]);

                $hlsPath = 'lessons/hls/' . $filename . '/playlist.m3u8';
                $keyFilename = $filename . '.key';
                $keyPath = 'lessons/keys/' . $keyFilename;
                $encryptionKey = random_bytes(16);


                // Save key to private storage
                Storage::disk('local')->put($keyPath, $encryptionKey);

                // We serve the key via a secure route
                $keyUrl = route('student.video.key', ['lesson' => $lesson->id]);

                // Debug Logging for FFmpeg
                Log::info("Starting FFMPEG Processing for Lesson ID: " . $lesson->id);
                Log::info("FFMPEG Binary Path configured: " . config('laravel-ffmpeg.ffmpeg.binaries'));
                Log::info("FFPROBE Binary Path configured: " . config('laravel-ffmpeg.ffprobe.binaries'));

                try {
                    FFMpeg::fromDisk('public')
                        ->open($originalPath)
                        ->export()
                        ->toDisk('public')
                        ->withEncryptionKey($encryptionKey, $keyUrl)
                        ->inFormat((new X264)->setKiloBitrate(800)) // Increased bitrate for better quality
                        ->save($hlsPath);

                    $lesson->update([
                        'hls_path' => $hlsPath,
                        'video_path' => $originalPath // Keep original as fallback if needed
                    ]);

                    Log::info("Encrypted HLS conversion successful for Lesson ID: " . $lesson->id);
                } catch (\Exception $e) {
                    Log::error("FFMpeg Processing Failed for Lesson ID: " . $lesson->id . " | Error: " . $e->getMessage());
                    Log::error($e->getTraceAsString());
                    throw $e;
                }
            }

            return redirect()->route('admin.courses.index')->with('success', 'Lesson saved and processed!');
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

            // 1. Delete Original Video File
            if ($lesson->video_path && Storage::disk('public')->exists($lesson->video_path)) {
                Storage::disk('public')->delete($lesson->video_path);
            }

            // 2. Delete HLS Folder (HLS generate hote waqt ek folder banta hai)
            if ($lesson->hls_path) {
                $hlsDirectory = dirname($lesson->hls_path);
                if (Storage::disk('public')->exists($hlsDirectory)) {
                    Storage::disk('public')->deleteDirectory($hlsDirectory);
                }
            }

            // 3. Delete from Database
            $lesson->delete();

            // Redirect back to LMS Index with the 'lessons' tab active
            return redirect()->route('admin.courses.index', ['tab' => 'lessons'])
                ->with('success', 'Video lesson and HLS streams removed successfully!');
        } catch (Exception $e) {
            Log::error("Lesson Delete Error: " . $e->getMessage());
            return redirect()->back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }
}

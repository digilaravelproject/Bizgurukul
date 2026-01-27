<?php

namespace App\Http\Controllers\Admin;



use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\VideoProgress;
use Illuminate\Http\Request;
use ProtoneMedia\LaravelFFMpeg\Support\FFMpeg;

class VideoController extends Controller
{
    // 1. Video Upload aur HLS Processing
    public function uploadVideo(Request $request)
    {
        $path = $request->file('video')->store('raw_videos', 'public');

        $lesson = Lesson::create([
            'title' => $request->title,
            'video_path' => $path
        ]);

        // Background Job me convert karna (For Smoothness)
        $this->processToHLS($lesson);

        return back()->with('success', 'Video uploading and processing started!');
    }

    private function processToHLS($lesson)
    {
        FFMpeg::fromDisk('public')
            ->open($lesson->video_path)
            ->exportForHLS()
            ->toDisk('public')
            ->save("lessons/{$lesson->id}/index.m3u8");

        $lesson->update(['hls_path' => "lessons/{$lesson->id}/index.m3u8"]);
    }

    // 2. Heartbeat API (Progress Save karne ke liye)
    public function updateHeartbeat(Request $request)
    {
        VideoProgress::updateOrCreate(
            ['user_id' => auth()->id(), 'lesson_id' => $request->lesson_id],
            ['last_watched_second' => $request->current_time]
        );
        return response()->json(['status' => 'saved']);
    }
}

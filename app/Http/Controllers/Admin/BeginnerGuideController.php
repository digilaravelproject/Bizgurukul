<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use App\Models\BeginnerGuideVideo;
use Illuminate\Support\Facades\Storage;

class BeginnerGuideController extends Controller
{
    /**
     * Display the beginner guide management page.
     */
    public function index(Request $request)
    {
        // show list of existing videos
        $videos = BeginnerGuideVideo::orderBy('order_column')->get();
        return view('admin.beginner-guide', compact('videos'));
    }

    /**
     * Store a new guide video record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category' => 'required|in:foundation,growth,scale',
            'video' => 'required|mimetypes:video/mp4,video/ogg,video/webm,video/mov|max:512000',
            'description' => 'nullable|string',
            'resources' => 'nullable|string',
            'order_column' => 'nullable|integer'
        ]);

        $file = $request->file('video');
        $path = $file->store('public/beginner-guide');

        BeginnerGuideVideo::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'resources' => $request->resources,
            'video_path' => $path,
            'order_column' => $request->order_column ?? 0,
        ]);

        return redirect()->route('admin.beginner-guide')->with('success', 'Video added successfully.');
    }

    /**
     * AJAX endpoint to save watching progress.
     */
    public function updateProgress(Request $request)
    {
        $seconds = (int) $request->input('seconds', 0);
        $completed = $request->boolean('completed');
        $videoId = $request->input('video_id');

        if ($videoId) {
            $progress = session('beginner_guide.progress', []);
            $progress[$videoId] = ['seconds' => $seconds, 'completed' => $completed];
            session(['beginner_guide.progress' => $progress]);
        }

        return response()->json(['ok' => true]);
    }

    /**
     * Delete a video entry.
     */
    public function destroy($id)
    {
        $video = BeginnerGuideVideo::findOrFail($id);
        if ($video->video_path) {
            Storage::delete($video->video_path);
        }
        $video->delete();
        return redirect()->route('admin.beginner-guide')->with('success', 'Video removed.');
    }
}

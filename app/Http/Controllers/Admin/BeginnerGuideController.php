<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
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
            'bunny_video_id' => 'nullable|string',
            'bunny_embed_url' => 'nullable|string',
            'description' => 'nullable|string',
            'resources' => 'nullable|string',
            'order_column' => 'nullable|integer'
        ]);

        $path = null;

        BeginnerGuideVideo::create([
            'title' => $request->title,
            'category' => $request->category,
            'description' => $request->description,
            'resources' => $request->resources,
            'video_path' => $path,
            'bunny_video_id' => $request->bunny_video_id,
            'bunny_embed_url' => $request->bunny_embed_url,
            'order_column' => $request->order_column ?? 0,
        ]);

        return redirect()->back()->with('success', 'Video record saved successfully.');
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

    /**
     * Display the resources page with tabs for Product Knowledge and Beginners Guide.
     */
    public function resources(Request $request)
    {
        $productKnowledge = \App\Models\CourseResource::orderBy('created_at', 'desc')->get();
        $beginnersGuide = BeginnerGuideVideo::orderBy('category')->orderBy('order_column')->get();

        return view('admin.resources', compact('productKnowledge', 'beginnersGuide'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Services\LmsService;
use App\Services\CourseService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

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
            'website_price' => 'nullable|numeric|min:0',
            'affiliate_price' => 'nullable|numeric|lte:website_price',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB
        ]);

        try {
            // Using CourseService
            $course = $this->courseService->createCourse($request->all());

            return redirect()->route('admin.courses.edit', ['id' => $course->id, 'tab' => 'lessons'])
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
            'affiliate_price' => 'sometimes|numeric|lte:website_price',
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

            // Only update these if they are present in the request (coming from settings tab)
            if ($request->has('is_published_trigger')) { // We can add a hidden trigger in the settings tab
                $data['is_published'] = $request->has('is_published') ? 1 : 0;
            }
            if ($request->has('certificate_trigger')) {
                $data['certificate_enabled'] = $request->has('certificate_enabled') ? 1 : 0;
            }
            if ($request->has('quiz_trigger')) {
                $data['quiz_required'] = $request->has('quiz_required') ? 1 : 0;
            }

            // Alternatively, use the redirect_tab as a hint
            if ($request->redirect_tab === 'settings') {
                $data['is_published'] = $request->has('is_published') ? 1 : 0;
                $data['certificate_enabled'] = $request->has('certificate_enabled') ? 1 : 0;
                $data['quiz_required'] = $request->has('quiz_required') ? 1 : 0;
            } else {
                // Remove them from data if not on settings tab to prevent overwriting
                unset($data['is_published'], $data['certificate_enabled'], $data['quiz_required']);
            }

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
            'bunny_video_id' => 'required_without:bunny_embed_url|nullable|string',
            'bunny_embed_url' => 'required_without:bunny_video_id|nullable|string',
            'document_file' => 'required_if:type,document|mimes:pdf,docx,zip|max:20480',
            'thumbnail' => 'nullable|image|max:5120',
        ]);

        try {
            $lesson = $this->courseService->addLesson($id, $request->all());

            if ($request->ajax() || $request->wantsJson()) {
                $html = view('admin.courses.partials._lesson_card', compact('lesson'))->render();
                return response()->json([
                    'success' => true,
                    'message' => 'Lesson added.',
                    'html' => $html,
                    'lesson' => $lesson
                ]);
            }

            return redirect()->route('admin.courses.edit', ['id' => $id, 'tab' => 'lessons'])
                ->with('success', 'Lesson added successfully!');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
            return back()->with('error', $e->getMessage());
        }
    }

    // Update lesson title and thumbnail inline from card
    public function updateLesson(Request $request, $id)
    {
        $request->validate([
            'title'     => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        try {
            $lesson = $this->courseService->updateLessonMeta($id, $request->all());

            if ($request->ajax() || $request->wantsJson()) {
                $html = view('admin.courses.partials._lesson_card', compact('lesson'))->render();
                return response()->json(['success' => true, 'html' => $html, 'message' => 'Lesson updated.']);
            }

            return back()->with('success', 'Lesson updated successfully!');
        } catch (Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 500);
            }
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

    /**
     * Proxy: Fetch lesson thumbnail from Bunny API server-side
     * Avoids CDN 403 / 404 issues by using the API key.
     */
    public function lessonThumbnail($id)
    {
        try {
            $lesson  = Lesson::findOrFail($id);
            $videoId = $lesson->getRawOriginal('bunny_video_id');
            $embed   = $lesson->getRawOriginal('bunny_embed_url');

            if (!$videoId && $embed) {
                if (preg_match('/\/embed\/[\d]+\/([a-f0-9\-]+)/i', $embed, $m)) {
                    $videoId = $m[1];
                } elseif (preg_match('/([a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})/i', $embed, $m)) {
                    $videoId = $m[1];
                }
            }

            if (!$videoId) {
                Log::error("Bunny Thumbnail Proxy: No video ID for Lesson #{$id}");
                abort(404);
            }

            $libId  = config('services.bunny.library_id');
            $apiKey = config('services.bunny.api_key');
            $host   = config('services.bunny.stream_host');

            // 1. Resolve Filename (1 Week cache)
            $thumbName = Cache::remember("bunny_tn_{$videoId}", 604800, function () use ($apiKey, $libId, $videoId) {
                /** @var \Illuminate\Http\Client\Response $r */
                $r = Http::withHeaders(['AccessKey' => $apiKey])->get("https://video.bunnycdn.com/library/{$libId}/videos/{$videoId}");
                return $r->successful() ? ($r->json()['thumbnailFileName'] ?? 'thumbnail.jpg') : 'thumbnail.jpg';
            });

            $url = "https://{$host}/{$videoId}/{$thumbName}";

            // 2. Resolve Binary (24 Hour cache)
            $cached = Cache::remember("bunny_bin_v5_{$videoId}", 86400, function () use ($url) {
                /** @var \Illuminate\Http\Client\Response $r */
                $r = Http::withHeaders([
                    'Referer' => request()->getSchemeAndHttpHost(),
                    'User-Agent' => 'Mozilla/5.0'
                ])->timeout(10)->get($url);

                return $r->successful() ? ['b' => base64_encode($r->body()), 't' => $r->header('Content-Type') ?? 'image/jpeg'] : null;
            });

            if ($cached) {
                return response(base64_decode($cached['b']), 200)
                    ->header('Content-Type', $cached['t'])
                    ->header('Cache-Control', 'public, max-age=604800');
            }
            abort(404);
        } catch (Exception $e) {
            Log::error("Bunny Proxy Exception: " . $e->getMessage());
            abort(404);
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

            return redirect()->route('admin.courses.edit', ['id' => $id, 'tab' => 'resources'])
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

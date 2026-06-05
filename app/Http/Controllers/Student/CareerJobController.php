<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\CareerJobExperience;
use App\Models\CareerJobLocation;
use App\Models\CareerJobSkill;
use App\Services\CareerJobService;
use Illuminate\Http\Request;

class CareerJobController extends Controller
{
    public function __construct(protected CareerJobService $service)
    {
    }

    public function index()
    {
        $locations = CareerJobLocation::all();
        $experiences = CareerJobExperience::all();
        $skills = CareerJobSkill::all();

        return view('student.career_jobs.index', compact('locations', 'experiences', 'skills'));
    }

    public function fetch(Request $request)
    {
        $filters = $request->only(['search', 'location', 'experience', 'skills']);
        $jobs = $this->service->getActiveJobs($filters);

        return view('student.career_jobs._list', compact('jobs'))->render();
    }

    public function show(int $id)
    {
        $job = $this->service->getJobById($id);
        
        if (!$job || !$job->is_active) {
            abort(404);
        }

        if (auth()->check()) {
            \App\Models\CareerJobClick::firstOrCreate([
                'career_job_id' => $job->id,
                'user_id' => auth()->id(),
                'action_type' => 'view',
            ]);
        }

        return view('student.career_jobs.show', compact('job'));
    }

    public function apply(int $id)
    {
        $job = $this->service->getJobById($id);
        
        if (!$job || !$job->is_active) {
            abort(404);
        }

        if (auth()->check()) {
            \App\Models\CareerJobClick::firstOrCreate([
                'career_job_id' => $job->id,
                'user_id' => auth()->id(),
                'action_type' => 'apply',
            ]);
        }

        $url = $job->apply_link;
        if (!str_starts_with($url, 'http://') && !str_starts_with($url, 'https://')) {
            $url = 'https://' . $url;
        }

        return redirect()->away($url);
    }
}

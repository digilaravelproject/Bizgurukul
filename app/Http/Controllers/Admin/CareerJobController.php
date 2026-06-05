<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCareerJobRequest;
use App\Http\Requests\UpdateCareerJobRequest;
use App\Models\CareerJobExperience;
use App\Models\CareerJobLocation;
use App\Models\CareerJobSalary;
use App\Models\CareerJobSkill;
use App\Models\CareerJobTitle;
use App\Services\CareerJobService;
use App\Models\Setting;
use Illuminate\Http\Request;

class CareerJobController extends Controller
{
    public function __construct(protected CareerJobService $service)
    {
    }

    public function index()
    {
        $jobs = $this->service->getPaginatedJobs(10, 'jobs_page');
        $analyticsJobs = $this->service->getPaginatedJobs(10, 'stats_page');
        return view('admin.career_jobs.index', compact('jobs', 'analyticsJobs'));
    }

    public function updateSettings(Request $request)
    {
        $request->validate([
            'career_how_to_build_resume_url' => 'nullable|url|max:500',
            'career_how_to_apply_url' => 'nullable|url|max:500',
        ]);

        Setting::set('career_how_to_build_resume_url', $request->input('career_how_to_build_resume_url'));
        Setting::set('career_how_to_apply_url', $request->input('career_how_to_apply_url'));

        return redirect()->route('admin.career-jobs.index')->with('success', 'Resource links updated successfully.');
    }

    public function create()
    {
        $titles = CareerJobTitle::all();
        $locations = CareerJobLocation::all();
        $experiences = CareerJobExperience::all();
        $salaries = CareerJobSalary::all();
        $skills = CareerJobSkill::all();

        return view('admin.career_jobs.create', compact('titles', 'locations', 'experiences', 'salaries', 'skills'));
    }

    public function store(StoreCareerJobRequest $request)
    {
        try {
            $this->service->createJob($request->validated());
            return redirect()->route('admin.career-jobs.index')->with('success', 'Job created successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error creating job: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $job = $this->service->getJobById($id);
        if (!$job) {
            return redirect()->route('admin.career-jobs.index')->with('error', 'Job not found.');
        }

        $titles = CareerJobTitle::all();
        $locations = CareerJobLocation::all();
        $experiences = CareerJobExperience::all();
        $salaries = CareerJobSalary::all();
        $skills = CareerJobSkill::all();

        return view('admin.career_jobs.edit', compact('job', 'titles', 'locations', 'experiences', 'salaries', 'skills'));
    }

    public function update(UpdateCareerJobRequest $request, int $id)
    {
        try {
            $this->service->updateJob($id, $request->validated());
            return redirect()->route('admin.career-jobs.index')->with('success', 'Job updated successfully.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error updating job: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->service->deleteJob($id);
            return redirect()->route('admin.career-jobs.index')->with('success', 'Job deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.career-jobs.index')->with('error', 'Error deleting job: ' . $e->getMessage());
        }
    }
}

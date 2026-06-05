<?php

namespace App\Services;

use App\Models\CareerJob;
use App\Repositories\CareerJobRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class CareerJobService
{
    public function __construct(protected CareerJobRepository $repository)
    {
    }

    public function getAllJobs()
    {
        return $this->repository->all();
    }

    public function getPaginatedJobs(int $perPage = 10, string $pageName = 'jobs_page')
    {
        return $this->repository->getPaginatedJobs($perPage, $pageName);
    }

    public function getActiveJobs(array $filters = [])
    {
        return $this->repository->getActiveJobs($filters);
    }

    public function getJobById(int $id)
    {
        return $this->repository->findById($id);
    }

    public function createJob(array $data)
    {
        DB::beginTransaction();
        try {
            $data = $this->resolveCustomMasterData($data);

            if (isset($data['company_logo'])) {
                $data['company_logo'] = $data['company_logo']->store('company_logos', 'public');
            }

            $data['posted_on'] = $data['posted_on'] ?? now()->toDateString();
            $data['is_active'] = $data['is_active'] ?? true;

            $job = $this->repository->create($data);

            if (isset($data['skills'])) {
                $this->repository->syncSkills($job, $data['skills']);
            }

            DB::commit();
            return $job;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating Career Job: ' . $e->getMessage());
            throw $e;
        }
    }

    public function updateJob(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $job = $this->repository->findByIdWithLock($id);
            if (!$job) {
                throw new \Exception('Job not found');
            }

            $data = $this->resolveCustomMasterData($data);

            if (isset($data['company_logo'])) {
                if ($job->company_logo) {
                    Storage::disk('public')->delete($job->company_logo);
                }
                $data['company_logo'] = $data['company_logo']->store('company_logos', 'public');
            }

            $this->repository->update($job, $data);

            if (isset($data['skills'])) {
                $this->repository->syncSkills($job, $data['skills']);
            }

            DB::commit();
            return $job;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating Career Job: ' . $e->getMessage());
            throw $e;
        }
    }

    public function deleteJob(int $id)
    {
        DB::beginTransaction();
        try {
            $job = $this->repository->findById($id);
            if ($job) {
                if ($job->company_logo) {
                    Storage::disk('public')->delete($job->company_logo);
                }
                $this->repository->delete($job);
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting Career Job: ' . $e->getMessage());
            throw $e;
        }
    }

    protected function resolveCustomMasterData(array $data): array
    {
        if (isset($data['career_job_title_id'])) {
            if (!is_numeric($data['career_job_title_id']) || !\App\Models\CareerJobTitle::where('id', $data['career_job_title_id'])->exists()) {
                $title = \App\Models\CareerJobTitle::firstOrCreate(['name' => trim($data['career_job_title_id'])]);
                $data['career_job_title_id'] = $title->id;
            }
        }
        if (isset($data['career_job_location_id'])) {
            if (!is_numeric($data['career_job_location_id']) || !\App\Models\CareerJobLocation::where('id', $data['career_job_location_id'])->exists()) {
                $loc = \App\Models\CareerJobLocation::firstOrCreate(['name' => trim($data['career_job_location_id'])]);
                $data['career_job_location_id'] = $loc->id;
            }
        }
        if (isset($data['career_job_experience_id'])) {
            if (!is_numeric($data['career_job_experience_id']) || !\App\Models\CareerJobExperience::where('id', $data['career_job_experience_id'])->exists()) {
                $exp = \App\Models\CareerJobExperience::firstOrCreate(['name' => trim($data['career_job_experience_id'])]);
                $data['career_job_experience_id'] = $exp->id;
            }
        }
        if (isset($data['career_job_salary_id']) && trim($data['career_job_salary_id']) !== '') {
            if (!is_numeric($data['career_job_salary_id']) || !\App\Models\CareerJobSalary::where('id', $data['career_job_salary_id'])->exists()) {
                $sal = \App\Models\CareerJobSalary::firstOrCreate(['name' => trim($data['career_job_salary_id'])]);
                $data['career_job_salary_id'] = $sal->id;
            }
        }
        if (isset($data['skills'])) {
            $skillIds = [];
            foreach ($data['skills'] as $skillItem) {
                if (is_numeric($skillItem) && \App\Models\CareerJobSkill::where('id', $skillItem)->exists()) {
                    $skillIds[] = (int) $skillItem;
                } else {
                    $newSkill = \App\Models\CareerJobSkill::firstOrCreate(['name' => trim($skillItem)]);
                    $skillIds[] = $newSkill->id;
                }
            }
            $data['skills'] = $skillIds;
        }

        return $data;
    }
}

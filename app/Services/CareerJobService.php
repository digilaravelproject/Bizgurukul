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
}

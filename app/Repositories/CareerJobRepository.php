<?php

namespace App\Repositories;

use App\Models\CareerJob;
use Illuminate\Support\Collection;

class CareerJobRepository
{
    public function all(): Collection
    {
        return CareerJob::with(['title', 'location', 'experience', 'salary', 'skills'])
            ->withCount(['views', 'applies'])
            ->get();
    }

    public function getPaginatedJobs(int $perPage = 10, string $pageName = 'jobs_page')
    {
        return CareerJob::with(['title', 'location', 'experience', 'salary', 'skills'])
            ->withCount(['views', 'applies'])
            ->latest('posted_on')
            ->paginate($perPage, ['*'], $pageName);
    }

    public function findById(int $id): ?CareerJob
    {
        return CareerJob::with(['title', 'location', 'experience', 'salary', 'skills'])->find($id);
    }

    public function findByIdWithLock(int $id): ?CareerJob
    {
        return CareerJob::lockForUpdate()->find($id);
    }

    public function create(array $data): CareerJob
    {
        return CareerJob::create($data);
    }

    public function update(CareerJob $job, array $data): bool
    {
        return $job->update($data);
    }

    public function delete(CareerJob $job): bool
    {
        return $job->delete();
    }

    public function syncSkills(CareerJob $job, array $skillIds): void
    {
        $job->skills()->sync($skillIds);
    }

    public function getActiveJobs(array $filters = []): Collection
    {
        $query = CareerJob::active()->with(['title', 'location', 'experience', 'salary', 'skills']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('title', function($t) use ($search) {
                      $t->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if (!empty($filters['location'])) {
            $query->where('career_job_location_id', $filters['location']);
        }

        if (!empty($filters['experience'])) {
            $query->where('career_job_experience_id', $filters['experience']);
        }

        if (!empty($filters['skills'])) {
            $query->whereHas('skills', function($q) use ($filters) {
                $q->whereIn('career_job_skills.id', $filters['skills']);
            });
        }

        return $query->latest('posted_on')->get();
    }
}

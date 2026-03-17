<?php

namespace App\Repositories;

use App\Models\Bundle;

class BundleRepository
{
    public function getAllBundles($filters = [])
    {
        $isPublished = $filters['is_published'] ?? null;

        return Bundle::query()
            ->withCount(['courses', 'childBundles']) // Performance optimization
            ->when(isset($filters['search']), function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%');
            })
            ->when($isPublished !== null, function ($q) use ($isPublished) {
                $q->where('is_published', $isPublished);
            })
            ->orderByDesc('preference_index')
            ->latest()
            ->paginate(10);
    }

    public function getBundleById($id)
    {
        return Bundle::with(['courses', 'childBundles'])->findOrFail($id);
    }

    public function createBundle(array $data)
    {
        return Bundle::create($data);
    }

    public function updateBundle(Bundle $bundle, array $data)
    {
        $bundle->update($data);
        return $bundle;
    }

    public function deleteBundle($id)
    {
        return Bundle::destroy($id);
    }

    public function syncItems(Bundle $bundle, array $courses = [], array $childBundles = [])
    {
        $bundle->courses()->sync($courses);
        $bundle->childBundles()->sync($childBundles);

        return $bundle;
    }
}

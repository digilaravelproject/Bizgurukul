<?php

namespace App\Services;

use App\Repositories\BundleRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class BundleService
{
    protected $repo;
    protected $disk;

    public function __construct(BundleRepository $repo)
    {
        $this->repo = $repo;
        $this->disk = config('filesystems.default');
    }

    public function getBundles(array $filters)
    {
        return $this->repo->getAllBundles($filters);
    }

    public function getBundle($id)
    {
        return $this->repo->getBundleById($id);
    }

    public function createBundle(array $data)
    {
        return DB::transaction(function () use ($data) {
            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                $data['thumbnail'] = $data['thumbnail']->store('bundles/thumbnails', $this->disk);
            }
            $data['slug'] = Str::slug($data['title']) . '-' . time();
            $bundle = $this->repo->createBundle($data);
            $courses = $data['courses'] ?? [];
            $childBundles = $data['bundles'] ?? [];
            $this->repo->syncItems($bundle, $courses, $childBundles);

            return $bundle;
        });
    }

    public function updateBundle($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            $bundle = $this->repo->getBundleById($id);

            if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                if ($old = $bundle->getRawOriginal('thumbnail')) {
                    Storage::disk($this->disk)->delete($old);
                }
                $data['thumbnail'] = $data['thumbnail']->store('bundles/thumbnails', $this->disk);
            } else {
                unset($data['thumbnail']);
            }
            $this->repo->updateBundle($bundle, $data);
            if (isset($data['courses']) || isset($data['bundles'])) {
                $courses = $data['courses'] ?? [];
                $childBundles = $data['bundles'] ?? [];
                $this->repo->syncItems($bundle, $courses, $childBundles);
            }

            return $bundle;
        });
    }

    public function deleteBundle($id)
    {
        return DB::transaction(function () use ($id) {
            $bundle = $this->repo->getBundleById($id);

            if ($path = $bundle->getRawOriginal('thumbnail')) {
                Storage::disk($this->disk)->delete($path);
            }

            return $this->repo->deleteBundle($id);
        });
    }
}

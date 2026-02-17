<?php

namespace App\Services;

use App\Repositories\BundleRepository;
use App\Services\MediaProcessingService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;

class BundleService
{
    protected $repo;
    protected $mediaService;
    protected $disk;

    public function __construct(BundleRepository $repo, MediaProcessingService $mediaService)
    {
        $this->repo = $repo;
        $this->mediaService = $mediaService;
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
            try {
                if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                    $data['thumbnail'] = $this->mediaService->compressAndConvertToWebP($data['thumbnail'], 'bundles/thumbnails');
                }
                $data['slug'] = Str::slug($data['title']) . '-' . time();
                $data = $this->calculatePricing($data);
                $bundle = $this->repo->createBundle($data);
                $courses = $data['courses'] ?? [];
                $childBundles = $data['bundles'] ?? [];
                $this->repo->syncItems($bundle, $courses, $childBundles);

                return $bundle;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Bundle Creation Failed: " . $e->getMessage(), [
                    'data' => $data,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
        });
    }

    public function updateBundle($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            try {
                $bundle = $this->repo->getBundleById($id);

                if (isset($data['thumbnail']) && $data['thumbnail'] instanceof UploadedFile) {
                    if ($old = $bundle->getRawOriginal('thumbnail')) {
                        Storage::disk($this->disk)->delete($old);
                    }
                    $data['thumbnail'] = $this->mediaService->compressAndConvertToWebP($data['thumbnail'], 'bundles/thumbnails');
                } else {
                    unset($data['thumbnail']);
                }

                // 3. Price Calculation
                // Use getRawOriginal for prices to avoid accessor interference if any
                $mergedData = array_merge($bundle->getAttributes(), $data);
                $calculatedData = $this->calculatePricing($mergedData);

                $data['final_price'] = $calculatedData['final_price'] ?? $bundle->final_price;

                $this->repo->updateBundle($bundle, $data);

                // Always sync, even if keys are missing (handles detaching all)
                $courses = $data['courses'] ?? [];
                $childBundles = $data['bundles'] ?? [];
                $this->repo->syncItems($bundle, $courses, $childBundles);

                return $bundle;
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error("Bundle Update Failed: " . $e->getMessage(), [
                    'id' => $id,
                    'data' => $data,
                    'trace' => $e->getTraceAsString()
                ]);
                throw $e;
            }
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

    private function calculatePricing(array $data)
    {
        $price = (float) ($data['website_price'] ?? 0);
        $discountValue = (float) ($data['discount_value'] ?? 0);
        $discountType = $data['discount_type'] ?? '';

        $finalPrice = $price;

        if ($discountType === 'percentage') {
            $finalPrice = $price - ($price * ($discountValue / 100));
        } elseif ($discountType === 'flat') {
            $finalPrice = $price - $discountValue;
        } else {
            // No discount or unknown type, final price is website price
            $finalPrice = $price;
        }

        $data['final_price'] = max(0, round($finalPrice, 2));

        return $data;
    }
}

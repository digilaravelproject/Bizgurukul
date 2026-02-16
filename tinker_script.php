<?php
try {
    echo "--- Tinker Verification Start ---\n";
    $repo = new \App\Repositories\BundleRepository();
    $media = new \App\Services\MediaProcessingService();
    $service = new \App\Services\BundleService($repo, $media);

    $data = [
        'title' => 'Tinker Test Bundle',
        'description' => 'Test Desc',
        'website_price' => 100.00,
        'affiliate_price' => 80.00,
        'is_published' => 1
    ];

    $b = $service->createBundle($data);
    echo "Bundle created with ID: " . $b->id . "\n";

    $b = $service->updateBundle($b->id, ['website_price' => 200.00]);
    echo "Updated Price: " . $b->website_price . "\n";

    if ($b->website_price == 200.00) {
        echo "[PASS] Price Update Verified\n";
    }

    $service->deleteBundle($b->id);
    echo "[PASS] Bundle Deleted\n";
    echo "--- Tinker Verification End ---\n";
} catch (\Exception $e) {
    echo "[FAIL] " . $e->getMessage() . "\n";
}

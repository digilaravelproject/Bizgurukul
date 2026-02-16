<?php

use App\Models\Bundle;
use App\Services\BundleService;
use App\Services\MediaProcessingService;
use App\Repositories\BundleRepository;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

ob_start();

echo "--- Starting Bundle Verification ---\n";

try {
    DB::beginTransaction();

    $repo = new BundleRepository();
    $mediaService = new MediaProcessingService();
    $service = new BundleService($repo, $mediaService);

    // 1. Create Bundle
    $data = [
        'title' => 'Test Bundle ' . time(),
        'description' => '<p>Rich Text Description</p>',
        'website_price' => 1000.00,
        'affiliate_price' => 800.00,
        'discount_type' => 'percentage',
        'discount_value' => 10.00,
        'commission_type' => 'flat',
        'commission_value' => 50.00,
        'is_published' => 1,
    ];

    echo "Creating Bundle...\n";
    $bundle = $service->createBundle($data);

    if ($bundle && $bundle->exists) {
        echo "[PASS] Bundle created with ID: {$bundle->id}\n";
    } else {
        throw new Exception("Bundle creation failed");
    }

    // 2. Verify Data
    $fresh = Bundle::find($bundle->id);
    if (
        $fresh->website_price == 1000.00 &&
        $fresh->affiliate_price == 800.00 &&
        $fresh->discount_type === 'percentage' &&
        $fresh->commission_value == 50.00
    ) {
        echo "[PASS] New fields verified in DB.\n";
    } else {
        throw new Exception("Data verification failed: " . json_encode($fresh->toArray()));
    }

    // 3. Update Bundle
    echo "Updating Bundle...\n";
    $updateData = [
        'title' => 'Updated Bundle Title',
        'website_price' => 1500.00,
        'discount_value' => 20.00
    ];
    $updated = $service->updateBundle($bundle->id, $updateData);

    if ($updated->title === 'Updated Bundle Title' && $updated->website_price == 1500.00) {
        echo "[PASS] Update successful.\n";
    } else {
        throw new Exception("Update failed");
    }

    // 4. Cleanup
    $service->deleteBundle($bundle->id);
    echo "[PASS] Bundle deleted (cleanup).\n";

    DB::commit();
    echo "--- Verification Successful ---\n";

} catch (Exception $e) {
    DB::rollBack();
    echo "[FAIL] Verification failed: " . $e->getMessage() . "\n";
}

$output = ob_get_clean();
file_put_contents('verification_log.txt', $output);
echo $output;

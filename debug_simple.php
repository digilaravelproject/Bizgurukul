<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\DB;

try {
    $commission = AffiliateCommission::latest()->first();

    if (!$commission) {
        echo "No commissions found.\n";
        exit;
    }

    echo "Commission ID: {$commission->id}\n";
    echo "Ref Type: {$commission->reference_type}\n";
    echo "Ref ID: {$commission->reference_id}\n";
    echo "Notes: {$commission->notes}\n";

    $types = DB::table('affiliate_commissions')
        ->select('reference_type', DB::raw('count(*) as count'))
        ->groupBy('reference_type')
        ->get();

    echo "\nDistribution:\n";
    foreach ($types as $t) {
        echo "- " . ($t->reference_type ?: 'NULL') . ": " . $t->count . "\n";
    }

    // Check relationship
    $ref = $commission->reference;
    if ($ref) {
        echo "\nRef Class: " . get_class($ref) . "\n";
        if (isset($ref->bundle_id)) {
            echo "Bundle ID: " . ($ref->bundle_id ?: 'NULL') . "\n";
        }
    } else {
        echo "\nRef NOT LOADED\n";
    }

} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

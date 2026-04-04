<?php

use App\Models\User;
use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

// Let's check the first user who has commissions
$commission = AffiliateCommission::first();

if (!$commission) {
    echo "No commissions found in the database.\n";
    exit;
}

echo "Sample Commission ID: " . $commission->id . "\n";
echo "Reference Type: " . $commission->reference_type . "\n";
echo "Reference ID: " . $commission->reference_id . "\n";
echo "Notes: " . $commission->notes . "\n";

if ($commission->reference) {
    echo "Reference Loaded: " . get_class($commission->reference) . "\n";
    if ($commission->reference instanceof \App\Models\Payment) {
        echo "Payment Bundle ID: " . ($commission->reference->bundle_id ?? 'NULL') . "\n";
        echo "Payment Course ID: " . ($commission->reference->course_id ?? 'NULL') . "\n";
    }
} else {
    echo "Reference could not be loaded.\n";
}

// Check distribution of reference types
$types = DB::table('affiliate_commissions')
    ->select('reference_type', DB::raw('count(*) as count'))
    ->groupBy('reference_type')
    ->get();

echo "\nReference Type Distribution:\n";
foreach ($types as $type) {
    echo "- " . ($type->reference_type ?: 'NULL') . ": " . $type->count . "\n";
}

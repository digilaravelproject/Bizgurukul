<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$output = "";

try {
    $totalUsers = App\Models\User::count();
    $activeUsers = App\Models\User::where('is_active', true)->count();
    $sampleUser = App\Models\User::where('is_active', true)->first();

    $output .= "Total Users: $totalUsers\n";
    $output .= "Active Users: $activeUsers\n";

    if ($sampleUser) {
        $output .= "Sample Active User: " . $sampleUser->name . " (ID: " . $sampleUser->id . ")\n";
    } else {
        $output .= "No active users found.\n";
    }

    $recentTxn = App\Models\Payment::latest()->take(1)->get();
    $output .= "Recent Transaction Count: " . $recentTxn->count() . "\n";

} catch (\Exception $e) {
    $output .= "Error: " . $e->getMessage() . "\n";
}

file_put_contents(__DIR__ . '/verify_result.txt', $output);
echo "Done.";

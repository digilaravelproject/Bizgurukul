<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $payment = App\Models\Payment::latest()->first();
    echo "Payment Data:\n";
    if ($payment) {
        echo "Amount: " . $payment->amount . "\n";
        echo "Status: " . $payment->status . "\n";
        echo "Created At: " . $payment->created_at . "\n";
    } else {
        echo "No payments found.\n";
    }

    echo "\n----------------\n";

    $commission = App\Models\AffiliateCommission::latest()->first();
    echo "Commission Data:\n";
    if ($commission) {
        echo "Amount: " . $commission->amount . "\n";
        echo "Status: " . $commission->status . "\n";
    } else {
        echo "No commissions found.\n";
    }

} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

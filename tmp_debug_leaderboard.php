<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\AffiliateCommission;
use Illuminate\Support\Facades\DB;

$total = AffiliateCommission::all()->count();
$paid = AffiliateCommission::where('status', 'paid')->count();
$pending = AffiliateCommission::where('status', 'pending')->count();
$others = AffiliateCommission::whereNotIn('status', ['paid', 'pending'])->count();

echo "Total: $total\n";
echo "Paid: $paid\n";
echo "Pending: $pending\n";
echo "Others: $others\n";

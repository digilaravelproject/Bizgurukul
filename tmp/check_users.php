<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$count = \App\Models\User::withTrashed()->count();
$trashed = \App\Models\User::onlyTrashed()->count();
echo "Total Users (including trashed): {$count}\n";
echo "Trashed Users: {$trashed}\n";

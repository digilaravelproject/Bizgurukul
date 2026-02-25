<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$exists = \Illuminate\Support\Facades\Schema::hasTable('settings');
if ($exists) {
    echo "Table SETTINGS EXISTS\n";
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing('settings');
    echo "COLUMNS: " . implode(', ', $columns) . "\n";
} else {
    echo "Table SETTINGS DOES NOT EXIST\n";
}

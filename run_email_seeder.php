<?php
// Temporary seeder runner script — delete after use
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$seeder = new Database\Seeders\EmailTemplateSeeder();
$seeder->setContainer($app);
$seeder->run();

echo "EmailTemplateSeeder ran successfully!\n";
echo "Total templates: " . App\Models\EmailTemplate::count() . "\n";

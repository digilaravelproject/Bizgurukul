<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "MAIL_MAILER from config: " . config('mail.default') . "\n";
echo "MAIL_MAILER from env helper: " . env('MAIL_MAILER') . "\n";
echo "APP_ENV: " . config('app.env') . "\n";
echo "LOG_CHANNEL: " . config('logging.default') . "\n";

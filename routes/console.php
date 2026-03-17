<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Ensure the queue worker is always running and restart if an idle one died
// --stop-when-empty ensures it terminates so Cron can respawn a fresh instance, freeing memory leaks.
Schedule::command('queue:work --stop-when-empty --timeout=7200 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('wallet:process')
    ->everyMinute()
    ->withoutOverlapping();

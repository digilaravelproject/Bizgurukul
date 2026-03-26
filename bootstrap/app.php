<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->validateCsrfTokens(except: [
            'webhook/razorpay',
        ]);

        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'check.purchase' => \App\Http\Middleware\CheckPurchaseStatus::class,
            '2fa' => \App\Http\Middleware\TwoFactorMiddleware::class,
        ]);

        $middleware->web(append: [\App\Http\Middleware\TrackReferral::class,]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
    })
    ->withSchedule(function ($schedule) {
        $schedule->command('queue:work --stop-when-empty')->everyMinute();
    })
    ->create();

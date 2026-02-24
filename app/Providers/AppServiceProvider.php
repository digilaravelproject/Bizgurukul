<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        view()->composer('layouts.admin', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check() && \Illuminate\Support\Facades\Auth::user()->role === 'Admin') {
                $pendingKycCount = \App\Models\User::whereHas('kyc', function($q) {
                    $q->where('status', 'pending');
                })->count();
                $pendingBankCount = \App\Models\BankDetail::where('status', 'pending')->count()
                                   + \App\Models\BankUpdateRequest::where('status', 'pending')->count();

                $view->with('pendingVerificationsCount', $pendingKycCount + $pendingBankCount);
            }
        });
    }
}

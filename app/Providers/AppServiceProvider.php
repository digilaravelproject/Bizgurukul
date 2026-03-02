<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Admin') ? true : null;
        });

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

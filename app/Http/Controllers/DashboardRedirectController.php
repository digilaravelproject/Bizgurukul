<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardRedirectController extends Controller
{
    /**
     * Handle the smart redirect to the appropriate dashboard.
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->hasRole('Admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        // Fallback for users with no specific role or unexpected roles
        return redirect()->route('home')->with('error', 'Dashboard not found for your account type.');
    }
}

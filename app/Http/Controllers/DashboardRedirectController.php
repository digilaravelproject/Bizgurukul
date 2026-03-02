<?php

namespace App\Http\Controllers;

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

        // 1. Administrative Check: Admin role, Has Permissions, or has ANY role other than 'Student'
        // This ensures custom roles (Moderate, Editor, etc.) go to the Admin side.
        $hasAdminRole = $user->hasRole('Admin');
        $hasPermissions = $user->permissions->count() > 0 || $user->getPermissionsViaRoles()->count() > 0;
        $hasCustomRole = $user->roles->where('name', '!=', 'Student')->count() > 0;

        if ($hasAdminRole || $hasPermissions || $hasCustomRole) {
            return redirect()->route('admin.dashboard');
        }

        // 2. Student Check
        if ($user->hasRole('Student')) {
            return redirect()->route('student.dashboard');
        }

        // 3. Fallback (If no roles assigned, default to student area)
        return redirect()->route('student.dashboard');
    }
}

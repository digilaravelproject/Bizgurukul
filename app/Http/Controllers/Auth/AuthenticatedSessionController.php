<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle the "Smart Login" email check.
     */
    public function checkEmail(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $email = $request->email;

        // Scenario A: Registered User
        if (\App\Models\User::where('email', $email)->exists()) {
            return response()->json(['status' => 'user']);
        }

        // Scenario C: Lead (Drop-off)
        $lead = \App\Models\Lead::where('email', $email)->first();
        if ($lead) {
            // Logic to restore session/context will be handled in Phase 2 controller
            // For now, redirect to Phase 2
            return response()->json([
                'status' => 'lead',
                'redirect_url' => route('register.phase2', ['lead_id' => $lead->id])
            ]);
        }

        // Scenario B: New User
        return response()->json([
            'status' => 'new',
            'redirect_url' => route('register', ['email' => $email])
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate(); // This will throw exception if credentials fail

        $user = \App\Models\User::where('email', $request->email)->first();

        // Check if user is Admin and has 2FA enabled
        if ($user && $user->hasRole('Admin') && $user->hasTwoFactorEnabled()) {
            Auth::logout(); // Logout for now, they need to verify 2FA
            
            $request->session()->put('auth.2fa_user_id', $user->id);
            $request->session()->put('auth.2fa_remember', $request->boolean('remember'));

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        if ($user->hasRole('Student')) {
            return redirect()->intended(route('student.courses.index'));
        }

        return redirect()->intended(route('dashboard'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

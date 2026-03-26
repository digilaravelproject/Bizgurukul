<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // If user is admin and has 2FA enabled, but hasn't verified this session
        if ($user && $user->hasRole('Admin') && $user->hasTwoFactorEnabled()) {
            if (!$request->session()->has('auth.2fa_verified')) {
                // If they are logged in but didn't go through the 2FA challenge, 
                // we should probably log them out or redirect to challenge if we have their ID
                // But usually, store() in AuthenticatedSessionController handles the redirect.
                // This is a safety net.
                
                Auth::guard('web')->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                
                return redirect()->route('login')->withErrors(['email' => 'Please login again and verify your 2FA code.']);
            }
        }

        return $next($request);
    }
}

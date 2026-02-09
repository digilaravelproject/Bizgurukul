<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Payment;

class CheckPurchaseStatus
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        // Admins strictly bypass this check
        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        // Check for any successful purchase
        $hasPurchased = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->exists();

        if (!$hasPurchased) {
            // Routes accessible to users who haven't purchased yet
            $allowedRoutes = [
                'student.product_selection',
                'razorpay.*',
                'logout',
                'profile.*',
                'verification.*',
                'password.*',
            ];

            if ($request->routeIs($allowedRoutes)) {
                return $next($request);
            }

            return redirect()->route('student.product_selection');
        }

        return $next($request);
    }
}

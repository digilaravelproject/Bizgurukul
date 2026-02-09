<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Payment;

class CheckPurchaseStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin should bypass this check
        if ($user->hasRole('Admin')) {
            return $next($request);
        }

        // Check if user has any successful payment
        // We can optimize this by caching or adding a 'has_purchased' flag to user table later.
        $hasPurchased = Payment::where('user_id', $user->id)
            ->where('status', 'success')
            ->exists();

        if (!$hasPurchased) {
            // Allow access to the product selection page and related routes (like payment, logout)
            if ($request->routeIs('student.product_selection', 'razorpay.*', 'logout', 'profile.*')) { // Added profile.* just in case they need to edit profile before buying
                return $next($request);
            }

            return redirect()->route('student.product_selection');
        }

        // If user HAS purchased, they shouldn't necessarily be blocked from product selection,
        // but if they try to access it 'fresh' maybe redirect to dashboard?
        // User said: "agr mene koi course purchase nhi kiyha hain to login krne ke baad bhi mujeh woi 2nd wala page show hoaga jab tak me kuch buy na krlu"
        // This implies if they HAVE bought, they go to dashboard.

        // If they access the selection page explicitly (e.g. to buy MORE), allow it.
        // But if they are just logging in, the RedirectIfAuthenticated logic usually handles the dashboard redirect.
        // This middleware is mainly to *protect* dashboard from unpaid users.

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Cookie;
use App\Models\Setting;

class TrackReferral
{
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Process Referral Code if parameter exists
        if ($request->has('ref')) {
            $referralCode = $request->query('ref');

            // Basic Validation
            if (strlen($referralCode) <= 20) {

                // Get Settings (Defaults)
                $isActive = Setting::get('referral_system_active', '1');

                if ($isActive === '1') {
                    $days = (int) Setting::get('referral_cookie_expiry_days', 30);
                    $minutes = $days * 24 * 60;

                    // Queue Cookie
                    Cookie::queue('referral_code', $referralCode, $minutes);

                    // Track Visit (New Logic)
                    try {
                        // Find affiliate
                        $affiliate = \App\Models\User::where('referral_code', $referralCode)->first();

                        if ($affiliate) {
                            \App\Models\ReferralVisit::create([
                                'affiliate_id' => $affiliate->id,
                                'ip_address' => $request->ip(),
                                'user_agent' => $request->userAgent(),
                                'landing_url' => $request->fullUrl(),
                            ]);
                        }
                    } catch (\Exception $e) {
                        // Silent fail for tracking to not disrupt user
                    }
                }
            }
        }

        // 2. Redirect Logic for Specific Product Links (Guest Users)
        // If user is accessing a Course or Bundle page AND has a referral code (in URL or Cookie)
        // AND is not logged in -> Redirect to Register
        if (!\Illuminate\Support\Facades\Auth::check()) {

            // Check if it's a product page
            $isProductPage = $request->routeIs('course.show') || $request->routeIs('bundles.show') || $request->is('course/*') || $request->is('coursesp/*');

            // Check if referral exists (Param has highest priority, then Cookie)
            $hasReferral = $request->has('ref') || $request->hasCookie('referral_code');

            if ($isProductPage && $hasReferral) {
                // Store intended URL to redirect back after registration
                session(['url.intended' => $request->fullUrl()]);

                // Redirect to Register
                return redirect()->route('register');
            }
        }

        return $next($request);
    }
}

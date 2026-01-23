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
        if ($request->has('ref')) {

            if (Setting::get('referral_system_active') === '1') {

                $referralCode = $request->query('ref');
                if (strlen($referralCode) <= 20) {

                    $days = (int) Setting::get('referral_cookie_expiry_days', 30);
                    $minutes = $days * 24 * 60;

                    Cookie::queue('referral_code', $referralCode, $minutes);
                }
            }
        }

        return $next($request);
    }
}

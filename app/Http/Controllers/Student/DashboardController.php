<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\AffiliateCommission;
use App\Models\Setting;
use App\Models\User;
use Exception;

class DashboardController extends Controller
{
    /**
     * Student Dashboard Index
     */
    public function index()
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return redirect()->route('login')->with('error', 'Please login to access dashboard.');
            }
            $totalEarnings = $user->commissions()
                ->where('status', 'paid')
                ->sum('amount') ?? 0;

            $pendingEarnings = $user->commissions()
                ->where('status', 'pending')
                ->sum('amount') ?? 0;

            $totalReferrals = $user->referrals()->count();

            $recentReferrals = $user->referrals()
                ->select('id', 'name', 'email', 'created_at', 'is_active')
                ->latest()
                ->limit(5)
                ->get();

            $referralLink = $user->referral_code
                ? route('register', ['ref' => $user->referral_code])
                : 'Referral code not generated. Please contact support.';
            $commissionAmount = Setting::get('referral_commission_amount', 0);

            return view('dashboard', compact(
                'totalEarnings',
                'pendingEarnings',
                'totalReferrals',
                'recentReferrals',
                'referralLink',
                'commissionAmount',
                'user'
            ));

        } catch (Exception $e) {
            Log::error("Dashboard Error for User ID " . Auth::id() . ": " . $e->getMessage());
            return response()->view('errors.500', [], 500);
        }
    }
}

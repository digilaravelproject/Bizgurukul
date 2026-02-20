<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\AffiliateCommission;
use App\Models\Lead;
use App\Models\Bundle;
use Illuminate\Support\Facades\Log;

class AffiliateController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();

            // 1. Statistics
            $totalEarnings = AffiliateCommission::where('affiliate_id', $user->id)->sum('amount');
            $thisMonthEarnings = AffiliateCommission::where('affiliate_id', $user->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount');

            $totalReferrals = User::where('referred_by', $user->id)->count();
            $todaysEarnings = AffiliateCommission::where('affiliate_id', $user->id)
                ->whereDate('created_at', now()->today())
                ->sum('amount');

            // Recent Commissions
            $recentCommissions = AffiliateCommission::where('affiliate_id', $user->id)
                ->with(['referredUser', 'reference']) // Expecting reference to be Bundle or Course
                ->latest()
                ->take(5)
                ->get();

            return view('student.affiliate.dashboard', compact('totalEarnings', 'thisMonthEarnings', 'totalReferrals', 'todaysEarnings', 'recentCommissions'));
        } catch (\Exception $e) {
            Log::error("Error loading affiliate dashboard for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading your dashboard.');
        }
    }

    public function leads()
    {
        try {
            $user = Auth::user();

            // Leads are users who started registration but haven't paid yet.
            // Using a custom page name 'leads_page' to separate from 'referrals_page'
            $leads = Lead::where('referral_code', $user->referral_code)
                ->latest()
                ->paginate(15, ['*'], 'leads_page');

            // Referrals are users who successfully completed payment and registered.
            $referrals = User::where('referred_by', $user->id)
                ->latest()
                ->paginate(15, ['*'], 'referrals_page');

            return view('student.affiliate.leads', compact('leads', 'referrals'));
        } catch (\Exception $e) {
            Log::error("Error loading leads for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading your leads and referrals.');
        }
    }

    public function commissionStructure()
    {
        try {
            $user = Auth::user();

            // Fetch all active bundles, sorted by preference_index
            $bundles = Bundle::where('is_published', true)->orderBy('preference_index', 'asc')->get();

            // Handle empty state gracefully
            if ($bundles->isEmpty()) {
                return view('student.affiliate.commission-structure', [
                    'bundles' => collect(),
                    'matrix' => [],
                    'userHighestBundleId' => null
                ])->with('warning', 'No commission structures are currently active.');
            }

            // Find user's highest owned bundle for row highlighting
            $userBundles = $user->bundles()->where('is_active', true)->get();
            $userHighestBundle = $userBundles->sortByDesc('preference_index')->first();
            $userHighestBundleId = $userHighestBundle ? $userHighestBundle->id : null;

            // Build the Commission Matrix
            $matrix = [];
            foreach ($bundles as $ownedBundle) {

                // Format owned bundle's flat/percentage string for the header
                $ownedBundle->formatted_commission = ($ownedBundle->commission_type == 'percentage')
                    ? rtrim(rtrim((string)$ownedBundle->commission_value, '0'), '.') . '%'
                    : '₹' . number_format($ownedBundle->commission_value);

                foreach ($bundles as $soldBundle) {
                    $ownedIndex = $ownedBundle->preference_index;
                    $soldIndex = $soldBundle->preference_index;

                    if ($soldIndex <= $ownedIndex) {
                        // Full Commission
                        $amount = $soldBundle->commission_value;
                        $type = $soldBundle->commission_type;
                        $status = 'full';
                    } else {
                        // Capped Commission
                        $amount = $ownedBundle->commission_value;
                        $type = $ownedBundle->commission_type;
                        $status = 'capped';
                    }

                    $formattedAmount = ($type == 'percentage')
                        ? rtrim(rtrim((string)$amount, '0'), '.') . '%'
                        : '₹' . number_format($amount);

                    $matrix[$ownedBundle->id][$soldBundle->id] = [
                        'amount' => $amount,
                        'type' => $type,
                        'formatted_amount' => $formattedAmount,
                        'status' => $status
                    ];
                }
            }

            return view('student.affiliate.commission-structure', compact('bundles', 'matrix', 'userHighestBundleId'));

        } catch (\Exception $e) {
            Log::error("Error loading commission structure for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading the commission structure.');
        }
    }
}

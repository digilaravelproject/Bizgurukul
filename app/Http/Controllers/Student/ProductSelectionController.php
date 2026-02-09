<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;

class ProductSelectionController extends Controller
{
    public function index()
    {
        // 1. Check for Affiliate Link in Session
        $slug = Session::get('affiliate_link_slug');
        $link = null;
        $filteredCourses = collect();
        $filteredBundles = collect();

        $showAll = true; // Default to show all if no link or general link

        if ($slug) {
            $link = AffiliateLink::where('slug', $slug)->where('is_active', true)->first();

            // Check expiry again just in case
            if ($link && $link->expiry_date && $link->expiry_date->isPast()) {
                $link = null; // Treat as no link or general link? User said "commision milega nhi".
                // We should probably treat it as a general viewing experience without attaching the referrer session?
                // But let's stick to the filtering logic first.
            }
        }

        if ($link) {
            $showAll = false;

            switch ($link->type) {
                case 'general':
                    $showAll = true;
                    break;
                case 'specific_course':
                    $filteredCourses = Course::where('id', $link->target_id)->where('is_published', true)->get();
                    break;
                case 'specific_bundle':
                    $filteredBundles = Bundle::where('id', $link->target_id)->get();
                    break;
                // Add new types if supported later, e.g. 'all_courses', 'all_bundles'
                default:
                    $showAll = true;
            }
        }

        if ($showAll) {
            $filteredCourses = Course::where('is_published', true)->get();
            $filteredBundles = Bundle::all();
        }

        // 2. Handle Referral Code for Display/Validation
        $referralCode = Session::get('referrer_code');
        $referrer = null;

        if ($referralCode) {
            $referrer = User::where('referral_code', $referralCode)->first();
        }

        return view('student.products.selection', compact('filteredCourses', 'filteredBundles', 'referrer', 'link'));
    }

    public function applyReferral(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|exists:users,referral_code',
        ]);

        $referrer = User::where('referral_code', $request->referral_code)->first();

        // Save to session and cookie
        Session::put('referrer_code', $referrer->referral_code);
        Cookie::queue('referrer_code', $referrer->referral_code, 43200);

        // Update User's referred_by field directly since they are logged in
        $user = auth()->user();
        if ($user && $user->id !== $referrer->id) { // Prevent self-referral
            $user->referred_by = $referrer->id;
            $user->save();
        }

        return response()->json([
            'success' => true,
            'referrer_name' => $referrer->name,
            'message' => 'Referral code applied successfully!'
        ]);
    }
}

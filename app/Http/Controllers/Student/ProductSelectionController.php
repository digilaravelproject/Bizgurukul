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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class ProductSelectionController extends Controller
{
    public function index()
    {
        try {
            $filteredCourses = collect();
            $filteredBundles = collect();
            $link = null;
            $showAll = true;

            $slug = Session::get('affiliate_link_slug');

            if ($slug) {
                $link = AffiliateLink::where('slug', $slug)->where('is_active', true)->first();
                if ($link && $link->expiry_date && $link->expiry_date->isPast()) {
                    $link = null;
                }
            }

            if ($link) {
                $showAll = false;
                switch ($link->type) {
                    case 'general':
                        $showAll = true;
                        break;
                    case 'specific_course':
                        $course = Course::where('id', $link->target_id)->where('is_published', true)->first();
                        $course ? $filteredCourses->push($course) : $showAll = true;
                        break;
                    case 'specific_bundle':
                        $bundle = Bundle::where('id', $link->target_id)->where('is_published', true)->first();
                        $bundle ? $filteredBundles->push($bundle) : $showAll = true;
                        break;
                    default:
                        $showAll = true;
                }
            }

            if ($showAll) {
                $filteredCourses = Course::where('is_published', true)->latest()->get();
                $filteredBundles = Bundle::where('is_published', true)->latest()->get();
            }

            $referrer = null;
            $referralCode = Session::get('referrer_code');
            if ($referralCode) {
                $referrer = User::where('referral_code', $referralCode)->first();
            }

            return view('student.products.selection', compact('filteredCourses', 'filteredBundles', 'referrer', 'link'));

        } catch (\Exception $e) {
            Log::error('Product Selection Page Error: ' . $e->getMessage());
            return view('student.products.selection', [
                'filteredCourses' => Course::where('is_published', true)->get(),
                'filteredBundles' => Bundle::where('is_published', true)->get()
            ])->with('error', 'Unable to load specific filters.');
        }
    }

    public function applyReferral(Request $request)
    {
        $request->validate([
            'referral_code' => 'required|string|exists:users,referral_code',
        ]);

        /** @var User $currentUser */
        $currentUser = Auth::user();
        $referrer = User::where('referral_code', $request->referral_code)->first();

        if ($currentUser && $currentUser->referral_code === $request->referral_code) {
            return response()->json(['success' => false, 'message' => 'You cannot refer yourself.'], 422);
        }

        Session::put('referrer_code', $referrer->referral_code);
        Cookie::queue('referrer_code', $referrer->referral_code, 43200);

        if ($currentUser) {
            $currentUser->referred_by = $referrer->id;
            $currentUser->save();
        }

        return response()->json([
            'success' => true,
            'referrer_name' => $referrer->name,
            'message' => 'Referral code applied successfully!'
        ]);
    }
}

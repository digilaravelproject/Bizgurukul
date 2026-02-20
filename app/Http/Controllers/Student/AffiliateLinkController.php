<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\AffiliateLink;
use App\Models\Bundle;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Exception;

class AffiliateLinkController extends Controller
{
    public function index()
    {
        try {
            $user = Auth::user();
            $links = AffiliateLink::where('user_id', $user->id)
                ->with(['course', 'bundle'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);

            // Check Affiliate Settings
            $settings = $user->affiliateSettings;
            $canSellCourses = $settings ? $settings->can_sell_courses : false;

            // Filter Products based on permissions
            $courses = $canSellCourses ? Course::where('is_published', true)->get() : collect([]);
            // User request: Bundle permission should always remain enabled for all users.
            $bundles = Bundle::where('is_published', true)->get();

            return view('student.affiliate.links.index', compact('links', 'courses', 'bundles', 'canSellCourses'));
        } catch (Exception $e) {
            Log::error("Error loading affiliate links for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Unable to load affiliate links.');
        }
    }

    public function store(Request $request)
    {
        try {
            // Consolidate target_id based on type
            if ($request->type == 'specific_bundle') {
                $request->merge(['target_id' => $request->target_id_bundle]);
            } elseif ($request->type == 'specific_course') {
                $request->merge(['target_id' => $request->target_id_course]);
            }

            $request->validate([
                'type' => 'required|in:general,specific_course,specific_bundle',
                'target_id' => 'nullable|required_if:type,specific_course,specific_bundle',
                'expiry_date' => 'nullable|date|after:today',
            ]);

            DB::beginTransaction();

            $slug = Str::random(8); // Simple random slug
            // Ensure uniqueness
            while (AffiliateLink::where('slug', $slug)->exists()) {
                $slug = Str::random(8);
            }

            AffiliateLink::create([
                'user_id' => Auth::id(),
                'slug' => $slug,
                'target_type' => $request->type, // Model: target_type
                'target_id' => $request->target_id,
                'expires_at' => $request->expiry_date ?: null, // Ensure null if empty string
                'description' => $request->description,
                'is_deleted' => false, // Active means NOT deleted
                'clicks' => 0,
            ]);

            DB::commit();

            return back()->with('success', 'Affiliate link generated successfully!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (Exception $e) {
            DB::rollBack();
            Log::error("Error generating affiliate link for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Failed to generate affiliate link.')->withInput();
        }
    }

    public function handleRedirect($slug)
    {
        try {
            $link = AffiliateLink::where('slug', $slug)->first();

            // Check if link exists and is NOT deleted (active)
            if (!$link || $link->is_deleted) {
                return redirect()->route('register')->with('error', 'Invalid or inactive link.');
            }

            // Check Expiry
            if ($link->expires_at && $link->expires_at->isPast()) {
                return redirect()->route('register')->with('error', 'This link has expired.');
            }

            // Increment Click
            $link->increment('clicks');

            // Store Referral Data in Session & Cookie (valid for 30 days)
            $referrer = $link->user;

            session([
                'affiliate_link_slug' => $slug,
                'referral_code' => $referrer->referral_code,
                'affiliate_referrer_id' => $referrer->id,
            ]);

            // Clear old cookie immediately and queue new specific matching cookie parameter name
            Cookie::queue(Cookie::forget('referral_code'));
            Cookie::queue(Cookie::forget('referrer_code'));
            $cookie = cookie('referral_code', $referrer->referral_code, 43200); // 30 days

            // STRICT REDIRECT: Always to Register Phase 1
            return redirect()->route('register.phase1')->withCookie($cookie);

        } catch (Exception $e) {
            Log::error("Error handling affiliate redirect for slug {$slug}: " . $e->getMessage());
            return redirect()->route('register')->with('error', 'An error occurred while processing the referral link.');
        }
    }

    public function destroy($id)
    {
        try {
            $link = AffiliateLink::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
            $link->delete();
            return back()->with('success', 'Link deleted successfully.');
        } catch (Exception $e) {
            Log::error("Error deleting affiliate link {$id} for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Failed to delete link.');
        }
    }
}

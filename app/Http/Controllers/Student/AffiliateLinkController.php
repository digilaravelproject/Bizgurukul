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

class AffiliateLinkController extends Controller
{
    public function index()
    {
        $links = AffiliateLink::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        $courses = Course::where('is_published', true)->get();
        $bundles = Bundle::all();

        return view('student.affiliate.links.index', compact('links', 'courses', 'bundles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:general,specific_course,specific_bundle',
            'target_id' => 'nullable|required_if:type,specific_course,specific_bundle',
            'expiry_date' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:255',
        ]);

        $slug = Str::random(8); // Simple random slug
        // Ensure uniqueness
        while (AffiliateLink::where('slug', $slug)->exists()) {
            $slug = Str::random(8);
        }

        AffiliateLink::create([
            'user_id' => Auth::id(),
            'slug' => $slug,
            'type' => $request->type,
            'target_id' => $request->target_id,
            'expiry_date' => $request->expiry_date,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return back()->with('success', 'Affiliate link generated successfully!');
    }

    public function handleRedirect($slug)
    {
        $link = AffiliateLink::where('slug', $slug)->first();

        if (!$link || !$link->is_active) {
            return redirect()->route('home')->with('error', 'Invalid or inactive link.');
        }

        // Check Expiry
        if ($link->expiry_date && $link->expiry_date->isPast()) {
            return redirect()->route('home')->with('error', 'This link has expired.');
        }

        // Increment Click
        $link->increment('click_count');

        // Store Referral Data in Session & Cookie (valid for 30 days)
        // We store the link ID to know *what* to show them later,
        // and the referrer_code (user's referral code) for the actual commission logic.

        $referrer = $link->user;

        session([
            'affiliate_link_slug' => $slug,
            'referrer_code' => $referrer->referral_code,
        ]);

        $cookie = cookie('referrer_code', $referrer->referral_code, 43200); // 30 days

        // Redirect flow:
        // 1. If user is already logged in:
        //    - If they haven't bought anything -> Go to Product Selection
        //    - If they have -> Go to Dashboard (or the specific product page if they want to buy it?)
        //      User said: "agr mene ek bhi course purchase nhi kiya hain to... direct jaunga product page per"

        if (Auth::check()) {
            // Check if user has purchased anything?
            // For now, let's redirect them to the product selection page if they are paying users or not.
            // The user wants them to see specific products if the link is specific.
            return redirect()->route('student.product_selection')->withCookie($cookie);
        }

        // 2. If user is NOT logged in -> Register Page
        return redirect()->route('register')->withCookie($cookie);
    }

    public function destroy($id)
    {
        $link = AffiliateLink::where('user_id', Auth::id())->where('id', $id)->firstOrFail();
        $link->delete();
        return back()->with('success', 'Link deleted successfully.');
    }
}

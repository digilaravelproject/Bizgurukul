<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Bundle;
use App\Models\Course; // Assuming logic allows selling courses explicitly
use App\Services\AffiliatePermissionService;
use App\Repositories\AffiliateRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AffiliateController extends Controller
{
    protected $permissionService;
    protected $affiliateRepo;

    public function __construct(AffiliatePermissionService $permissionService, AffiliateRepository $affiliateRepo)
    {
        $this->permissionService = $permissionService;
        $this->affiliateRepo = $affiliateRepo;
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Fetch My Links
        $links = $this->affiliateRepo->getAffiliateLinks($user->id);

        // Fetch Products available to generate links for
        // 1. Bundles (Check permissions)
        // 2. Courses (Check permissions)

        $bundles = Bundle::where('is_active', true)->where('is_published', true)->get();
        // Filter by permissions
        $availableBundles = $bundles->filter(function($bundle) use ($user) {
            return $this->permissionService->canSellBundle($user, $bundle->id);
        });

        $canSellCourses = $this->permissionService->canSellCourses($user);
        $availableCourses = $canSellCourses ? Course::where('is_published', true)->get() : collect([]);

        return view('affiliate.dashboard', compact('links', 'availableBundles', 'availableCourses', 'canSellCourses'));
    }

    public function generateLink(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:all,bundle,course',
            'target_id' => 'required_if:target_type,bundle,course|nullable|integer',
            'expires_at' => 'nullable|date|after:now',
            'description' => 'nullable|string|max:255',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Permission Check
        if ($request->target_type == 'course' && !$this->permissionService->canSellCourses($user)) {
             return redirect()->back()->with('error', 'You are not authorized to sell courses.');
        }
        if ($request->target_type == 'bundle') {
             if (!$this->permissionService->canSellBundle($user, $request->target_id)) {
                 return redirect()->back()->with('error', 'You are not authorized to sell this bundle.');
             }
        }

        // Generate Slug
        $slug = 'ref_' . Str::random(8); // or whatever format
        // Ensure uniqueness
        while (\App\Models\AffiliateLink::where('slug', $slug)->exists()) {
            $slug = 'ref_' . Str::random(8);
        }

        $this->affiliateRepo->createLink([
            'user_id' => $user->id,
            'slug' => $slug,
            'target_type' => $request->target_type,
            'target_id' => $request->target_id,
            'expires_at' => $request->expires_at,
            'description' => $request->description,
        ]);

        return redirect()->back()->with('success', 'Affiliate link generated successfully.');
    }

    public function deleteLink($id)
    {
        /** @var \App\Models\AffiliateLink|null $link */
        $link = \App\Models\AffiliateLink::where('user_id', Auth::id())->find($id);
        if (!$link) {
            return redirect()->back()->with('error', 'Link not found.');
        }

        $link->is_deleted = true;
        $link->save();

        return redirect()->back()->with('success', 'Link deleted successfully.');
    }
}

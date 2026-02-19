<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AffiliateService;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;

class AffiliateController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function index()
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $data = $this->affiliateService->getDashboardData($user);

            return view('affiliate.dashboard', $data);
        } catch (Exception $e) {
            Log::error("AffiliateController Error [index]: " . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to load dashboard data.');
        }
    }

    public function generateLink(Request $request)
    {
        // Consolidate target_id based on type
        if ($request->type == 'specific_bundle') {
            $request->merge(['target_id' => $request->target_id_bundle]);
        } elseif ($request->type == 'specific_course') {
            $request->merge(['target_id' => $request->target_id_course]);
        }

        // Use 'type' as 'target_type' for validation compatibility
        if ($request->has('type')) {
            $request->merge(['target_type' => $request->type]);
        }

        $validatedData = $request->validate([
            'target_type' => 'required|in:general,specific_bundle,specific_course',
            'target_id' => 'required_if:target_type,specific_bundle,specific_course|nullable|integer',
            'expiry_date' => 'nullable|date|after:now',
            // Description removed
        ]);

        // Map expiry_date to expires_at for the Service
        if (isset($validatedData['expiry_date'])) {
            $validatedData['expires_at'] = $validatedData['expiry_date'];
            unset($validatedData['expiry_date']);
        }

        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $this->affiliateService->generateLink($user, $validatedData);

            return redirect()->back()->with('success', 'Affiliate link generated successfully.');
        } catch (Exception $e) {
            Log::error("AffiliateController Error [generateLink]: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function deleteLink($id)
    {
        try {
            /** @var \App\Models\User $user */
            $user = Auth::user();

            $this->affiliateService->deleteLink($user, $id);

            return redirect()->back()->with('success', 'Link deleted successfully.');
        } catch (Exception $e) {
            Log::error("AffiliateController Error [deleteLink]: " . $e->getMessage());
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}

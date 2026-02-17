<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CommissionRule;
use App\Models\User;
use App\Models\Course;
use App\Models\Bundle;
use Exception;
use Illuminate\Support\Facades\Log;

class CommissionRuleController extends Controller
{
    protected $ruleService;

    public function __construct(\App\Services\CommissionRuleService $ruleService)
    {
        $this->ruleService = $ruleService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            // 1. Fetch Commission Rules
            $rules = $this->ruleService->getRules();

            // 2. Fetch Users with specific permissions (Non-default settings)
            $userOverrides = \App\Models\UserAffiliateSetting::with('user')
                                ->whereNotNull('can_sell_courses')
                                ->orWhereNotNull('allowed_bundle_ids')
                                ->latest()
                                ->paginate(10);

            // Fetch potential targets for dropdowns
            $courses = Course::select('id', 'title')->get();
            $bundles = Bundle::select('id', 'title')->get();
            $affiliates = User::select('id', 'name', 'email')->get(); // Fetch all for search (or limit if too many)

            return view('admin.affiliate.rules', compact('rules', 'userOverrides', 'courses', 'bundles', 'affiliates'));
        } catch (Exception $e) {
            Log::error($e->getMessage());
            return back()->with('error', 'Something went wrong');
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'affiliate_id' => 'nullable|exists:users,id',
            'product_type' => 'nullable|in:course,bundle',
            'product_id' => 'nullable|integer',
            'commission_type' => 'required|in:percent,fixed',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            $this->ruleService->createRule($request->all());
            return back()->with('success', 'Commission Rule Created Successfully');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to create rule: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $this->ruleService->deleteRule($id);
            return back()->with('success', 'Rule Deleted');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete rule');
        }
    }
}

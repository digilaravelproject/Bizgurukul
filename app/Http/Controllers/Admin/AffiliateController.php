<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCommission;
use App\Models\Bundle;
use App\Models\CommissionRule;
use App\Models\Course;
use App\Models\Setting;
use App\Models\User;
use App\Services\AffiliateService;
use App\Services\CommissionRuleService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;

class AffiliateController extends Controller
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    /**
     * User Manager - List Users
     */
    public function index(Request $request)
    {
        return redirect()->route('admin.affiliate.rules.index');
    }

    /**
     * Edit User Affiliate Permissions
     */
    public function edit($id)
    {
        $user = User::with(['affiliateSettings', 'commissionRules.product'])->findOrFail($id);
        $bundles = Bundle::where('is_active', true)->get();
        // For rule creation modal/form
        $courses = Course::select('id', 'title')->get();
        $allBundles = Bundle::select('id', 'title')->get();

        return view('admin.affiliate.users.edit', compact('user', 'bundles', 'courses', 'allBundles'));
    }

    /**
     * Update User Affiliate Permissions
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        // Validate
        $request->validate([
            'can_sell_courses' => 'nullable|boolean',
            'allowed_bundle_ids' => 'nullable|array',
            'custom_commission_percentage' => 'nullable|numeric|min:0|max:100',
        ]);

        // Create or Update Settings
        $settings = $user->affiliateSettings()->firstOrCreate(['user_id' => $user->id]);

        // If can_sell_courses is 'null' (string check from form?) assume null means global default.
        // Usually forms send '1' or nothing. If radio with 'default', value should be null.
        // We'll trust validation.

        $settings->can_sell_courses = $request->input('can_sell_courses'); // null, true, false
        $settings->allowed_bundle_ids = $request->input('allowed_bundle_ids');
        $settings->custom_commission_percentage = $request->input('custom_commission_percentage');
        $settings->save();

        return redirect()->route('admin.affiliate.rules.index')->with('success', 'User affiliate permissions updated.');
    }

    /**
     * Global Settings View
     */
    public function settings()
    {
        $affiliateEnabled = Setting::get('affiliate_module_enabled', true);
        $courseSellingEnabled = Setting::get('course_selling_enabled', false);
        $upgradeWindowHours = Setting::get('upgrade_window_hours', 24);

        // Fetch Global Rules (where affiliate_id is null)
        $globalRules = CommissionRule::with('product')
            ->whereNull('affiliate_id')
            ->latest()
            ->get();

        $courses = Course::select('id', 'title')->get();
        $bundles = Bundle::select('id', 'title')->get();

        return view('admin.affiliate.settings', compact('affiliateEnabled', 'courseSellingEnabled', 'upgradeWindowHours', 'globalRules', 'courses', 'bundles'));
    }

    /**
     * Update Global Settings
     */
    public function updateSettings(Request $request)
    {
        Setting::set('affiliate_module_enabled', $request->boolean('affiliate_module_enabled'));
        Setting::set('course_selling_enabled', $request->boolean('course_selling_enabled'));

        $request->validate([
            'upgrade_window_hours' => 'required|integer|min:0',
        ]);
        Setting::set('upgrade_window_hours', $request->input('upgrade_window_hours'));

        return redirect()->back()->with('success', 'Global settings updated.');
    }

    public function history(Request $request)
    {
        $perPage = $request->input('per_page', 20);
        if (! in_array($perPage, [20, 30, 50, 100, 200])) {
            $perPage = 20;
        }

        $query = AffiliateCommission::with(['affiliate', 'referredUser', 'reference']);

        // Applying Search
        if ($search = $request->input('search')) {
            $query->whereHas('affiliate', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Applying Date Filter
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [Carbon::parse($startDate)->startOfDay(), Carbon::parse($endDate)->endOfDay()]);
        }

        $commissions = $query->latest()->paginate($perPage);

        if ($request->ajax()) {
            return response()->json([
                'status' => true,
                'table' => view('admin.affiliate.partials.history_table', compact('commissions'))->render(),
                'pagination' => view('components.admin.table.pagination', ['records' => $commissions])->render(),
            ]);
        }

        return view('admin.affiliate.history', compact('commissions'));
    }

    /**
     * Mark Commission as Paid (Approve & Credit Wallet)
     */
    public function markAsPaid($id)
    {
        try {
            $this->affiliateService->processPayout($id);

            return redirect()->back()->with('success', 'Commission approved and wallet credited.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    /**
     * Store a specific rule for a user
     */
    public function storeRule(Request $request, $userId)
    {
        $request->merge(['affiliate_id' => $userId]);

        $request->validate([
            'product_type' => 'nullable|in:course,bundle',
            'product_id' => 'nullable|integer',
            'commission_type' => 'required|in:percent,fixed',
            'amount' => 'required|numeric|min:0',
        ]);

        try {
            // Use CommissionRuleService (resolve it here or inject in constructor)
            // Ideally inject, but for quick refactor:
            $ruleService = app(CommissionRuleService::class);
            $ruleService->createRule($request->all());

            return back()->with('success', 'Commission Rule Added.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a specific rule
     */
    public function deleteRule($id)
    {
        try {
            $ruleService = app(CommissionRuleService::class);
            $ruleService->deleteRule($id);

            return back()->with('success', 'Commission Rule Removed.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

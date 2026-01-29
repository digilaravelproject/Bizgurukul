<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Bundle;
use App\Services\CouponService; // Custom Service Inject kiya hai
use Illuminate\Http\Request;
use Exception;

class CouponController extends Controller
{
    protected $couponService;

    /**
     * Dependency Injection for CouponService
     */
    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

    /**
     * List all coupons with search and AJAX support
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $query = Coupon::latest();

        // Search fixed: service_name removed
        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$search}%");
        }

        $coupons = $query->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'coupons' => view('admin.coupons.partials.table', compact('coupons'))->render(),
            ]);
        }

        return view('admin.coupons.index', compact('coupons'));
    }

    /**
     * Show Create Form
     */
    public function create()
    {
        // Published Courses aur Bundles load karna multiple selection ke liye
        $courses = Course::where('is_published', true)->select('id', 'title')->get();
        $bundles = Bundle::where('is_published', true)->select('id', 'title')->get();

        return view('admin.coupons.edit', compact('courses', 'bundles'));
    }

    /**
     * Store or Update via Service
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'        => 'required|string|max:50|unique:coupons,code,' . $request->id,
            'coupon_type' => 'required|in:general,specific',
            'type'        => 'required|in:fixed,percentage',
            'value'       => 'required|numeric|min:0',
            'courses'     => 'nullable|array',
            'bundles'     => 'nullable|array',
        ]);

        try {
            $this->couponService->saveCoupon($request->all(), $request->id);

            return redirect()->route('admin.coupons.index')->with('success', 'Coupon processed successfully!');
        } catch (Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show Edit Form
     */
    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $courses = Course::where('is_published', true)->select('id', 'title')->get();
        $bundles = Bundle::where('is_published', true)->select('id', 'title')->get();

        return view('admin.coupons.edit', compact('coupon', 'courses', 'bundles'));
    }

    /**
     * Toggle active/inactive status via AJAX
     */
    public function toggleStatus($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->is_active = !$coupon->is_active;
            $coupon->save();

            return response()->json(['success' => 'Status updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['error' => 'Status update failed.'], 500);
        }
    }

    /**
     * Remove coupon permanently
     */
    public function destroy($id)
    {
        try {
            $coupon = Coupon::findOrFail($id);
            $coupon->delete();

            return redirect()->route('admin.coupons.index')->with('success', 'Coupon deleted permanently!');
        } catch (Exception $e) {
            return back()->with('error', 'Delete failed: ' . $e->getMessage());
        }
    }
}

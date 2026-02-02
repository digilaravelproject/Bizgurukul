<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCouponRequest;
use App\Services\CouponService;
use App\Models\Course;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Exception;

class CouponController extends Controller
{
    protected $couponService;

    public function __construct(CouponService $couponService)
    {
        $this->couponService = $couponService;
    }

public function index(Request $request)
{
    // 1. Always fetch coupons so they are available for both AJAX and initial load
    $coupons = $this->couponService->getPaginatedCoupons($request);

    if ($request->ajax()) {
        try {
            return response()->json([
                'status' => 'success',
                'html'   => view('admin.coupons.partials.table_rows', compact('coupons'))->render(),
                'pagination' => (string) $coupons->links()
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to load data.'], 500);
        }
    }

    // 2. Data for the dropdowns/modals
    $courses = Course::where('is_published', true)->select('id', 'title')->get();
    $bundles = Bundle::where('is_published', true)->select('id', 'title')->get();

    $courses->each->setAppends([]);
    $bundles->each->setAppends([]);

    // 3. Pass $coupons to the view
    return view('admin.coupons.index', compact('courses', 'bundles', 'coupons'));
}

    public function store(StoreCouponRequest $request)
    {
        try {
            // Validated data comes from StoreCouponRequest
            $this->couponService->handleSave($request->validated(), $request->id);

            return response()->json([
                'status' => 'success',
                'message' => $request->id ? 'Coupon updated successfully!' : 'Coupon created successfully!',
                'redirect_url' => route('admin.coupons.index') // Optional: Used by JS to redirect
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong. Please try again.',
                'debug_error' => $e->getMessage() // Remove this in production
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $coupon = $this->couponService->getCouponForEdit($id);
            return response()->json(['status' => 'success', 'data' => $coupon]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 404);
        }
    }

    public function destroy($id)
    {
        try {
            $this->couponService->deleteCoupon($id);
            return response()->json(['status' => 'success', 'message' => 'Coupon deleted permanently.']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Deletion failed.'], 500);
        }
    }

    public function toggleStatus($id)
    {
        try {
            $this->couponService->toggleStatus($id);
            return response()->json(['status' => 'success', 'message' => 'Status updated.']);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Could not update status.'], 500);
        }
    }
}

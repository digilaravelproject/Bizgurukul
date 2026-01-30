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
        if ($request->ajax()) {
            try {
                $coupons = $this->couponService->getPaginatedCoupons($request);
                return response()->json([
                    'status' => 'success',
                    'html'   => view('admin.coupons.partials.table_rows', compact('coupons'))->render(),
                    'pagination' => (string) $coupons->links()
                ]);
            } catch (Exception $e) {
                return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
            }
        }

        $courses = Course::where('is_published', true)->select('id', 'title')->get();
        $bundles = Bundle::where('is_published', true)->select('id', 'title')->get();

        return view('admin.coupons.index', compact('courses', 'bundles'));
    }

    public function store(StoreCouponRequest $request)
    {
        try {
            $this->couponService->handleSave($request->validated(), $request->id);

            return response()->json([
                'status' => 'success',
                'message' => $request->id ? 'Coupon updated successfully!' : 'Coupon created successfully!'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $coupon = $this->couponService->getCouponForEdit($id);
            return response()->json(['status' => 'success', 'data' => $coupon]);
        } catch (Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Coupon not found.'], 404);
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
}

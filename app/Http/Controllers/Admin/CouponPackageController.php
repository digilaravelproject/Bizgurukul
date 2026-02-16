<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CouponPackage;
use App\Models\Course;
use App\Models\Bundle;
use App\Services\CouponPackageService;
use Illuminate\Http\Request;

class CouponPackageController extends Controller
{
    protected $service;

    public function __construct(CouponPackageService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $packages = CouponPackage::when($request->search, function ($q) use ($request) {
            $q->where('name', 'like', "%{$request->search}%");
        })->latest()->paginate(10);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'html'   => view('admin.coupon_packages.partials.table_rows', compact('packages'))->render(),
                'pagination' => (string) $packages->links()
            ]);
        }

        $courses = Course::where('is_published', true)->get(['id', 'title']);
        $bundles = Bundle::where('is_published', true)->get(['id', 'title']);

        return view('admin.coupon_packages.index', compact('packages', 'courses', 'bundles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'selling_price' => 'required|numeric',
            'discount_value' => 'required|numeric',
            'type' => 'required|in:fixed,percentage'
        ]);

        try {
            $this->service->handleSave($request->all(), $request->id);
            return response()->json(['status' => 'success', 'message' => 'Package Saved!']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function edit($id)
    {
        return response()->json(['status' => 'success', 'data' => CouponPackage::findOrFail($id)]);
    }

    public function destroy($id)
    {
        CouponPackage::findOrFail($id)->delete();
        return response()->json(['status' => 'success', 'message' => 'Deleted!']);
    }
}

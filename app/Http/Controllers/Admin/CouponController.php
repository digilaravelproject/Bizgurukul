<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Course;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;

class CouponController extends Controller
{
    /**
     * Display a listing of the coupons with AJAX support.
     */
    public function index(Request $request)
    {
        $search = $request->search;

        $query = Coupon::with('couponable')->latest();

        // Search logic for codes or linked item titles
        if ($request->filled('search')) {
            $query->where('code', 'like', "%{$search}%")
                ->orWhereHasMorph('couponable', [Course::class, Bundle::class], function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                });
        }

        $coupons = $query->paginate(10);

        // AJAX response for search/pagination
        if ($request->ajax()) {
            return response()->json([
                'coupons' => view('admin.coupons.partials.table', compact('coupons'))->render(),
            ]);
        }

        // For Create Form Dropdowns
        $courses = Course::select('id', 'title')->where('is_published', true)->get();
        $bundles = Bundle::select('id', 'title')->where('is_published', true)->get();

        return view('admin.coupons.index', compact('coupons', 'courses', 'bundles'));
    }

    /**
     * Store or Update a Coupon.
     */
    public function store(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|max:50|unique:coupons,code,' . $request->id,
            'type'            => 'required|in:fixed,percentage',
            'value'           => 'required|numeric|min:0',
            'item_id'         => 'required|numeric',
            'item_type'       => 'required|string', // Course or Bundle model path
            'expiry_date'     => 'nullable|date|after_or_equal:today',
            'usage_limit'     => 'nullable|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            Coupon::updateOrCreate(
                ['id' => $request->id],
                [
                    'code'            => strtoupper($request->code),
                    'type'            => $request->type,
                    'value'           => $request->value,
                    'expiry_date'     => $request->expiry_date,
                    'usage_limit'     => $request->usage_limit ?? 1,
                    'couponable_id'   => $request->item_id,
                    'couponable_type' => $request->item_type,
                    'is_active'       => $request->has('is_active') ? true : true,
                ]
            );

            DB::commit();
            return redirect()->route('admin.coupons.index')->with('success', 'Coupon saved successfully!');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show edit data (Used if you want a separate edit page or modal).
     */
    public function create()
    {
        $courses = Course::where('is_published', true)->get();
        $bundles = Bundle::where('is_published', true)->get();
        return view('admin.coupons.edit', compact('courses', 'bundles'));
    }

    public function edit($id)
    {
        $coupon = Coupon::findOrFail($id);
        $courses = Course::where('is_published', true)->get();
        $bundles = Bundle::where('is_published', true)->get();
        return view('admin.coupons.edit', compact('coupon', 'courses', 'bundles'));
    }

    /**
     * Remove the specified coupon.
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

    /**
     * Toggle status via AJAX.
     */
    public function toggleStatus($id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = !$coupon->is_active;
        $coupon->save();

        return response()->json(['success' => 'Status updated!']);
    }
}

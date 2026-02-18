<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\BundleService;
use App\Services\LmsService;
use Exception;
use Illuminate\Http\Request;

class BundleController extends Controller
{
    protected $bundleService;
    protected $lmsService;

    public function __construct(BundleService $bundleService, LmsService $lmsService)
    {
        $this->bundleService = $bundleService;
        $this->lmsService = $lmsService;
    }

    public function index(Request $request)
    {
        $bundles = $this->bundleService->getBundles($request->all());
         if ($request->ajax()) {
                $bundles = $this->bundleService->getBundles($request->all());

                return view('admin.bundles.partials.table', compact('bundles'))->render();
            }
        return view('admin.bundles.index', compact('bundles'));
    }

    public function create()
    {
        $courses = \App\Models\Course::select('id', 'title')->get();
        $allBundles = \App\Models\Bundle::select('id', 'title')->get();

        return view('admin.bundles.create', compact('courses', 'allBundles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_price' => 'required|numeric|min:0',
            'affiliate_price' => 'required|numeric|min:0|lte:website_price',
            'discount_type' => 'nullable|in:flat,percentage',
            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('The discount percentage cannot exceed 100.');
                    }
                    if ($request->discount_type === 'flat' && $value > $request->website_price) {
                        $fail('The discount value cannot exceed the website price.');
                    }
                },
            ],
            'commission_type' => 'nullable|in:flat,percentage',
            'commission_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->commission_type === 'percentage' && $value > 100) {
                        $fail('The commission percentage cannot exceed 100.');
                    }
                    if ($request->commission_type === 'flat' && $value > $request->website_price) {
                        $fail('The commission value cannot exceed the website price.');
                    }
                },
            ],
            'thumbnail' => 'required|image|max:5120', // Max 5MB
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'bundles' => 'nullable|array',
            'bundles.*' => 'exists:bundles,id',
            'is_published' => 'boolean',
            'preference_index' => 'nullable|integer|min:0',
        ]);

        try {
            $this->bundleService->createBundle($request->all());
            return redirect()->route('admin.bundles.index')->with('success', 'Bundle created successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $bundle = $this->bundleService->getBundle($id);

        $courses = \App\Models\Course::select('id', 'title')->get();
        $allBundles = \App\Models\Bundle::where('id', '!=', $id)->select('id', 'title')->get();
        $selectedCourses = $bundle->courses->pluck('id')->toArray();
        $selectedBundles = $bundle->childBundles->pluck('id')->toArray();

        return view('admin.bundles.edit', compact('bundle', 'courses', 'allBundles', 'selectedCourses', 'selectedBundles'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'website_price' => 'required|numeric|min:0',
            'affiliate_price' => 'required|numeric|min:0|lte:website_price',
            'discount_type' => 'nullable|in:flat,percentage',
            'discount_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->discount_type === 'percentage' && $value > 100) {
                        $fail('The discount percentage cannot exceed 100.');
                    }
                    if ($request->discount_type === 'flat' && $value > $request->website_price) {
                        $fail('The discount value cannot exceed the website price.');
                    }
                },
            ],
            'commission_type' => 'nullable|in:flat,percentage',
            'commission_value' => [
                'nullable',
                'numeric',
                'min:0',
                function ($attribute, $value, $fail) use ($request) {
                    if ($request->commission_type === 'percentage' && $value > 100) {
                        $fail('The commission percentage cannot exceed 100.');
                    }
                    if ($request->commission_type === 'flat' && $value > $request->website_price) {
                        $fail('The commission value cannot exceed the website price.');
                    }
                },
            ],
            'thumbnail' => 'nullable|image|max:5120', // Max 5MB
            'courses' => 'nullable|array',
            'bundles' => 'nullable|array',
            'is_published' => 'boolean',
            'preference_index' => 'nullable|integer|min:0',
        ]);

        try {

            $this->bundleService->updateBundle($id, $request->all());
            return redirect()->route('admin.bundles.index')->with('success', 'Bundle updated successfully');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $this->bundleService->deleteBundle($id);
            return back()->with('success', 'Bundle deleted');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}

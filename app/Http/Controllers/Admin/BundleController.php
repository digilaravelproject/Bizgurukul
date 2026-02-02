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
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'required|image|max:2048',
            'courses' => 'nullable|array',
            'courses.*' => 'exists:courses,id',
            'bundles' => 'nullable|array',
            'bundles.*' => 'exists:bundles,id',
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
            'price' => 'required|numeric|min:0',
            'thumbnail' => 'nullable|image|max:2048',
            'courses' => 'nullable|array',
            'bundles' => 'nullable|array',
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

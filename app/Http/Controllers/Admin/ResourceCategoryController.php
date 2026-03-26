<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ResourceCategory;
use Illuminate\Http\Request;

class ResourceCategoryController extends Controller
{
    public function index()
    {
        $categories = ResourceCategory::withCount('resources')->orderBy('order_column')->get();
        return view('admin.resource-categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order_column' => 'nullable|integer',
            'status' => 'nullable|boolean'
        ]);

        ResourceCategory::create([
            'name' => $request->name,
            'order_column' => $request->order_column ?? 0,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Resource Category created successfully.');
    }

    public function update(Request $request, ResourceCategory $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'order_column' => 'nullable|integer',
            'status' => 'nullable|boolean'
        ]);

        $category->update([
            'name' => $request->name,
            'order_column' => $request->order_column ?? 0,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Resource Category updated successfully.');
    }

    public function destroy(ResourceCategory $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Resource Category deleted successfully.');
    }
}

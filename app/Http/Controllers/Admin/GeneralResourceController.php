<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralResource;
use App\Models\ResourceCategory;
use Illuminate\Http\Request;

class GeneralResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = GeneralResource::with('category')->orderBy('order_column');
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $resources = $query->get();
        $categories = ResourceCategory::all();
        
        return view('admin.general-resources.index', compact('resources', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category_id' => 'required|exists:resource_categories,id',
            'title' => 'required|string|max:255',
            'link_url' => 'required|url',
            'icon' => 'nullable|string',
            'order_column' => 'nullable|integer',
            'status' => 'nullable|boolean'
        ]);

        GeneralResource::create([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'link_url' => $request->link_url,
            'icon' => $request->icon ?? 'fa-link',
            'order_column' => $request->order_column ?? 0,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Resource created successfully.');
    }

    public function update(Request $request, GeneralResource $resource)
    {
        $request->validate([
            'category_id' => 'required|exists:resource_categories,id',
            'title' => 'required|string|max:255',
            'link_url' => 'required|url',
            'icon' => 'nullable|string',
            'order_column' => 'nullable|integer',
            'status' => 'nullable|boolean'
        ]);

        $resource->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'link_url' => $request->link_url,
            'icon' => $request->icon ?? 'fa-link',
            'order_column' => $request->order_column ?? 0,
            'status' => $request->has('status') ? true : false,
        ]);

        return redirect()->back()->with('success', 'Resource updated successfully.');
    }

    public function destroy(GeneralResource $resource)
    {
        $resource->delete();
        return redirect()->back()->with('success', 'Resource deleted successfully.');
    }
}

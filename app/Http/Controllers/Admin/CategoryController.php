<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        try {
            // 1. Base Query
            $query = Category::with('subCategories')->whereNull('parent_id');

            // 2. Search Logic (Server Side)
            if ($request->filled('search')) {
                $query->where('name', 'like', '%' . $request->search . '%');
            }

            // 3. Pagination (10 records per page) with Query String preservation
            $categories = $query->latest()->paginate(10)->withQueryString();

            // 4. AJAX CHECK: If this is an AJAX request (from search), return ONLY the table partial
            if ($request->ajax()) {
                return view('admin.categories.partials.table', compact('categories'))->render();
            }

            // 5. Normal Request: Load modal data and full page
            $allCategories = Category::whereNull('parent_id')->get();

            return view('admin.categories.index', compact('categories', 'allCategories'));

        } catch (Exception $e) {
            // Fallback for Index Error
            return back()->with('error', 'Something went wrong: ' . $e->getMessage());
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            Category::create([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'parent_id' => $request->parent_id,
                'is_active' => $request->is_active ?? 1,
            ]);

            return response()->json(['status' => true, 'message' => 'Category created successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255|unique:categories,name,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        try {
            $category = Category::findOrFail($id);
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'parent_id' => $request->parent_id,
                'is_active' => $request->is_active,
            ]);

            return response()->json(['status' => true, 'message' => 'Category updated successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $category = Category::findOrFail($id);

            // Recursive delete: Remove sub-categories first
            if ($category->subCategories()->exists()) {
                $category->subCategories()->delete();
            }

            $category->delete();

            return response()->json(['status' => true, 'message' => 'Category and its sub-categories deleted successfully!']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

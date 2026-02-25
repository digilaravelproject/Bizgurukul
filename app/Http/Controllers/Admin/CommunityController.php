<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Community;
use Illuminate\Http\Request;
use Exception;

class CommunityController extends Controller
{
    public function index()
    {
        $communities = Community::orderBy('order_index')->get()->groupBy('group_name');
        return view('admin.communities.index', compact('communities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:255',
            'link' => 'required|url',
            'button_text' => 'required|string|max:50',
            'group_name' => 'required|string',
        ]);

        try {
            Community::create([
                'name' => $request->name,
                'description' => $request->description,
                'link' => $request->link,
                'button_text' => $request->button_text,
                'group_name' => $request->group_name,
                'is_custom' => true,
                'is_active' => true,
                'order_index' => Community::max('order_index') + 1,
            ]);

            return back()->with('success', 'Community link added successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to add community: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Community $community)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:255',
            'link' => 'required|url',
            'button_text' => 'sometimes|required|string|max:50',
            'group_name' => 'sometimes|required|string',
        ]);

        try {
            $community->update($request->only(['name', 'description', 'link', 'button_text', 'group_name']));
            return back()->with('success', 'Community link updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update community: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Community $community)
    {
        try {
            $community->update(['is_active' => !$community->is_active]);
            return back()->with('success', 'Status updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to update status.');
        }
    }

    public function destroy(Community $community)
    {
        if (!$community->is_custom) {
            return back()->with('error', 'Default community links cannot be deleted.');
        }

        try {
            $community->delete();
            return back()->with('success', 'Community link deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Failed to delete community.');
        }
    }
}

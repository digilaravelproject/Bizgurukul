<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tax;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Log;

class TaxController extends Controller
{
    public function index()
    {
        $taxes = Tax::latest()->paginate(10);
        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        return view('admin.taxes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,flat',
            'tax_type' => 'required|in:inclusive,exclusive',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            // Default is_active to false if not provided
            $validated['is_active'] = $request->boolean('is_active');

            Tax::create($validated);
            return redirect()->route('admin.taxes.index')->with('success', 'Tax created successfully.');
        } catch (Exception $e) {
            Log::error("Error creating tax: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create tax. Please try again.');
        }
    }

    public function edit(Tax $tax)
    {
        return view('admin.taxes.edit', compact('tax'));
    }

    public function update(Request $request, Tax $tax)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:percentage,flat',
            'tax_type' => 'required|in:inclusive,exclusive',
            'value' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        try {
            $validated['is_active'] = $request->boolean('is_active');
            $tax->update($validated);
            return redirect()->route('admin.taxes.index')->with('success', 'Tax updated successfully.');
        } catch (Exception $e) {
            Log::error("Error updating tax: " . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update tax. Please try again.');
        }
    }

    public function destroy(Tax $tax)
    {
        try {
            $tax->delete();
            return redirect()->route('admin.taxes.index')->with('success', 'Tax deleted successfully.');
        } catch (Exception $e) {
            Log::error("Error deleting tax: " . $e->getMessage());
            return back()->with('error', 'Failed to delete tax.');
        }
    }
}

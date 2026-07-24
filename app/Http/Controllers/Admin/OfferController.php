<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Offer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class OfferController extends Controller
{
    /**
     * Display a listing of offers with real-time status indicators.
     */
    public function index(Request $request)
    {
        try {
            $query = Offer::query();

            if ($search = $request->input('search')) {
                $query->where('title', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%");
            }

            if ($statusFilter = $request->input('status')) {
                $now = now();
                if ($statusFilter === 'active') {
                    $query->activePhase();
                } elseif ($statusFilter === 'expired') {
                    $query->expiredPhase();
                } elseif ($statusFilter === 'disabled') {
                    $query->where('is_active', false);
                }
            }

            $offers = $query->orderBy('id', 'desc')->paginate(15);

            if ($request->ajax()) {
                return response()->json([
                    'table' => view('admin.offers.partials.offers_table', compact('offers'))->render(),
                    'pagination' => view('components.admin.table.pagination', ['records' => $offers])->render()
                ])->header('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate')
                  ->header('Pragma', 'no-cache');
            }

            return view('admin.offers.index', compact('offers'));
        } catch (\Throwable $e) {
            Log::error('Error in OfferController@index: ' . $e->getMessage());
            return back()->with('error', 'Unable to load offers. Please try again.');
        }
    }

    /**
     * Show form to create a new offer.
     */
    public function create()
    {
        return view('admin.offers.create');
    }

    /**
     * Store a newly created offer.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'reward_value' => 'required|numeric|min:0',
            'reward_type' => 'required|in:cash,gift,trip,gadget,custom',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            if ($request->hasFile('image')) {
                $validated['image'] = $request->file('image')->store('offers', 'public');
            }

            $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : true;
            $validated['target_amount'] = $validated['target_amount'] ?? 0;

            Offer::create($validated);

            return redirect()->route('admin.offers.index')
                ->with('success', 'Time-sensitive offer created successfully!');
        } catch (\Throwable $e) {
            Log::error('Error storing offer: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to create offer. ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit an existing offer.
     */
    public function edit($id)
    {
        $offer = Offer::findOrFail($id);
        return view('admin.offers.edit', compact('offer'));
    }

    /**
     * Update an existing offer.
     */
    public function update(Request $request, $id)
    {
        $offer = Offer::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:2048',
            'reward_value' => 'required|numeric|min:0',
            'reward_type' => 'required|in:cash,gift,trip,gadget,custom',
            'target_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'is_active' => 'boolean',
        ]);

        try {
            if ($request->hasFile('image')) {
                if ($offer->image) {
                    Storage::disk('public')->delete($offer->image);
                }
                $validated['image'] = $request->file('image')->store('offers', 'public');
            }

            $validated['is_active'] = $request->has('is_active') ? (bool) $request->is_active : false;
            $validated['target_amount'] = $validated['target_amount'] ?? 0;

            $offer->update($validated);

            return redirect()->route('admin.offers.index')
                ->with('success', 'Offer updated successfully!');
        } catch (\Throwable $e) {
            Log::error('Error updating offer #' . $id . ': ' . $e->getMessage());
            return back()->withInput()->with('error', 'Failed to update offer.');
        }
    }

    /**
     * Delete an offer.
     */
    public function destroy($id)
    {
        try {
            $offer = Offer::findOrFail($id);
            if ($offer->image) {
                Storage::disk('public')->delete($offer->image);
            }
            $offer->delete();

            return redirect()->route('admin.offers.index')
                ->with('success', 'Offer deleted successfully!');
        } catch (\Throwable $e) {
            Log::error('Error deleting offer #' . $id . ': ' . $e->getMessage());
            return back()->with('error', 'Failed to delete offer.');
        }
    }
}

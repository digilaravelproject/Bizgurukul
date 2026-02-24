<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Achievement;
use App\Http\Requests\Admin\StoreAchievementRequest;
use App\Http\Requests\Admin\UpdateAchievementRequest;
use Illuminate\Support\Facades\Storage;
use Exception;

class AchievementController extends Controller
{
    public function index()
    {
        $achievements = Achievement::orderBy('priority', 'asc')
            ->orderBy('target_amount', 'asc')
            ->get();

        return view('admin.achievements.index', compact('achievements'));
    }

    public function create()
    {
        return view('admin.achievements.create');
    }

    public function store(StoreAchievementRequest $request)
    {
        try {
            $data = $request->validated();
            $data['status'] = $request->boolean('status');

            if ($request->hasFile('reward_image')) {
                $data['reward_image'] = $request->file('reward_image')->store('rewards', 'public');
            }

            Achievement::create($data);

            return redirect()->route('admin.achievements.index')
                ->with('success', 'Achievement created successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error creating achievement: ' . $e->getMessage())->withInput();
        }
    }

    public function edit(Achievement $achievement)
    {
        return view('admin.achievements.edit', compact('achievement'));
    }

    public function update(UpdateAchievementRequest $request, Achievement $achievement)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('reward_image')) {
                // Delete old image
                if ($achievement->reward_image) {
                    Storage::disk('public')->delete($achievement->reward_image);
                }
                $data['reward_image'] = $request->file('reward_image')->store('rewards', 'public');
            }
            $data['status'] = $request->boolean('status');

            $achievement->update($data);

            return redirect()->route('admin.achievements.index')
                ->with('success', 'Achievement updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error updating achievement: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(Achievement $achievement)
    {
        try {
            // Delete image if exists
            if ($achievement->reward_image) {
                Storage::disk('public')->delete($achievement->reward_image);
            }

            $achievement->delete();

            return back()->with('success', 'Achievement deleted successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error deleting achievement: ' . $e->getMessage());
        }
    }

    public function toggleStatus(Achievement $achievement)
    {
        try {
            $achievement->update(['status' => !$achievement->status]);
            return response()->json([
                'success' => true,
                'status' => $achievement->status,
                'message' => 'Status updated successfully.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status.'
            ], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        $user = Auth::user()->load(['kyc', 'bank']);
        return view('student.profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'mobile' => 'required|numeric|digits:10',
            'gender' => 'required|in:male,female,other',
            'dob' => 'required|date',
            'state_id' => 'required',
            'city' => 'required|string',
            'password' => 'nullable|min:6'
        ]);

        try {
            $this->profileService->updateProfile($user->id, $request->all());
            return response()->json(['status' => true, 'message' => 'Profile updated successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Update failed'], 500);
        }
    }

    public function submitKyc(Request $request)
    {
        $request->validate([
            'pan_name' => 'required|string',
            'document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048', // Nullable only if re-submitting without file change logic is handled
        ]);

        // Ensure file is present if new submission
        if(!Auth::user()->kyc && !$request->hasFile('document')) {
             return response()->json(['status' => false, 'message' => 'Document is required'], 422);
        }

        try {
            $this->profileService->submitKyc(Auth::id(), $request->all());
            return response()->json(['status' => true, 'message' => 'KYC Submitted']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function saveBank(Request $request)
    {
        $request->validate([
            'bank_name' => 'required',
            'holder_name' => 'required',
            'account_number' => 'required|confirmed',
            'ifsc_code' => 'required',
        ]);

        try {
            $this->profileService->saveBankDetails(Auth::id(), $request->all());
            return response()->json(['status' => true, 'message' => 'Bank Saved']);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => 'Failed'], 500);
        }
    }
}

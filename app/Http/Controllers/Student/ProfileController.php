<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\State;

class ProfileController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index()
    {
        try {
            $user = Auth::user()->load(['kyc', 'bank']);
            $states = State::select('id', 'name')->orderBy('name', 'asc')->get();
            return view('student.profile.index', compact('user', 'states'));
        } catch (\Exception $e) {
            Log::error("Error loading profile index for user " . Auth::id() . ": " . $e->getMessage());
            return back()->with('error', 'Something went wrong while loading your profile.');
        }
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'mobile' => 'required|numeric|digits:10',
                'gender' => 'required|in:male,female,other',
                'dob' => 'required|date',
                'state_id' => 'required'
            ]);

            $this->profileService->updateProfile($user->id, $request->all());
            return response()->json(['status' => true, 'message' => 'Profile updated successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error updating profile for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Update failed due to a server error.'], 500);
        }
    }

    public function changePassword(Request $request)
    {
        try {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:6|confirmed'
            ]);

            $this->profileService->changePassword(
                Auth::id(),
                $request->current_password,
                $request->new_password
            );
            return response()->json(['status' => true, 'message' => 'Password changed successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error changing password for user " . Auth::id() . ": " . $e->getMessage());
            // The service throws an exception with a specific message if current password fails
            $statusCode = $e->getMessage() === 'Incorrect current password' ? 422 : 500;
            return response()->json(['status' => false, 'message' => $e->getMessage()], $statusCode);
        }
    }

    public function submitKyc(Request $request)
    {
        try {
            $request->validate([
                'pan_name' => 'required|string',
                'document' => 'nullable|mimes:jpg,jpeg,png,pdf|max:2048',
            ]);

            // Ensure file is present if new submission
            if (!Auth::user()->kyc && !$request->hasFile('document')) {
                return response()->json(['status' => false, 'message' => 'Document is required'], 422);
            }

            $this->profileService->submitKyc(Auth::id(), $request->all());
            return response()->json(['status' => true, 'message' => 'KYC Submitted']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error submitting KYC for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to submit KYC. Please try again later.'], 500);
        }
    }

    public function saveBank(Request $request)
    {
        try {
            $request->validate([
                'bank_name' => 'required|string',
                'holder_name' => 'required|string',
                'account_number' => 'required|confirmed',
                'ifsc_code' => 'required|string',
                'document' => 'required|mimes:jpg,jpeg,png,pdf|max:3072',
            ]);

            $this->profileService->saveBankDetails(Auth::id(), $request->all());
            return response()->json(['status' => true, 'message' => 'Bank details request submitted successfully']);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['status' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error("Error saving bank details for user " . Auth::id() . ": " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to save bank details.'], 500);
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use App\Models\User;
use Illuminate\Http\Request;

class KycController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        $kycUsers = User::whereHas('kyc')->with('kyc')->latest()->paginate(15);
        return view('admin.kyc.index', compact('kycUsers'));
    }

    public function show($id)
    {
        $user = User::with(['kyc', 'bank'])->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'user_name' => $user->name, // Profile Name
                'user_dob' => $user->dob ? $user->dob->format('d M, Y') : 'Not Provided', // Profile DOB
                'user_email' => $user->email,

                // KYC Data
                'kyc_status' => $user->kyc->status,
                'pan_name' => $user->kyc->pan_name, // Submitted Name
                'doc_url' => asset('storage/' . $user->kyc->document_path),
                'doc_type' => pathinfo($user->kyc->document_path, PATHINFO_EXTENSION) == 'pdf' ? 'pdf' : 'image',
                'submitted_at' => $user->kyc->updated_at->format('d M, Y h:i A')
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:verified,rejected',
            'note' => 'required_if:status,rejected'
        ]);

        $this->profileService->updateKycStatus($id, $request->status, $request->note);
        return response()->json(['status' => true, 'message' => 'KYC Status Updated']);
    }
}

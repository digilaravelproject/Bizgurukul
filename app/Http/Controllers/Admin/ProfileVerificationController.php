<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ProfileService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProfileVerificationController extends Controller
{
    protected $profileService;

    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function index(Request $request)
    {
        // 1. Pending KYC Requests
        $pendingKyc = User::whereHas('kyc', function($q) {
            $q->where('status', 'pending');
        })->with(['kyc', 'referrer'])->latest()->paginate(10, ['*'], 'kyc_page');

        // 2. Pending Bank Initial Setup
        $pendingBankInitial = \App\Models\BankDetail::where('status', 'pending')
            ->with(['user.referrer'])
            ->latest()
            ->get();

        // 3. Pending Bank Update Requests
        $pendingBankUpdates = \App\Models\BankUpdateRequest::where('status', 'pending')
            ->with(['user.referrer'])
            ->latest()
            ->get();

        // Attach old data to update requests
        foreach ($pendingBankUpdates as $req) {
            $currentBank = \App\Models\BankDetail::where('user_id', $req->user_id)->first();
            $req->old_data = $currentBank ? [
                'bank_name' => $currentBank->bank_name,
                'account_number' => $currentBank->account_number,
                'holder_name' => $currentBank->account_holder_name,
                'ifsc_code' => $currentBank->ifsc_code,
            ] : null;
        }

        return view('admin.verifications.index', compact('pendingKyc', 'pendingBankInitial', 'pendingBankUpdates'));
    }

    public function checkNew()
    {
        $kycCount = User::whereHas('kyc', function($q) { $q->where('status', 'pending'); })->count();
        $bankInitialCount = \App\Models\BankDetail::where('status', 'pending')->count();
        $bankUpdateCount = \App\Models\BankUpdateRequest::where('status', 'pending')->count();

        return response()->json([
            'total_pending' => $kycCount + $bankInitialCount + $bankUpdateCount,
            'kyc' => $kycCount,
            'bank' => $bankInitialCount + $bankUpdateCount
        ]);
    }

    public function kycIndex(Request $request) { return $this->index($request); }
    public function bankIndex(Request $request) { return $this->index($request); }

    public function kycApprove(Request $request, $userId)
    {
        try {
            $this->profileService->updateKycStatus($userId, 'verified', $request->admin_note);
            return response()->json(['status' => true, 'message' => 'KYC verified successfully.']);
        } catch (\Exception $e) {
            Log::error("KYC Approval Error: " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to approve KYC.'], 500);
        }
    }

    public function kycReject(Request $request, $userId)
    {
        try {
            $this->profileService->updateKycStatus($userId, 'rejected', $request->admin_note);
            return response()->json(['status' => true, 'message' => 'KYC rejected.']);
        } catch (\Exception $e) {
            Log::error("KYC Rejection Error: " . $e->getMessage());
            return response()->json(['status' => false, 'message' => 'Failed to reject KYC.'], 500);
        }
    }

    public function verifyInitialBank(Request $request, $bankId)
    {
        try {
            $status = $request->action === 'approve' ? 'verified' : 'rejected';
            $this->profileService->verifyInitialBank($bankId, $status, $request->admin_note);
            return back()->with('success', 'Bank verification processed.');
        } catch (\Exception $e) {
            Log::error("Bank verification processing Error: " . $e->getMessage());
            return back()->with('error', 'Failed to process bank verification.');
        }
    }

    public function processBankUpdate(Request $request, $requestId)
    {
        try {
            $status = $request->action === 'approve' ? 'approved' : 'rejected';
            $this->profileService->processBankUpdate($requestId, $status, $request->admin_note);
            return back()->with('success', 'Bank update request processed.');
        } catch (\Exception $e) {
            Log::error("Bank update processing Error: " . $e->getMessage());
            return back()->with('error', 'Failed to process bank update request.');
        }
    }
}

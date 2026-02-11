<?php

namespace App\Services;

use App\Models\User;
use App\Models\KycDetail;
use App\Models\BankDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class ProfileService
{
    // --- STUDENT: Update Profile (Custom) ---
    public function updateProfile($userId, $data)
    {
        $user = User::findOrFail($userId);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'state_id' => $data['state_id'],
            'zip_code' => $data['zip_code'] ?? $user->zip_code,
            'address' => $data['address'] ?? $user->address,
        ];

        $user->update($updateData);
        return $user;
    }

    // --- STUDENT: Change Password ---
    public function changePassword($userId, $currentPassword, $newPassword)
    {
        $user = User::findOrFail($userId);

        // Verify current password
        if (!Hash::check($currentPassword, $user->password)) {
            throw new \Exception('Current password is incorrect');
        }

        // Update to new password
        $user->update([
            'password' => Hash::make($newPassword)
        ]);

        return $user;
    }

    // --- STUDENT: Submit KYC ---
    public function submitKyc($userId, $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);

            $path = null;
            if (isset($data['document']) && $data['document']->isValid()) {
                if ($user->kyc && $user->kyc->document_path) {
                    Storage::disk('public')->delete($user->kyc->document_path);
                }
                $path = $data['document']->store('kyc_documents', 'public');
            }

            KycDetail::updateOrCreate(
                ['user_id' => $userId],
                [
                    'pan_name' => $data['pan_name'],
                    'document_path' => $path ?? ($user->kyc->document_path ?? null),
                    'document_type' => 'pan_card',
                    'status' => 'pending',
                    'admin_note' => null
                ]
            );

            $user->update(['kyc_status' => 'pending']);
            return true;
        });
    }

    // --- STUDENT: Save Bank ---
    public function saveBankDetails($userId, $data)
    {
        return BankDetail::updateOrCreate(
            ['user_id' => $userId],
            [
                'bank_name' => $data['bank_name'],
                'account_holder_name' => $data['holder_name'],
                'account_number' => $data['account_number'],
                'ifsc_code' => strtoupper($data['ifsc_code']),
                'upi_id' => $data['upi_id'] ?? null,
            ]
        );
    }

    // --- ADMIN: Get KYC List ---
    public function getKycRequests($status = 'pending')
    {
        return User::whereHas('kyc', function ($q) use ($status) {
            // Filter by status if needed, default all or pending
            if ($status !== 'all')
                $q->where('status', $status);
        })->with('kyc')->latest()->paginate(15);
    }

    // --- ADMIN: Update Status ---
    public function updateKycStatus($userId, $status, $note = null)
    {
        $user = User::findOrFail($userId);
        $user->kyc()->update([
            'status' => $status,
            'admin_note' => $note,
            'verified_at' => $status === 'verified' ? now() : null
        ]);
        $user->update(['kyc_status' => $status]);
        return true;
    }
}

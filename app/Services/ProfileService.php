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

        // Fields that cannot be changed after registration
        // 'name', 'email', 'mobile', 'dob' are locked.

        $updateData = [
            'gender' => $data['gender'] ?? $user->gender,
            'state_id' => $data['state_id'] ?? $user->state_id,
            'zip_code' => $data['zip_code'] ?? $user->zip_code,
            'address' => $data['address'] ?? $user->address,
        ];

        // Only allow updating non-locked fields
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

            $path = $user->kyc->document_path ?? null;
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
                    'document_path' => $path,
                    'document_type' => 'id_proof',
                    'status' => 'pending',
                    'admin_note' => null
                ]
            );

            $user->update(['kyc_status' => 'pending']);
            return true;
        });
    }

    // --- STUDENT: Save/Request Bank Details ---
    public function saveBankDetails($userId, $data)
    {
        return DB::transaction(function () use ($userId, $data) {
            $user = User::findOrFail($userId);
            $existingBank = $user->bank;

            $path = null;
            if (isset($data['document']) && $data['document']->isValid()) {
                $path = $data['document']->store('bank_documents', 'public');
            }

            // If bank details already exist and are verified, create an update request
            if ($existingBank && $existingBank->status === 'verified') {
                return \App\Models\BankUpdateRequest::create([
                    'user_id' => $userId,
                    'bank_name' => $data['bank_name'],
                    'account_holder_name' => $data['holder_name'],
                    'account_number' => $data['account_number'],
                    'ifsc_code' => strtoupper($data['ifsc_code']),
                    'upi_id' => $data['upi_id'] ?? null,
                    'document_path' => $path,
                    'status' => 'pending'
                ]);
            }

            // Otherwise, update or create the primary bank details record
            if ($existingBank && $existingBank->document_path && $path) {
                Storage::disk('public')->delete($existingBank->document_path);
            }

            return BankDetail::updateOrCreate(
                ['user_id' => $userId],
                [
                    'bank_name' => $data['bank_name'],
                    'account_holder_name' => $data['holder_name'],
                    'account_number' => $data['account_number'],
                    'ifsc_code' => strtoupper($data['ifsc_code']),
                    'upi_id' => $data['upi_id'] ?? null,
                    'document_path' => $path ?? ($existingBank->document_path ?? null),
                    'status' => 'pending',
                    'is_verified' => false
                ]
            );
        });
    }

    // --- ADMIN: Get KYC List ---
    public function getKycRequests($status = 'pending')
    {
        return User::whereHas('kyc', function ($q) use ($status) {
            if ($status !== 'all')
                $q->where('status', $status);
        })->with('kyc')->latest()->paginate(15);
    }

    // --- ADMIN: Update Status ---
    public function updateKycStatus($userId, $status, $note = null)
    {
        return DB::transaction(function () use ($userId, $status, $note) {
            $user = User::findOrFail($userId);
            $user->kyc()->update([
                'status' => $status,
                'admin_note' => $note,
                'verified_at' => $status === 'verified' ? now() : null
            ]);
            $user->update(['kyc_status' => $status]);

            // Optional: Send Email Notification

            return true;
        });
    }

    // --- ADMIN: Get Bank Requests ---
    public function getBankRequests($status = 'pending')
    {
        return \App\Models\BankUpdateRequest::with('user')
            ->when($status !== 'all', function($q) use ($status) {
                return $q->where('status', $status);
            })->latest()->paginate(15);
    }

    // --- ADMIN: Get Initial Bank Verification List ---
    public function getInitialBankRequests()
    {
        return BankDetail::with('user')
            ->where('status', 'pending')
            ->latest()->paginate(15);
    }

    // --- ADMIN: Process Initial Bank Verification ---
    public function verifyInitialBank($bankId, $status, $note = null)
    {
        return DB::transaction(function () use ($bankId, $status, $note) {
            $bank = BankDetail::findOrFail($bankId);
            $bank->update([
                'status' => $status,
                'admin_note' => $note,
                'is_verified' => $status === 'verified',
                'verified_at' => $status === 'verified' ? now() : null
            ]);

            return true;
        });
    }

    // --- ADMIN: Process Bank Update Request ---
    public function processBankUpdate($requestId, $status, $note = null)
    {
        return DB::transaction(function () use ($requestId, $status, $note) {
            $request = \App\Models\BankUpdateRequest::findOrFail($requestId);

            $request->update([
                'status' => $status,
                'admin_note' => $note,
                'processed_by' => \Illuminate\Support\Facades\Auth::id(),
                'processed_at' => now()
            ]);

            if ($status === 'approved') {
                $bank = BankDetail::where('user_id', $request->user_id)->first();

                // If there's an old document, we might want to delete it
                if ($bank->document_path && $bank->document_path !== $request->document_path) {
                    Storage::disk('public')->delete($bank->document_path);
                }

                $bank->update([
                    'bank_name' => $request->bank_name,
                    'account_holder_name' => $request->account_holder_name,
                    'account_number' => $request->account_number,
                    'ifsc_code' => $request->ifsc_code,
                    'upi_id' => $request->upi_id,
                    'document_path' => $request->document_path,
                    'status' => 'verified',
                    'is_verified' => true,
                    'verified_at' => now(),
                    'admin_note' => 'Approved via update request.'
                ]);
            }

            return true;
        });
    }
}

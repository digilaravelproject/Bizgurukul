<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserService
{
    public function getUsers($perPage = 15, $search = null, $viewTrash = 'false')
    {
        $query = User::with('roles');

        if ($viewTrash === 'true') {
            $query->onlyTrashed();
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('mobile', 'like', "%{$search}%")
                    ->orWhere('referral_code', 'like', "%{$search}%");
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function createUser($data)
    {
        return DB::transaction(function () use ($data) {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'mobile' => $data['mobile'],
                'gender' => $data['gender'] ?? null,
                'dob' => $data['dob'] ?? null,
                'state_id' => $data['state_id'] ?? null, // Assuming state dropdown sends ID
                'city' => $data['city'] ?? null,
                'password' => Hash::make($data['password']),
                'kyc_status' => $data['kyc_status'] ?? 'not_submitted',
                'is_active' => 1,
            ];

            // Referral Code (Optional manual entry, else auto-gen via Model)
            if (!empty($data['referral_code'])) {
                $userData['referral_code'] = $data['referral_code'];
            }

            $user = User::create($userData);

            if (!empty($data['role'])) {
                $user->assignRole($data['role']);
            }

            return $user;
        });
    }

    public function updateUser($id, $data)
    {
        $user = User::findOrFail($id);

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'gender' => $data['gender'],
            'dob' => $data['dob'],
            'state_id' => $data['state_id'],
            'city' => $data['city'],
            'kyc_status' => $data['kyc_status'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        // Allow updating referral code if needed
        if (!empty($data['referral_code'])) {
            $updateData['referral_code'] = $data['referral_code'];
        }

        $user->update($updateData);

        if (!empty($data['role'])) {
            $user->syncRoles([$data['role']]);
        }

        return $user;
    }

    public function toggleBan($id)
    {
        $user = User::findOrFail($id);
        $user->is_banned = !$user->is_banned;
        $user->banned_at = $user->is_banned ? now() : null;
        $user->save();
        return $user;
    }

    public function deleteUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return true;
    }

    public function restoreUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return true;
    }

    public function forceDeleteUser($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->forceDelete();
        return true;
    }

    public function getUserDetails($id)
    {
        $user = User::with(['roles', 'referrer'])->find($id);
        if (!$user) {
            $user = User::withTrashed()->with(['roles', 'referrer'])->find($id);
        }
        return $user;
    }
}

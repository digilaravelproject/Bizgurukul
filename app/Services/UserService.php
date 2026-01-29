<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Exception;

class UserService
{
    protected $userRepo;

    public function __construct(UserRepository $userRepo)
    {
        $this->userRepo = $userRepo;
    }

    public function getUsers($perPage = 15, $search = null, $viewTrash = 'false')
    {
        try {
            return $this->userRepo->getPaginatedUsers($perPage, $search, $viewTrash);
        } catch (Exception $e) {
            Log::error("UserService Error [getUsers]: " . $e->getMessage());
            throw new Exception("Unable to load users list.");
        }
    }

    public function createUser(array $data)
    {
        return DB::transaction(function () use ($data) {
            try {
                // Prepare data
                $userData = [
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'mobile' => $data['mobile'] ?? null,
                    'gender' => $data['gender'] ?? null,
                    'dob' => $data['dob'] ?? null,
                    'state_id' => $data['state_id'] ?? null,
                    'city' => $data['city'] ?? null,
                    'password' => Hash::make($data['password']),
                    'kyc_status' => $data['kyc_status'] ?? 'not_submitted',
                    'is_active' => 1,
                    'referral_code' => $data['referral_code'] ?? null,
                ];

                // Create User
                $user = $this->userRepo->create($userData);

                // Assign Role
                if (!empty($data['role'])) {
                    $user->assignRole($data['role']);
                }

                return $user;

            } catch (Exception $e) {
                Log::error("UserService Error [createUser]: " . $e->getMessage());
                throw new Exception("Failed to create user. Please check data.");
            }
        });
    }

    public function updateUser($id, array $data)
    {
        return DB::transaction(function () use ($id, $data) {
            try {
                // LOCK the user row so no one else edits it right now
                $user = $this->userRepo->findForUpdate($id);

                if (!$user) throw new Exception("User not found.");

                $updateData = collect($data)->except(['role', 'password'])->toArray();

                // Hash Password if provided
                if (!empty($data['password'])) {
                    $updateData['password'] = Hash::make($data['password']);
                }

                // Update Data
                $this->userRepo->update($user, $updateData);

                // Sync Role
                if (!empty($data['role'])) {
                    $user->syncRoles([$data['role']]);
                }

                return $user;

            } catch (Exception $e) {
                Log::error("UserService Error [updateUser]: " . $e->getMessage());
                throw new Exception("Failed to update user details.");
            }
        });
    }

    public function getUserDetails($id)
    {
        try {
            // Try finding normally, if not found try in trash
            $user = $this->userRepo->findById($id) ?: $this->userRepo->findById($id, true);

            if (!$user) throw new Exception("User not found.");

            return $user;
        } catch (Exception $e) {
            Log::error("UserService Error [getUserDetails]: " . $e->getMessage());
            throw $e;
        }
    }

    public function toggleBan($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                // Lock row
                $user = $this->userRepo->findForUpdate($id);
                if (!$user) throw new Exception("User not found.");

                $user->is_banned = !$user->is_banned;
                $user->banned_at = $user->is_banned ? now() : null;
                $user->save();

                return $user;
            } catch (Exception $e) {
                Log::error("UserService Error [toggleBan]: " . $e->getMessage());
                throw new Exception("Failed to change ban status.");
            }
        });
    }

    public function deleteUser($id)
    {
        try {
            $user = $this->userRepo->findById($id);
            if (!$user) throw new Exception("User not found.");
            return $this->userRepo->delete($user);
        } catch (Exception $e) {
            Log::error("UserService Error [deleteUser]: " . $e->getMessage());
            throw new Exception("Failed to move user to trash.");
        }
    }

    public function restoreUser($id)
    {
        try {
            $user = $this->userRepo->findById($id, true);
            if (!$user) throw new Exception("User not found in trash.");
            return $this->userRepo->restore($user);
        } catch (Exception $e) {
            Log::error("UserService Error [restoreUser]: " . $e->getMessage());
            throw new Exception("Failed to restore user.");
        }
    }

    public function forceDeleteUser($id)
    {
        try {
            $user = $this->userRepo->findById($id, true);
            if (!$user) throw new Exception("User not found.");
            return $this->userRepo->forceDelete($user);
        } catch (Exception $e) {
            Log::error("UserService Error [forceDeleteUser]: " . $e->getMessage());
            throw new Exception("Failed to permanently delete user.");
        }
    }
}

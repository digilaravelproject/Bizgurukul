<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    // List with Pagination, Search & Trash logic
public function getPaginatedUsers($perPage, $search, $viewTrash)
{
    $searchTerm = trim($search);

    return $this->model->query()
        // Optimization: Sirf table ke liye zaroori columns hi fetch karein
        ->select('id', 'name', 'email', 'profile_picture', 'referral_code', 'kyc_status', 'is_banned')
        ->with(['roles:id,name'])
        ->when($viewTrash === 'true', function ($q) {
            return $q->onlyTrashed();
        })
        ->when($searchTerm, function ($q) use ($searchTerm) {
            $q->where(function ($sub) use ($searchTerm) {
                $sub->where('name', 'like', "%{$searchTerm}%")
                  ->orWhere('email', 'like', "%{$searchTerm}%")
                  ->orWhere('mobile', 'like', "%{$searchTerm}%")
                  ->orWhere('referral_code', 'like', "%{$searchTerm}%");
            });
        })
        ->latest()
        ->paginate($perPage);
}

    // **Aapki Requirement:** Active & Unbanned Users List
    public function getActiveUnbannedUsers()
    {
        return $this->model->where('is_active', 1)
                           ->where('is_banned', 0)
                           ->get();
    }

    // Find User (Normal)
    public function findById($id, $withTrashed = false)
    {
        $query = $this->model->with(['roles', 'referrer']);
        return $withTrashed ? $query->withTrashed()->find($id) : $query->find($id);
    }

    // **DB Locking:** Find User and Lock Row (for Updates/Transactions)
    public function findForUpdate($id, $withTrashed = false)
    {
        $query = $this->model->with(['roles']);
        if ($withTrashed) {
            $query->withTrashed();
        }
        // Ye line do logo ko same time edit karne se rokegi
        return $query->lockForUpdate()->find($id);
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update(User $user, array $data)
    {
        $user->update($data);
        return $user;
    }

    public function delete(User $user)
    {
        return $user->delete();
    }

    public function restore(User $user)
    {
        return $user->restore();
    }

    public function forceDelete(User $user)
    {
        return $user->forceDelete();
    }
}

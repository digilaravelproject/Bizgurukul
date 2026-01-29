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
        $query = $this->model->with('roles');

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

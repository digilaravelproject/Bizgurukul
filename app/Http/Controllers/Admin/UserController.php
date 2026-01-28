<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = $this->userService->getUsers(
                15,
                $request->get('search'),
                $request->get('trash', 'false')
            );
            return response()->json(['status' => true, 'data' => $users]);
        }

        $roles = Role::all();
        return view('admin.users.index', compact('roles'));
    }

    public function show($id)
    {
        // View Modal ke liye Data
        $user = $this->userService->getUserDetails($id);

        // State Name Map (Temporary mapping since we are using IDs in DB but need names for View)
        // Ideally, you should have a states table and relationship.
        // For now, returning raw ID or you can map it in frontend.

        return response()->json([
            'status' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'mobile' => $user->mobile,
                'gender' => ucfirst($user->gender),
                'dob' => $user->dob ? $user->dob->format('d M, Y') : 'N/A',
                'city' => $user->city,
                'state_id' => $user->state_id, // Pass ID to frontend to map to name
                'referral_code' => $user->referral_code,
                'role' => $user->roles->pluck('name')->implode(', '),
                'kyc_status' => $user->kyc_status,
                'status' => $user->is_active ? 'Active' : 'Inactive',
                'is_banned' => $user->is_banned,
                'joined_at' => $user->created_at->format('d M, Y'),
                'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                'initials' => strtoupper(substr($user->name, 0, 1)),
                'referred_by' => $user->referrer ? $user->referrer->name : 'Direct'
            ]
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'role' => 'required',
            'mobile' => 'nullable|numeric|digits:10',
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
        ]);

        $this->userService->createUser($request->all());
        return response()->json(['status' => true, 'message' => 'User created successfully']);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'mobile' => 'nullable|numeric|digits:10',
        ]);

        $this->userService->updateUser($id, $request->all());
        return response()->json(['status' => true, 'message' => 'User updated successfully']);
    }

    public function toggleBan($id)
    {
        $this->userService->toggleBan($id);
        return response()->json(['status' => true, 'message' => 'User status updated']);
    }

    public function destroy($id)
    {
        $this->userService->deleteUser($id);
        return response()->json(['status' => true, 'message' => 'User moved to trash']);
    }

    public function restore($id)
    {
        $this->userService->restoreUser($id);
        return response()->json(['status' => true, 'message' => 'User restored successfully']);
    }

    public function forceDelete($id)
    {
        $this->userService->forceDeleteUser($id);
        return response()->json(['status' => true, 'message' => 'User permanently deleted']);
    }
}

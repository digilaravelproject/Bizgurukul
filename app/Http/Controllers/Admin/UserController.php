<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Exception;

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
        try {
            $users = $this->userService->getUsers(
                15,
                $request->get('search'),
                $request->get('trash', 'false')
            );
            return response()->json(['status' => true, 'data' => $users]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    $roles = Role::all();
    return view('admin.users.index', compact('roles'));
}

    public function show($id)
    {
        try {
            $user = $this->userService->getUserDetails($id);

            // Formatting Response as per your original code
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
                    'state_id' => $user->state_id,
                    'referral_code' => $user->referral_code,
                    'role' => $user->roles->pluck('name')->implode(', '),
                    'kyc_status' => $user->kyc_status,
                    'status' => $user->is_active ? 'Active' : 'Inactive',
                    'is_banned' => $user->is_banned,
                    'joined_at' => $user->created_at->format('d M, Y'),
                    'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                    'initials' => strtoupper(substr($user->name, 0, 1)),
                    'address' => $user->address,
                    'zip_code' => $user->zip_code,
                    'referred_by' => $user->referrer ? $user->referrer->name : 'Direct',
                    'sponsor_name' => $user->referrer ? $user->referrer->name : 'N/A',
                    'sponsor_mobile' => $user->referrer ? $user->referrer->mobile : 'N/A',

                    // Affiliate Stats
                    'referral_count' => $user->referrals()->count(),
                    'total_earnings' => $user->commissions()->where('status', 'paid')->sum('amount'),
                    'pending_earnings' => $user->commissions()->where('status', 'pending')->sum('amount'),
                    // Using the accessor we added earlier
                    'wallet_balance' => $user->wallet_balance,
                ]
            ]);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 404);
        }
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

        try {
            $this->userService->createUser($request->all());
            return response()->json(['status' => true, 'message' => 'New user added successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,' . $id,
            'role' => 'required',
            'mobile' => 'nullable|numeric|digits:10',
        ]);

        try {
            $this->userService->updateUser($id, $request->all());
            return response()->json(['status' => true, 'message' => 'User details updated successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function toggleBan($id)
    {
        try {
            $this->userService->toggleBan($id);
            return response()->json(['status' => true, 'message' => 'User access status updated.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $this->userService->deleteUser($id);
            return response()->json(['status' => true, 'message' => 'User moved to trash.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function restore($id)
    {
        try {
            $this->userService->restoreUser($id);
            return response()->json(['status' => true, 'message' => 'User restored successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function forceDelete($id)
    {
        try {
            $this->userService->forceDeleteUser($id);
            return response()->json(['status' => true, 'message' => 'User permanently deleted from database.']);
        } catch (Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}

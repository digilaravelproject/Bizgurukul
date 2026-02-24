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
    $states = \App\Models\State::orderBy('name')->get();
    return view('admin.users.index', compact('roles', 'states'));
}

    public function show($id)
    {
        try {
            $user = $this->userService->getUserDetails($id);
            $user->load(['bank', 'roles', 'referrer']);

            return response()->json([
                'status' => true,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'mobile' => $user->mobile,
                    'gender' => $user->gender,
                    'dob' => $user->dob ? $user->dob->format('Y-m-d') : null,
                    'state_id' => $user->state_id,
                    'state_name' => $user->state?->name ?? 'N/A',
                    'city' => $user->city,
                    'role' => $user->roles->pluck('name')->implode(', '),
                    'kyc_status' => $user->kyc_status,
                    'joined_at' => $user->created_at->format('d M, Y'),
                    'profile_picture' => $user->profile_picture ? asset('storage/' . $user->profile_picture) : null,
                    'initials' => strtoupper(substr($user->name, 0, 1)),

                    // Affiliate Stats
                    'total_earnings' => $user->commissions()->where('status', 'paid')->sum('amount'),
                    'wallet_balance' => $user->wallet_balance,

                    // Bank Details
                    'bank' => $user->bank ? [
                        'name' => $user->bank->bank_name,
                        'holder' => $user->bank->account_holder_name,
                        'account' => $user->bank->account_number,
                        'ifsc' => $user->bank->ifsc_code,
                        'upi' => $user->bank->upi_id,
                        'status' => $user->bank->status ?? 'not_submitted',
                    ] : null,

                    'sponsor_name' => $user->referrer ? $user->referrer->name : 'N/A',
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
            'state_id' => 'nullable|integer',
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
            'gender' => 'nullable|in:male,female,other',
            'dob' => 'nullable|date',
            'state_id' => 'nullable|integer',
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

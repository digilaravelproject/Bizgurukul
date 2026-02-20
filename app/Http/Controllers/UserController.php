<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Arr;
use App\Models\State;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UserController extends Controller implements HasMiddleware
{
    // DEFINE MIDDLEWARE HERE
    public static function middleware(): array
    {
        return [
            new Middleware('permission:user-list', only: ['index']),
            new Middleware('permission:user-create', only: ['create', 'store']),
            new Middleware('permission:user-edit', only: ['edit', 'update']),
            new Middleware('permission:user-delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        try {
            if ($request->ajax()) {
                // DataTables ke liye JSON Data
                $data = User::with('roles')->orderBy('id', 'DESC')->get();
                return response()->json(['data' => $data]);
            }

            // Normal View Load
            return view('users.index');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("UserController Error [index]: " . $e->getMessage());
            return back()->with('error', 'Failed to load users list.');
        }
    }

    public function create()
    {
        $roles = Role::pluck('name', 'name')->all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|same:confirm-password',
                'roles' => 'required'
            ]);

            DB::beginTransaction();

            $input = $request->all();
            $input['password'] = Hash::make($input['password']);

            $user = User::create($input);
            $user->assignRole($request->input('roles'));

            DB::commit();

            // JSON Response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'redirect' => route('users.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("UserController Error [store]: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to create user due to server error.'], 500);
        }
    }

    public function show($id)
    {
        try {
            $user = User::findOrFail($id);
            return view('users.show', compact('user'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return back()->with('error', 'User not found.');
        }
    }

    public function edit($id)
    {
        try {
            $user = User::findOrFail($id);
            $roles = Role::pluck('name', 'name')->all();
            $userRole = $user->roles->pluck('name', 'name')->all();
            $states = State::orderBy('name', 'ASC')->get();

            return view('users.edit', compact('user', 'roles', 'userRole', 'states'));
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
             return back()->with('error', 'User not found.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'roles' => 'required',
                'mobile' => 'nullable|numeric',
                'state_id' => 'nullable|exists:states,id',
                'gender' => 'nullable|in:male,female,other',
                'dob' => 'nullable|date',
            ]);

            DB::beginTransaction();

            $input = $request->all();

            if (!empty($input['password'])) {
                $input['password'] = Hash::make($input['password']);
            } else {
                $input = Arr::except($input, array('password'));
            }

            $input['is_active'] = $request->has('is_active') ? 1 : 0;

            $user = User::findOrFail($id);
            $user->update($input);

            DB::table('model_has_roles')->where('model_id', $id)->delete();
            $user->assignRole($request->input('roles'));

            DB::commit();

            // JSON Response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'redirect' => route('users.index')
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => $e->errors()], 422);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("UserController Error [update] for ID {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to update user due to server error.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            User::findOrFail($id)->delete();
            DB::commit();

            // JSON Response for AJAX
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'User not found.'], 404);
        } catch (\Exception $e) {
            DB::rollBack();
            \Illuminate\Support\Facades\Log::error("UserController Error [destroy] for ID {$id}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to delete user.'], 500);
        }
    }
}

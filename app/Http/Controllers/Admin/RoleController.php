<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:manage-roles'),
        ];
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            // Return JSON for DataTable
            $roles = Role::orderBy('id', 'DESC')->get();
            return response()->json(['data' => $roles]);
        }
        return view('roles.index');
    }

    public function create()
    {
        $permissions = Permission::get();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permission' => 'required',
        ]);

        $role = Role::create(['name' => $request->name]);
        $role->syncPermissions($request->permission);

        activity()
            ->performedOn($role)
            ->event('created')
            ->log('Created role');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role created successfully',
                'redirect' => route('admin.roles.index')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role created successfully');
    }

    public function edit($id)
    {
        $role = Role::find($id);
        $permissions = Permission::get();
        $rolePermissions = DB::table("role_has_permissions")
            ->where("role_has_permissions.role_id", $id)
            ->pluck('role_has_permissions.permission_id', 'role_has_permissions.permission_id')
            ->all();

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'permission' => 'required',
        ]);

        $role = Role::find($id);
        $role->name = $request->name;
        $role->save();
        $role->syncPermissions($request->permission);

        activity()
            ->performedOn($role)
            ->event('updated')
            ->log('Updated role');

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role updated successfully',
                'redirect' => route('admin.roles.index')
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role updated successfully');
    }

    public function destroy($id)
    {
        $role = Role::find($id);
        if ($role) {
            activity()
                ->performedOn($role)
                ->event('deleted')
                ->log('Deleted role');
        }
        DB::table("roles")->where('id', $id)->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Role deleted successfully'
            ]);
        }

        return redirect()->route('admin.roles.index')->with('success', 'Role deleted successfully');
    }
}

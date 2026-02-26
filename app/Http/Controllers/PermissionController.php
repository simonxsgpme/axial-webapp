<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::latest()->get();
        return view('permissions.index', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
        ]);

        $permission = Permission::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        // Auto-fill role_permissions pour tous les rôles existants avec status = false
        $roles = Role::all();
        foreach ($roles as $role) {
            RolePermission::create([
                'role_uuid' => $role->uuid,
                'permission_uuid' => $permission->uuid,
                'status' => false,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Permission créée avec succès.', 'urlback' => 'back']);
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->uuid . ',uuid',
        ]);

        $permission->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json(['success' => true, 'message' => 'Permission modifiée avec succès.', 'urlback' => 'back']);
    }

    public function destroy(Permission $permission)
    {
        $permission->rolePermissions()->delete();
        $permission->delete();

        return response()->json(['success' => true, 'message' => 'Permission supprimée avec succès.', 'urlback' => 'back']);
    }
}

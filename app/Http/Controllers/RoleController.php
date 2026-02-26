<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::latest()->get();
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        // Auto-fill role_permissions pour toutes les permissions existantes avec status = false
        $permissions = Permission::all();
        foreach ($permissions as $permission) {
            RolePermission::create([
                'role_uuid' => $role->uuid,
                'permission_uuid' => $permission->uuid,
                'status' => false,
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Rôle créé avec succès.', 'urlback' => 'back']);
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->uuid . ',uuid',
        ]);

        $role->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json(['success' => true, 'message' => 'Rôle modifié avec succès.', 'urlback' => 'back']);
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Ce rôle est attribué à des utilisateurs et ne peut pas être supprimé.'], 422);
        }

        $role->delete();

        return response()->json(['success' => true, 'message' => 'Rôle supprimé avec succès.', 'urlback' => 'back']);
    }
}

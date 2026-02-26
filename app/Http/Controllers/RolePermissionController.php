<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function show(Role $role)
    {
        $role->load(['rolePermissions.permission']);
        return view('roles.permissions', compact('role'));
    }

    public function toggle(RolePermission $rolePermission)
    {
        $rolePermission->update([
            'status' => !$rolePermission->status,
        ]);

        $statusText = $rolePermission->status ? 'activée' : 'désactivée';

        return response()->json([
            'success' => true,
            'message' => 'Permission ' . $statusText . ' avec succès.',
            'status' => $rolePermission->status,
        ]);
    }
}

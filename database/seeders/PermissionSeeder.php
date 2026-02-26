<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\RolePermission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'Gérer les utilisateurs', 'slug' => 'gerer-les-utilisateurs'],
            ['name' => 'Gérer les rôles', 'slug' => 'gerer-les-roles'],
            ['name' => 'Gérer les permissions', 'slug' => 'gerer-les-permissions'],
            ['name' => 'Fixer les objectifs', 'slug' => 'fixer-les-objectifs'],
            ['name' => 'Valider les objectifs', 'slug' => 'valider-les-objectifs'],
            ['name' => 'Évaluer les objectifs', 'slug' => 'evaluer-les-objectifs'],
            ['name' => 'Voir le tableau de bord', 'slug' => 'voir-le-tableau-de-bord'],
        ];

        $roles = Role::all();

        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(['slug' => $permData['slug']], $permData);

            // Auto-fill role_permissions pour chaque rôle
            foreach ($roles as $role) {
                RolePermission::firstOrCreate([
                    'role_uuid' => $role->uuid,
                    'permission_uuid' => $permission->uuid,
                ], [
                    'status' => $role->slug === 'administrateur', // Admin a tout activé par défaut
                ]);
            }
        }
    }
}

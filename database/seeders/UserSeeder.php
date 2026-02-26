<?php

namespace Database\Seeders;

use App\Models\Entity;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::where('slug', 'administrateur')->first();
        $managerRole = Role::where('slug', 'manager')->first();
        $employeRole = Role::where('slug', 'employe')->first();

        $entities = Entity::all();
        $dg = $entities->where('name', 'Direction Générale')->first();
        $drh = $entities->where('name', 'Direction des Ressources Humaines')->first();
        $df = $entities->where('name', 'Direction Financière')->first();
        $si = $entities->where('name', 'Service Informatique')->first();
        $sc = $entities->where('name', 'Service Commercial')->first();
        $dj = $entities->where('name', 'Département Juridique')->first();

        $admin = User::firstOrCreate(
            ['email' => 'admin@axial.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'AXIAL',
                'full_name' => 'AXIAL Admin',
                'email' => 'admin@axial.com',
                'password' => Hash::make('password'),
                'position' => 'Administrateur Système',
                'is_active' => true,
                'role_uuid' => $adminRole?->uuid,
                'entity_uuid' => $dg?->uuid,
                'email_verified_at' => now(),
            ]
        );

        $users = [
            [
                'first_name' => 'Kouadio',
                'last_name' => 'KONAN',
                'email' => 'kouadio.konan@sgpme.ci',
                'position' => 'Directeur Général',
                'role' => $managerRole,
                'entity' => $dg,
                'supervisor' => null,
            ],
            [
                'first_name' => 'Aminata',
                'last_name' => 'DIALLO',
                'email' => 'aminata.diallo@sgpme.ci',
                'position' => 'Directrice des Ressources Humaines',
                'role' => $managerRole,
                'entity' => $drh,
                'supervisor' => 'kouadio.konan@sgpme.ci',
            ],
            [
                'first_name' => 'Jean-Marc',
                'last_name' => 'BROU',
                'email' => 'jeanmarc.brou@sgpme.ci',
                'position' => 'Directeur Financier',
                'role' => $managerRole,
                'entity' => $df,
                'supervisor' => 'kouadio.konan@sgpme.ci',
            ],
            [
                'first_name' => 'Fatou',
                'last_name' => 'COULIBALY',
                'email' => 'fatou.coulibaly@sgpme.ci',
                'position' => 'Responsable Informatique',
                'role' => $managerRole,
                'entity' => $si,
                'supervisor' => 'kouadio.konan@sgpme.ci',
            ],
            [
                'first_name' => 'Moussa',
                'last_name' => 'TRAORE',
                'email' => 'moussa.traore@sgpme.ci',
                'position' => 'Responsable Commercial',
                'role' => $managerRole,
                'entity' => $sc,
                'supervisor' => 'kouadio.konan@sgpme.ci',
            ],
            [
                'first_name' => 'Awa',
                'last_name' => 'KONE',
                'email' => 'awa.kone@sgpme.ci',
                'position' => 'Chargée de Recrutement',
                'role' => $employeRole,
                'entity' => $drh,
                'supervisor' => 'aminata.diallo@sgpme.ci',
            ],
            [
                'first_name' => 'Yao',
                'last_name' => 'ASSI',
                'email' => 'yao.assi@sgpme.ci',
                'position' => 'Comptable Senior',
                'role' => $employeRole,
                'entity' => $df,
                'supervisor' => 'jeanmarc.brou@sgpme.ci',
            ],
            [
                'first_name' => 'Mariam',
                'last_name' => 'OUATTARA',
                'email' => 'mariam.ouattara@sgpme.ci',
                'position' => 'Développeuse Web',
                'role' => $employeRole,
                'entity' => $si,
                'supervisor' => 'fatou.coulibaly@sgpme.ci',
            ],
            [
                'first_name' => 'Ibrahim',
                'last_name' => 'SANGARE',
                'email' => 'ibrahim.sangare@sgpme.ci',
                'position' => 'Commercial Terrain',
                'role' => $employeRole,
                'entity' => $sc,
                'supervisor' => 'moussa.traore@sgpme.ci',
            ],
            [
                'first_name' => 'Christelle',
                'last_name' => 'AKA',
                'email' => 'christelle.aka@sgpme.ci',
                'position' => 'Juriste',
                'role' => $employeRole,
                'entity' => $dj,
                'supervisor' => 'kouadio.konan@sgpme.ci',
            ],
        ];

        // Première passe : créer tous les utilisateurs
        foreach ($users as $userData) {
            User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'first_name' => $userData['first_name'],
                    'last_name' => $userData['last_name'],
                    'full_name' => $userData['last_name'] . ' ' . $userData['first_name'],
                    'email' => $userData['email'],
                    'password' => Hash::make('password'),
                    'position' => $userData['position'],
                    'is_active' => true,
                    'role_uuid' => $userData['role']?->uuid,
                    'entity_uuid' => $userData['entity']?->uuid,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Deuxième passe : assigner les supérieurs hiérarchiques
        foreach ($users as $userData) {
            if ($userData['supervisor']) {
                $user = User::where('email', $userData['email'])->first();
                $supervisor = User::where('email', $userData['supervisor'])->first();
                if ($user && $supervisor) {
                    $user->update(['supervisor_uuid' => $supervisor->uuid]);
                }
            }
        }
    }
}

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
            // Gestion des utilisateurs
            ['name' => 'Voir les utilisateurs', 'slug' => 'voir-utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'Créer des utilisateurs', 'slug' => 'creer-utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'Modifier des utilisateurs', 'slug' => 'modifier-utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'Supprimer des utilisateurs', 'slug' => 'supprimer-utilisateurs', 'category' => 'Utilisateurs'],
            ['name' => 'Importer des utilisateurs', 'slug' => 'importer-utilisateurs', 'category' => 'Utilisateurs'],
            
            // Gestion des rôles et permissions
            ['name' => 'Voir les rôles', 'slug' => 'voir-roles', 'category' => 'Rôles & Permissions'],
            ['name' => 'Gérer les rôles', 'slug' => 'gerer-roles', 'category' => 'Rôles & Permissions'],
            ['name' => 'Gérer les permissions', 'slug' => 'gerer-permissions', 'category' => 'Rôles & Permissions'],
            
            // Gestion des entités
            ['name' => 'Voir les entités', 'slug' => 'voir-entites', 'category' => 'Entités'],
            ['name' => 'Gérer les entités', 'slug' => 'gerer-entites', 'category' => 'Entités'],
            
            // Gestion des campagnes
            ['name' => 'Voir les campagnes', 'slug' => 'voir-campagnes', 'category' => 'Campagnes'],
            ['name' => 'Créer des campagnes', 'slug' => 'creer-campagnes', 'category' => 'Campagnes'],
            ['name' => 'Modifier des campagnes', 'slug' => 'modifier-campagnes', 'category' => 'Campagnes'],
            ['name' => 'Supprimer des campagnes', 'slug' => 'supprimer-campagnes', 'category' => 'Campagnes'],
            ['name' => 'Gérer les phases de campagne', 'slug' => 'gerer-phases-campagnes', 'category' => 'Campagnes'],
            
            // Gestion des objectifs (Collaborateur)
            ['name' => 'Voir mes objectifs', 'slug' => 'voir-mes-objectifs', 'category' => 'Objectifs'],
            ['name' => 'Créer mes objectifs', 'slug' => 'creer-mes-objectifs', 'category' => 'Objectifs'],
            ['name' => 'Modifier mes objectifs', 'slug' => 'modifier-mes-objectifs', 'category' => 'Objectifs'],
            ['name' => 'Supprimer mes objectifs', 'slug' => 'supprimer-mes-objectifs', 'category' => 'Objectifs'],
            ['name' => 'Soumettre mes objectifs', 'slug' => 'soumettre-mes-objectifs', 'category' => 'Objectifs'],
            
            // Validation des objectifs (Manager)
            ['name' => 'Voir les objectifs des collaborateurs', 'slug' => 'voir-objectifs-collaborateurs', 'category' => 'Validation'],
            ['name' => 'Valider les objectifs', 'slug' => 'valider-objectifs', 'category' => 'Validation'],
            ['name' => 'Rejeter les objectifs', 'slug' => 'rejeter-objectifs', 'category' => 'Validation'],
            ['name' => 'Télécharger fiche objectifs', 'slug' => 'telecharger-fiche-objectifs', 'category' => 'Validation'],
            
            // Évaluation mi-parcours
            ['name' => 'Modifier objectifs mi-parcours', 'slug' => 'modifier-objectifs-midterm', 'category' => 'Mi-parcours'],
            ['name' => 'Voir évaluations mi-parcours', 'slug' => 'voir-evaluations-midterm', 'category' => 'Mi-parcours'],
            ['name' => 'Télécharger fiche mi-parcours', 'slug' => 'telecharger-fiche-midterm', 'category' => 'Mi-parcours'],
            ['name' => 'Importer fiche mi-parcours', 'slug' => 'importer-fiche-midterm', 'category' => 'Mi-parcours'],
            
            // Évaluation finale
            ['name' => 'Voir les évaluations', 'slug' => 'voir-evaluations', 'category' => 'Évaluation'],
            ['name' => 'Évaluer les collaborateurs', 'slug' => 'evaluer-collaborateurs', 'category' => 'Évaluation'],
            ['name' => 'Valider les évaluations', 'slug' => 'valider-evaluations', 'category' => 'Évaluation'],
            ['name' => 'Voir mon évaluation', 'slug' => 'voir-mon-evaluation', 'category' => 'Évaluation'],
            
            // Catégories d'objectifs
            ['name' => 'Voir les catégories d\'objectifs', 'slug' => 'voir-categories-objectifs', 'category' => 'Catégories'],
            ['name' => 'Gérer les catégories d\'objectifs', 'slug' => 'gerer-categories-objectifs', 'category' => 'Catégories'],
            
            // Tableau de bord
            ['name' => 'Voir le tableau de bord', 'slug' => 'voir-tableau-de-bord', 'category' => 'Dashboard'],
            ['name' => 'Voir les statistiques globales', 'slug' => 'voir-statistiques-globales', 'category' => 'Dashboard'],
        ];

        $roles = Role::all();
        $adminRole = $roles->where('slug', 'administrateur')->first();
        $managerRole = $roles->where('slug', 'manager')->first();
        $employeRole = $roles->where('slug', 'collaborateur')->first();

        foreach ($permissions as $permData) {
            $permission = Permission::firstOrCreate(['slug' => $permData['slug']], $permData);

            // Assigner les permissions par défaut selon le rôle
            foreach ($roles as $role) {
                $status = false;
                
                // Administrateur a toutes les permissions
                if ($role->slug === 'administrateur') {
                    $status = true;
                }
                // Manager a les permissions de validation et évaluation
                elseif ($role->slug === 'manager') {
                    $managerPermissions = [
                        'voir-mes-objectifs', 'creer-mes-objectifs', 'modifier-mes-objectifs', 
                        'supprimer-mes-objectifs', 'soumettre-mes-objectifs',
                        'voir-objectifs-collaborateurs', 'valider-objectifs', 'rejeter-objectifs',
                        'telecharger-fiche-objectifs', 'voir-evaluations-midterm', 
                        'telecharger-fiche-midterm', 'importer-fiche-midterm',
                        'voir-evaluations', 'evaluer-collaborateurs', 'valider-evaluations',
                        'voir-mon-evaluation', 'voir-tableau-de-bord', 'modifier-objectifs-midterm'
                    ];
                    $status = in_array($permData['slug'], $managerPermissions);
                }
                // Collaborateur a les permissions de base
                elseif ($role->slug === 'collaborateur') {
                    $employePermissions = [
                        'voir-mes-objectifs', 'creer-mes-objectifs', 'modifier-mes-objectifs',
                        'supprimer-mes-objectifs', 'soumettre-mes-objectifs',
                        'voir-mon-evaluation', 'voir-tableau-de-bord', 'modifier-objectifs-midterm'
                    ];
                    $status = in_array($permData['slug'], $employePermissions);
                }

                RolePermission::firstOrCreate([
                    'role_uuid' => $role->uuid,
                    'permission_uuid' => $permission->uuid,
                ], [
                    'status' => $status,
                ]);
            }
        }
    }
}

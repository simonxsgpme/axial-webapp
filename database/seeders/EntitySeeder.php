<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        // Créer d'abord les entités parentes (Directions principales)
        $parentEntities = [
            ['name' => 'Direction Générale', 'acronym' => 'DG', 'category' => 'direction', 'parent_uuid' => null],
            ['name' => 'Direction Administration et Ressources', 'acronym' => 'DAR', 'category' => 'direction', 'parent_uuid' => null],
            ['name' => 'Direction des Risques', 'acronym' => 'DR', 'category' => 'direction', 'parent_uuid' => null],
            ['name' => 'Direction Commerciale', 'acronym' => 'DCOM', 'category' => 'direction', 'parent_uuid' => null],
            ['name' => 'Direction Octrois et Engagements', 'acronym' => 'DOE', 'category' => 'direction', 'parent_uuid' => null],
        ];

        foreach ($parentEntities as $entityData) {
            Entity::firstOrCreate(
                ['name' => $entityData['name']], 
                $entityData
            );
        }

        // Créer ensuite les entités enfants (Services/Départements)
        $dar = Entity::where('name', 'Direction Administration et Ressources')->first();
        $dr = Entity::where('name', 'Direction des Risques')->first();
        $dg = Entity::where('name', 'Direction Générale')->first();

        $childEntities = [
            // Sous-entités de Direction Administration et Ressources
            ['name' => 'Moyens Généraux', 'acronym' => 'MG', 'category' => 'service', 'parent_uuid' => $dar?->uuid],
            ['name' => 'Finances & Comptabilité', 'acronym' => 'FC', 'category' => 'service', 'parent_uuid' => $dar?->uuid],
            ['name' => 'Systèmes d\'Information', 'acronym' => 'SI', 'category' => 'service', 'parent_uuid' => $dar?->uuid],
            ['name' => 'Ressources Humaines', 'acronym' => 'RH', 'category' => 'service', 'parent_uuid' => $dar?->uuid],
            
            // Sous-entités de Direction des Risques
            ['name' => 'Gestion des risques et contrôle permanent', 'acronym' => 'GRCP', 'category' => 'service', 'parent_uuid' => $dr?->uuid],
            ['name' => 'RSE', 'acronym' => 'RSE', 'category' => 'service', 'parent_uuid' => $dr?->uuid],
            ['name' => 'Conformité', 'acronym' => 'CONF', 'category' => 'service', 'parent_uuid' => $dr?->uuid],
            
            // Sous-entités de Direction Générale
            ['name' => 'Juridique & Contentieux', 'acronym' => 'JC', 'category' => 'departement', 'parent_uuid' => $dg?->uuid],
            ['name' => 'Audit Interne', 'acronym' => 'AI', 'category' => 'departement', 'parent_uuid' => $dg?->uuid],
        ];

        foreach ($childEntities as $entityData) {
            Entity::firstOrCreate(
                ['name' => $entityData['name']], 
                $entityData
            );
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Entity;
use Illuminate\Database\Seeder;

class EntitySeeder extends Seeder
{
    public function run(): void
    {
        $entities = [
            ['name' => 'Direction Générale', 'category' => 'direction'],
            ['name' => 'Direction des Ressources Humaines', 'category' => 'direction'],
            ['name' => 'Direction Financière', 'category' => 'direction'],
            ['name' => 'Service Informatique', 'category' => 'service'],
            ['name' => 'Service Commercial', 'category' => 'service'],
            ['name' => 'Département Juridique', 'category' => 'departement'],
        ];

        foreach ($entities as $entity) {
            Entity::firstOrCreate(['name' => $entity['name']], $entity);
        }
    }
}

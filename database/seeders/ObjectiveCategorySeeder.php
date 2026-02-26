<?php

namespace Database\Seeders;

use App\Models\ObjectiveCategory;
use Illuminate\Database\Seeder;

class ObjectiveCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Individuel', 'description' => 'Objectifs Individuels', 'percentage' => 85],
            ['name' => 'Collectif', 'description' => 'Objectifs Collectifs', 'percentage' => 10],
            ['name' => 'Comportemental', 'description' => 'Objectifs Comportementaux', 'percentage' => 5],
        ];

        foreach ($categories as $category) {
            ObjectiveCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}

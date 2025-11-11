<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Category::insert([
            ['name' => 'Mammals', 'type' => 'animal', 'description' => 'Warm-blooded vertebrates', 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Birds', 'type' => 'animal', 'description' => 'Feathered vertebrates', 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Reptiles', 'type' => 'animal', 'description' => 'Cold-blooded vertebrates', 'display_order' => 3, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Dining', 'type' => 'facility', 'description' => 'Food and beverage facilities', 'display_order' => 1, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Amenities', 'type' => 'facility', 'description' => 'Zoo amenities', 'display_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}

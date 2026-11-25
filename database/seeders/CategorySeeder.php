<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Viewpoint', 'color' => '#0d6efd', 'is_outdoor' => true],
            ['name' => 'Café & Food', 'color' => '#fd7e14', 'is_outdoor' => false],
            ['name' => 'Nature & Parks', 'color' => '#198754', 'is_outdoor' => true],
            ['name' => 'Street Art', 'color' => '#d63384', 'is_outdoor' => true],
            ['name' => 'Historic', 'color' => '#6f42c1', 'is_outdoor' => true],
            ['name' => 'Hidden Gem', 'color' => '#ffc107', 'is_outdoor' => true],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}

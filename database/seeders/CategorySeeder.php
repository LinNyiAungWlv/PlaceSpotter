<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Viewpoint',      'slug' => 'viewpoint',    'icon' => 'bi-binoculars', 'is_outdoor' => true],
            ['name' => 'Café & Food',    'slug' => 'cafe-food',    'icon' => 'bi-cup-hot',    'is_outdoor' => false],
            ['name' => 'Nature & Parks', 'slug' => 'nature-parks', 'icon' => 'bi-tree',       'is_outdoor' => true],
            ['name' => 'Street Art',     'slug' => 'street-art',   'icon' => 'bi-palette',    'is_outdoor' => true],
            ['name' => 'Historic',       'slug' => 'historic',     'icon' => 'bi-bank',       'is_outdoor' => true],
            ['name' => 'Hidden Gem',     'slug' => 'hidden-gem',   'icon' => 'bi-gem',        'is_outdoor' => true],
        ];

        foreach ($categories as $category) {
            // updateOrCreate so the seeder can be run more than once without duplicates
            Category::updateOrCreate(['slug' => $category['slug']], $category);
        }
    }
}

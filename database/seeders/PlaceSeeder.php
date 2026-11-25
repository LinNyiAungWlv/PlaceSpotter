<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        // demo accounts for testing (password: password)
        $aung = User::create(['name' => 'Aung Aung', 'email' => 'aung@example.com', 'password' => 'password']);
        $mya = User::create(['name' => 'Mya Mya', 'email' => 'mya@example.com', 'password' => 'password']);

        $cat = Category::pluck('id', 'name');

        $places = [
            [$aung, 'Historic', 'Shwedagon Pagoda', 'The golden pagoda on Singuttara Hill. Best at sunset when the stupa glows.', 16.7983, 96.1497],
            [$aung, 'Historic', 'Sule Pagoda', 'Old pagoda right in the middle of downtown, surrounded by a busy roundabout.', 16.7746, 96.1588],
            [$mya, 'Nature & Parks', 'Kandawgyi Lake', 'Wooden boardwalk around the lake, quiet in the early morning.', 16.7924, 96.1650],
            [$mya, 'Nature & Parks', 'Inya Lake', 'Big lake popular with students for evening walks and breeze.', 16.8341, 96.1489],
            [$aung, 'Viewpoint', 'Karaweik Hall view', 'Nice view of the floating Karaweik Hall with the Shwedagon behind it.', 16.7930, 96.1600],
            [$mya, 'Café & Food', 'Rangoon Tea House', 'Modern Burmese tea shop on Pansodan Street. Try the tea leaf salad.', 16.7748, 96.1563],
            [$aung, 'Café & Food', '19th Street night food', 'Barbecue skewers and cold drinks in Chinatown every evening.', 16.7740, 96.1530],
            [$mya, 'Street Art', 'Downtown mural lane', 'Colourful murals painted on old colonial walls near Pansodan.', 16.7720, 96.1610],
            [$aung, 'Hidden Gem', 'Dala ferry crossing', 'Cheap ferry across the Yangon River with great river views.', 16.7680, 96.1560],
        ];

        foreach ($places as [$user, $category, $name, $description, $lat, $lng]) {
            $user->places()->create([
                'category_id' => $cat[$category],
                'name' => $name,
                'description' => $description,
                'latitude' => $lat,
                'longitude' => $lng,
            ]);
        }

        // a few likes and comments so the pages are not empty
        $first = $aung->places()->first();
        $first->likes()->create(['user_id' => $mya->id]);
        $first->comments()->create(['user_id' => $mya->id, 'body' => 'Went there last week, the sunset was amazing!']);
    }
}

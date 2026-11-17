<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;

class PlaceSeeder extends Seeder
{
    public function run(): void
    {
        // demo account that owns the sample places (local testing only)
        $user = User::firstOrCreate(
            ['email' => 'demo@placespotter.test'],
            ['name' => 'Demo User', 'password' => 'password']
        );

        // category id by slug, e.g. $cat['historic']
        $cat = Category::pluck('id', 'slug');

        // sample places in Yangon (coordinates from OpenStreetMap)
        $places = [
            [
                'category' => 'historic',
                'name' => 'Shwedagon Pagoda',
                'description' => 'The big golden stupa on Singuttara Hill. Best visited in the late afternoon and stay until sunset when the lights come on.',
                'latitude' => 16.7990633, 'longitude' => 96.1494229,
            ],
            [
                'category' => 'historic',
                'name' => 'Sule Pagoda',
                'description' => 'Old pagoda right in the middle of a roundabout in downtown. Good starting point for walking around the colonial buildings.',
                'latitude' => 16.7744095, 'longitude' => 96.1587656,
            ],
            [
                'category' => 'historic',
                'name' => 'The Secretariat',
                'description' => 'Huge red brick colonial building from the British era. Worth walking around the outside to see the architecture.',
                'latitude' => 16.7754655, 'longitude' => 96.1652912,
            ],
            [
                'category' => 'viewpoint',
                'name' => 'Karaweik Palace, Kandawgyi Lake',
                'description' => 'The boat-shaped hall on Kandawgyi Lake. From the boardwalk you can see Shwedagon Pagoda across the water.',
                'latitude' => 16.7952309, 'longitude' => 96.1679887,
            ],
            [
                'category' => 'viewpoint',
                'name' => 'Botahtaung Jetty',
                'description' => 'Riverside spot next to Botahtaung Pagoda. Nice place to watch the boats on the Yangon River in the evening.',
                'latitude' => 16.7671517, 'longitude' => 96.1721283,
            ],
            [
                'category' => 'cafe-food',
                'name' => '19th Street, Chinatown',
                'description' => 'Busy street full of BBQ skewer stalls and small beer stations. Comes alive after dark.',
                'latitude' => 16.7734273, 'longitude' => 96.1493975,
            ],
            [
                'category' => 'cafe-food',
                'name' => 'Rangoon Tea House',
                'description' => 'Tea house on Pansodan Street serving Burmese milk tea and mohinga in a restored old building.',
                'latitude' => 16.7722578, 'longitude' => 96.1614103,
            ],
            [
                'category' => 'nature-parks',
                'name' => 'Inya Lake',
                'description' => 'Large lake in the north of the city. The path along the shore is popular for walks and the sunset is great.',
                'latitude' => 16.8386348, 'longitude' => 96.1380135,
            ],
            [
                'category' => 'nature-parks',
                'name' => "People's Park",
                'description' => 'Big green park next to Shwedagon Pagoda with lawns, fountains and shady trees. Quiet escape from the traffic.',
                'latitude' => 16.7989366, 'longitude' => 96.1420935,
            ],
            [
                'category' => 'nature-parks',
                'name' => 'Maha Bandula Park',
                'description' => 'Small park beside Sule Pagoda with the Independence Monument in the middle. Good for a short break downtown.',
                'latitude' => 16.7725275, 'longitude' => 96.1594525,
            ],
            [
                'category' => 'hidden-gem',
                'name' => 'Bogyoke Aung San Market',
                'description' => 'Old market building with hundreds of small shops. Go upstairs for the quieter galleries and fabric shops.',
                'latitude' => 16.7802627, 'longitude' => 96.1555892,
            ],
            [
                'category' => 'hidden-gem',
                'name' => 'Dala Ferry Terminal',
                'description' => 'Short ferry ride across the river from downtown to Dala. Feels like a different world from the city.',
                'latitude' => 16.7627205, 'longitude' => 96.1566348,
            ],
        ];

        foreach ($places as $place) {
            $user->places()->updateOrCreate(
                ['name' => $place['name']],
                [
                    'category_id' => $cat[$place['category']],
                    'description' => $place['description'],
                    'latitude' => $place['latitude'],
                    'longitude' => $place['longitude'],
                ]
            );
        }
    }
}

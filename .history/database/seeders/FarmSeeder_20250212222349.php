<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use App\Models\Farm;
use App\Models\Farmer;
use App\Models\Barangay;
use App\Models\Commodity;
use Faker\Factory as Faker;

class FarmSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
{
    // Disable foreign key checks
    DB::statement('SET FOREIGN_KEY_CHECKS=0');

    // Truncate related tables in the correct order
    Farm::truncate();
    Farmer::truncate();

    // Enable foreign key checks back
    DB::statement('SET FOREIGN_KEY_CHECKS=1');

    $geojson = json_decode(file_get_contents(public_path('Digos_City.geojson')), true);
    $coordinates = $geojson['features'][0]['geometry']['coordinates'];
    $flattenedCoordinates = array_merge(...array_merge(...$coordinates));
    $latitudes = [];
    $longitudes = [];

    foreach ($flattenedCoordinates as $coordinate) {
        $longitudes[] = $coordinate[0];
        $latitudes[] = $coordinate[1];
    }

    $minLat = min($latitudes);
    $maxLat = max($latitudes);
    $minLon = min($longitudes);
    $maxLon = max($longitudes);

    $faker = Faker::create();

    for ($i = 0; $i < 20000; $i++) {
        Farm::create([
            'farmer_id' => Farmer::inRandomOrder()->first()->id,
            'brgy_id' => Barangay::inRandomOrder()->first()->id,
            'commodity_id' => Commodity::inRandomOrder()->first()->id,
            'ha' => $faker->randomFloat(2, 0, 10),
            'owner' => $faker->randomElement(['yes', 'no']),
            'name'  => fake()->company() . ' Farm',
            'latitude' => $faker->latitude($minLat, $maxLat),
            'longitude' => $faker->longitude($minLon, $maxLon),
            'created_at' => $faker->dateTimeBetween('2019-01-01', '2024-12-31'),
            'updated_at' => now(),
        ]);
    }
}
}

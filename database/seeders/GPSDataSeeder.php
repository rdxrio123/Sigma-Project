<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\GPSData;

class GPSDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        GPSData::create([
            'latitude' => -7.250445,
            'longitude' => 112.768845,
            'altitude' => 75.00,
            'satellites' => 9,
            'status' => '3D FIX',
            'recorded_at' => now(),
        ]);

        // Add more sample data
        GPSData::create([
            'latitude' => -7.250500,
            'longitude' => 112.768900,
            'altitude' => 76.50,
            'satellites' => 8,
            'status' => '3D FIX',
            'recorded_at' => now()->subMinutes(5),
        ]);
    }
}

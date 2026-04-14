<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\School;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $school1 = School::where('npsn', '20512345')->first();
        $school2 = School::where('npsn', '20512346')->first();

        if ($school1) {
            Location::create([
                'school_id' => $school1->id,
                'name' => 'Gerbang Utama SMPN 6',
                'latitude' => -8.1234567,
                'longitude' => 111.1234567,
                'radius_max' => 80,
                'is_active' => true,
            ]);

            Location::create([
                'school_id' => $school1->id,
                'name' => 'Pintu Samping SMPN 6',
                'latitude' => -8.1235000,
                'longitude' => 111.1236000,
                'radius_max' => 50,
                'is_active' => true,
            ]);
        }

        if ($school2) {
            Location::create([
                'school_id' => $school2->id,
                'name' => 'Gerbang Utama SMPN 1',
                'latitude' => -8.2000000,
                'longitude' => 111.0900000,
                'radius_max' => 80,
                'is_active' => true,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::create([
            'name' => 'SMP Negeri 6 Sudimoro',
            'npsn' => '20512345',
            'slug' => 'smpn-6-sudimoro',
            'address' => 'Jl. Raya Sudimoro No. 1, Pacitan, Jawa Timur',
            'phone' => '0357-123456',
            'email' => 'smpn6sudimoro@sch.id',
            'late_threshold' => '07:00:00',
            'checkout_time' => '14:00:00',
            'is_active' => true,
        ]);

        School::create([
            'name' => 'SMP Negeri 1 Pacitan',
            'npsn' => '20512346',
            'slug' => 'smpn-1-pacitan',
            'address' => 'Jl. Raya Pacitan No. 10, Pacitan, Jawa Timur',
            'phone' => '0357-654321',
            'email' => 'smpn1pacitan@sch.id',
            'late_threshold' => '07:15:00',
            'checkout_time' => '14:30:00',
            'is_active' => true,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin (no school)
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@absensi.com',
            'password' => Hash::make('password'),
            'role' => 'super_admin',
            'school_id' => null,
        ]);

        $school1 = School::where('npsn', '20512345')->first();
        $school2 = School::where('npsn', '20512346')->first();

        if ($school1) {
            User::create([
                'name' => 'Admin SMPN 6 Sudimoro',
                'email' => 'admin@smpn6sudimoro.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'school_id' => $school1->id,
            ]);
        }

        if ($school2) {
            User::create([
                'name' => 'Admin SMPN 1 Pacitan',
                'email' => 'admin@smpn1pacitan.sch.id',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'school_id' => $school2->id,
            ]);
        }
    }
}

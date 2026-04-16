<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $school1 = School::where('npsn', '20512345')->first();
        $school2 = School::where('npsn', '20512346')->first();

        if ($school1) {
            $students = [
                ['nisn' => '0012345001', 'name' => 'Ahmad Fauzi', 'class' => 'IX-A'],
                ['nisn' => '0012345002', 'name' => 'Siti Nurhaliza', 'class' => 'IX-A'],
                ['nisn' => '0012345003', 'name' => 'Budi Santoso', 'class' => 'IX-B'],
                ['nisn' => '0012345004', 'name' => 'Dewi Sartika', 'class' => 'VIII-A'],
                ['nisn' => '0012345005', 'name' => 'Rizki Pratama', 'class' => 'VIII-B'],
            ];

            foreach ($students as $data) {
                Student::create([
                    'school_id' => $school1->id,
                    'nisn' => $data['nisn'],
                    'name' => $data['name'],
                    'class' => $data['class'],
                    'password' => Hash::make($school1->npsn), // Default password = NPSN
                    'is_active' => true,
                    'verification_status' => 'verified',
                ]);
            }
        }

        if ($school2) {
            $students = [
                ['nisn' => '0012346001', 'name' => 'Andi Wijaya', 'class' => 'IX-A'],
                ['nisn' => '0012346002', 'name' => 'Maya Putri', 'class' => 'IX-B'],
                ['nisn' => '0012346003', 'name' => 'Dimas Prasetyo', 'class' => 'VIII-A'],
            ];

            foreach ($students as $data) {
                Student::create([
                    'school_id' => $school2->id,
                    'nisn' => $data['nisn'],
                    'name' => $data['name'],
                    'class' => $data['class'],
                    'password' => Hash::make($school2->npsn),
                    'is_active' => true,
                    'verification_status' => 'verified',
                ]);
            }
        }
    }
}

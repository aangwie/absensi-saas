<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $school1 = School::where('npsn', '20512345')->first();
        $school2 = School::where('npsn', '20512346')->first();

        if ($school1) {
            $teachers = [
                ['nip' => '198501012010011001', 'name' => 'Drs. Sugiyanto, M.Pd', 'subject' => 'Matematika', 'phone' => '08123456001'],
                ['nip' => '198601022011011002', 'name' => 'Sri Wahyuni, S.Pd', 'subject' => 'Bahasa Indonesia', 'phone' => '08123456002'],
                ['nip' => '198701032012011003', 'name' => 'Agus Setiawan, S.Pd', 'subject' => 'IPA', 'phone' => '08123456003'],
            ];

            foreach ($teachers as $data) {
                Teacher::create([
                    'school_id' => $school1->id,
                    'nip' => $data['nip'],
                    'name' => $data['name'],
                    'subject' => $data['subject'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($school1->npsn), // Default password = NPSN
                    'is_active' => true,
                ]);
            }
        }

        if ($school2) {
            $teachers = [
                ['nip' => '198801012013011001', 'name' => 'Bambang Hermanto, S.Pd', 'subject' => 'Matematika', 'phone' => '08123456101'],
                ['nip' => '198901022014011002', 'name' => 'Ratna Dewi, S.Pd', 'subject' => 'Bahasa Inggris', 'phone' => '08123456102'],
            ];

            foreach ($teachers as $data) {
                Teacher::create([
                    'school_id' => $school2->id,
                    'nip' => $data['nip'],
                    'name' => $data['name'],
                    'subject' => $data['subject'],
                    'phone' => $data['phone'],
                    'password' => Hash::make($school2->npsn),
                    'is_active' => true,
                ]);
            }
        }
    }
}

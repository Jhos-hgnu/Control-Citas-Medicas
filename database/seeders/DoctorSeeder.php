<?php

namespace Database\Seeders;

use App\Models\Doctor;
use Illuminate\Database\Seeder;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        Doctor::query()->insert([
            [
                'nombres' => 'Elena',
                'apellidos' => 'Vega',
                'especialidad' => 'Medicina familiar',
                'email' => 'elena.vega@example.test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Gabriel',
                'apellidos' => 'Luna',
                'especialidad' => 'Cardiologia',
                'email' => 'gabriel.luna@example.test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Ines',
                'apellidos' => 'Pardo',
                'especialidad' => 'Pediatria',
                'email' => 'ines.pardo@example.test',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

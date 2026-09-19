<?php

namespace Database\Seeders;

use App\Models\Paciente;
use Illuminate\Database\Seeder;

class PacienteSeeder extends Seeder
{
    public function run(): void
    {
        Paciente::query()->insert([
            [
                'nombres' => 'Ana',
                'apellidos' => 'Rios',
                'email' => 'ana.rios@example.test',
                'telefono' => '555-0101',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Bruno',
                'apellidos' => 'Soto',
                'email' => 'bruno.soto@example.test',
                'telefono' => '555-0102',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nombres' => 'Carla',
                'apellidos' => 'Mendez',
                'email' => 'carla.mendez@example.test',
                'telefono' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

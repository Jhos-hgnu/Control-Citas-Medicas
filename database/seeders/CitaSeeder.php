<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Database\Seeder;

class CitaSeeder extends Seeder
{
    public function run(): void
    {
        $pacientes = Paciente::query()->pluck('id', 'email');
        $doctores = Doctor::query()->pluck('id', 'email');
        $inicio = now()->addDays(1)->startOfDay()->setTime(9, 0);

        Cita::query()->insert([
            [
                'paciente_id' => $pacientes['ana.rios@example.test'],
                'doctor_id' => $doctores['elena.vega@example.test'],
                'inicio' => $inicio,
                'fin' => $inicio->copy()->addMinutes(30),
                'motivo' => 'Consulta general de seguimiento.',
                'estado' => 'pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'paciente_id' => $pacientes['bruno.soto@example.test'],
                'doctor_id' => $doctores['elena.vega@example.test'],
                'inicio' => $inicio->copy()->addHour(),
                'fin' => $inicio->copy()->addMinutes(90),
                'motivo' => 'Revision preventiva.',
                'estado' => 'confirmada',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'paciente_id' => $pacientes['carla.mendez@example.test'],
                'doctor_id' => $doctores['gabriel.luna@example.test'],
                'inicio' => $inicio->copy()->addHours(2),
                'fin' => $inicio->copy()->addMinutes(150),
                'motivo' => 'Control cardiovascular.',
                'estado' => 'pendiente',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}

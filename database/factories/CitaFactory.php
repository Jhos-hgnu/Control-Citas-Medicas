<?php

namespace Database\Factories;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Cita>
 */
class CitaFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $inicio = now()->addDay()->startOfHour();

        return ['paciente_id' => Paciente::factory(), 'doctor_id' => Doctor::factory(), 'inicio' => $inicio, 'fin' => $inicio->copy()->addMinutes(30), 'motivo' => fake()->sentence(), 'estado' => 'pendiente'];
    }
}

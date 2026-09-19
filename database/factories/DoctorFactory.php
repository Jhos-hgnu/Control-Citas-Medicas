<?php

namespace Database\Factories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Doctor>
 */
class DoctorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return ['nombres' => fake()->firstName(), 'apellidos' => fake()->lastName(), 'especialidad' => 'Medicina general', 'email' => fake()->unique()->safeEmail()];
    }
}

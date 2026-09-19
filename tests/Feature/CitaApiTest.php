<?php

namespace Tests\Feature;

use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CitaApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_citas_and_filters_by_doctor_patient_and_start_range(): void
    {
        $doctor = Doctor::factory()->create();
        $paciente = Paciente::factory()->create();
        $cita = Cita::factory()->for($doctor)->for($paciente)->create(['inicio' => '2026-10-10 09:00:00', 'fin' => '2026-10-10 09:30:00']);
        Cita::factory()->create(['inicio' => '2026-11-01 09:00:00']);

        $this->getJson("/api/citas?doctor_id={$doctor->id}&paciente_id={$paciente->id}&desde=2026-10-01&hasta=2026-10-31")
            ->assertOk()->assertJsonCount(1, 'data')->assertJsonPath('data.0.id', $cita->id);
    }

    public function test_creates_cita_with_pending_status(): void
    {
        $data = $this->citaData();
        $this->postJson('/api/citas', $data)->assertCreated()->assertJsonPath('data.estado', 'pendiente');
        $this->assertDatabaseHas('citas', ['motivo' => $data['motivo'], 'estado' => 'pendiente']);
    }

    public function test_rejects_invalid_create_data(): void
    {
        $this->postJson('/api/citas', ['paciente_id' => 999, 'doctor_id' => 999, 'inicio' => '2026-10-10 10:00', 'fin' => '2026-10-10 09:00'])
            ->assertUnprocessable()->assertJsonValidationErrors(['paciente_id', 'doctor_id', 'fin', 'motivo']);
    }

    public function test_shows_or_returns_not_found_for_cita(): void
    {
        $cita = Cita::factory()->create();
        $this->getJson("/api/citas/{$cita->id}")->assertOk()->assertJsonPath('data.id', $cita->id);
        $this->getJson('/api/citas/999999')->assertNotFound();
    }

    public function test_updates_cita_and_persists_changes(): void
    {
        $cita = Cita::factory()->create();
        $data = $this->citaData(['motivo' => 'Reprogramada']);
        $this->putJson("/api/citas/{$cita->id}", $data)->assertOk()->assertJsonPath('data.motivo', 'Reprogramada');
        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'motivo' => 'Reprogramada']);
    }

    public function test_rejects_invalid_update_and_updates_valid_status(): void
    {
        $cita = Cita::factory()->create();
        $this->putJson("/api/citas/{$cita->id}", [])->assertUnprocessable();
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'confirmada'])->assertOk()->assertJsonPath('data.estado', 'confirmada');
        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'estado' => 'confirmada']);
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'otro'])->assertUnprocessable();
    }

    public function test_lists_doctores_and_pacientes(): void
    {
        Doctor::factory()->create();
        Paciente::factory()->create();
        $this->getJson('/api/doctores')->assertOk()->assertJsonCount(1, 'data');
        $this->getJson('/api/pacientes')->assertOk()->assertJsonCount(1, 'data');
    }

    private function citaData(array $overrides = []): array
    {
        return [...['paciente_id' => Paciente::factory()->create()->id, 'doctor_id' => Doctor::factory()->create()->id, 'inicio' => '2026-10-10T09:00:00', 'fin' => '2026-10-10T09:30:00', 'motivo' => 'Consulta general'], ...$overrides];
    }
}

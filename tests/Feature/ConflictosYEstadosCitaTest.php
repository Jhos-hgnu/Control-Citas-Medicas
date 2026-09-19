<?php

namespace Tests\Feature;

use App\Enums\EstadoCita;
use App\Models\Cita;
use App\Models\Doctor;
use App\Models\Paciente;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConflictosYEstadosCitaTest extends TestCase
{
    use RefreshDatabase;

    public function test_rejects_all_overlap_forms_and_does_not_create_a_cita(): void
    {
        $doctor = Doctor::factory()->create();
        Cita::factory()->for($doctor)->create(['inicio' => '2026-10-10 09:00', 'fin' => '2026-10-10 10:00']);

        foreach ([['09:30', '10:30'], ['08:30', '09:30'], ['09:15', '09:45'], ['08:00', '11:00']] as [$inicio, $fin]) {
            $this->postJson('/api/citas', $this->data($doctor, $inicio, $fin))->assertConflict()->assertJsonPath('message', 'El doctor ya tiene una cita activa en el horario solicitado.');
        }

        $this->assertDatabaseCount('citas', 1);
    }

    public function test_allows_consecutive_and_different_doctor_appointments(): void
    {
        $doctor = Doctor::factory()->create();
        Cita::factory()->for($doctor)->create(['inicio' => '2026-10-10 09:00', 'fin' => '2026-10-10 09:30']);
        $this->postJson('/api/citas', $this->data($doctor, '09:30', '10:00'))->assertCreated();
        $this->postJson('/api/citas', $this->data(Doctor::factory()->create(), '09:00', '09:30'))->assertCreated();
    }

    public function test_cancelled_and_attended_citas_do_not_block_availability(): void
    {
        $doctor = Doctor::factory()->create();
        foreach ([[EstadoCita::Cancelada, '09:00', '10:00'], [EstadoCita::Atendida, '11:00', '12:00']] as [$estado, $inicio, $fin]) {
            Cita::factory()->for($doctor)->create(['inicio' => "2026-10-10 {$inicio}", 'fin' => "2026-10-10 {$fin}", 'estado' => $estado]);
            $this->postJson('/api/citas', $this->data($doctor, $inicio, $fin))->assertCreated();
        }
    }

    public function test_update_excludes_itself_but_rejects_conflict_and_preserves_original_data(): void
    {
        $doctor = Doctor::factory()->create();
        $cita = Cita::factory()->for($doctor)->create(['inicio' => '2026-10-10 09:00', 'fin' => '2026-10-10 10:00']);
        $this->putJson("/api/citas/{$cita->id}", $this->data($doctor, '09:00', '10:00'))->assertOk();
        Cita::factory()->for($doctor)->create(['inicio' => '2026-10-10 11:00', 'fin' => '2026-10-10 12:00']);
        $this->putJson("/api/citas/{$cita->id}", $this->data($doctor, '11:30', '12:30'))->assertConflict();
        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'inicio' => '2026-10-10 09:00:00']);
    }

    public function test_update_checks_the_new_doctor_availability(): void
    {
        $cita = Cita::factory()->create();
        $newDoctor = Doctor::factory()->create();
        Cita::factory()->for($newDoctor)->create(['inicio' => '2026-10-10 09:00', 'fin' => '2026-10-10 10:00']);
        $this->putJson("/api/citas/{$cita->id}", $this->data($newDoctor, '09:30', '10:30'))->assertConflict();
    }

    public function test_enforces_state_transitions_and_same_state_is_idempotent(): void
    {
        $cita = Cita::factory()->create();
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'pendiente'])->assertOk();
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'confirmada'])->assertOk();
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'atendida'])->assertOk();
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'pendiente'])->assertConflict();
    }

    public function test_cancelled_cita_is_preserved_available_and_cannot_be_reactivated_or_reprogrammed(): void
    {
        $doctor = Doctor::factory()->create();
        $cita = Cita::factory()->for($doctor)->create(['inicio' => '2026-10-10 09:00', 'fin' => '2026-10-10 10:00']);
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'cancelada'])->assertOk();
        $this->getJson("/api/citas/{$cita->id}")->assertOk()->assertJsonPath('data.estado', 'cancelada');
        $this->postJson('/api/citas', $this->data($doctor, '09:00', '10:00'))->assertCreated();
        $this->patchJson("/api/citas/{$cita->id}/estado", ['estado' => 'confirmada'])->assertConflict();
        $this->putJson("/api/citas/{$cita->id}", $this->data($doctor, '11:00', '12:00'))->assertConflict();
        $this->assertDatabaseHas('citas', ['id' => $cita->id, 'estado' => 'cancelada']);
    }

    private function data(Doctor $doctor, string $inicio, string $fin): array
    {
        return ['paciente_id' => Paciente::factory()->create()->id, 'doctor_id' => $doctor->id, 'inicio' => "2026-10-10T{$inicio}:00", 'fin' => "2026-10-10T{$fin}:00", 'motivo' => 'Consulta de prueba'];
    }
}

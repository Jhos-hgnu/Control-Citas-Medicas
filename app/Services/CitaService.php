<?php

namespace App\Services;

use App\Enums\EstadoCita;
use App\Exceptions\ConflictoCitaException;
use App\Models\Cita;
use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class CitaService
{
    public function __construct(private readonly DisponibilidadCitaService $disponibilidad) {}

    public function list(array $filters): Collection
    {
        return Cita::query()->with(['paciente', 'doctor'])->when($filters['doctor_id'] ?? null, fn ($query, $id) => $query->where('doctor_id', $id))->when($filters['paciente_id'] ?? null, fn ($query, $id) => $query->where('paciente_id', $id))->when($filters['desde'] ?? null, fn ($query, $date) => $query->where('inicio', '>=', $date))->when($filters['hasta'] ?? null, fn ($query, $date) => $query->where('inicio', '<=', $date))->orderBy('inicio')->get();
    }

    public function detail(Cita $cita): Cita
    {
        return $cita->load(['paciente', 'doctor']);
    }

    public function create(array $data): Cita
    {
        return DB::transaction(function () use ($data) {
            $this->bloquearDoctorYValidarDisponibilidad($data['doctor_id'], $data['inicio'], $data['fin']);

            return $this->detail(Cita::query()->create([...$data, 'estado' => EstadoCita::Pendiente]));
        });
    }

    public function update(Cita $cita, array $data): Cita
    {
        if ($cita->estado->esTerminal()) {
            throw new ConflictoCitaException('No se puede reprogramar una cita cancelada o atendida.');
        }

        return DB::transaction(function () use ($cita, $data) {
            $this->bloquearDoctorYValidarDisponibilidad($data['doctor_id'], $data['inicio'], $data['fin'], $cita->id);
            $cita->update($data);

            return $this->detail($cita);
        });
    }

    public function updateEstado(Cita $cita, string $estado): Cita
    {
        $destino = EstadoCita::from($estado);

        if (! $cita->estado->permite($destino)) {
            throw new ConflictoCitaException('La transicion de estado solicitada no esta permitida.');
        }

        $cita->update(['estado' => $destino]);

        return $this->detail($cita);
    }

    private function bloquearDoctorYValidarDisponibilidad(int $doctorId, string $inicio, string $fin, ?int $citaExcluirId = null): void
    {
        Doctor::query()->lockForUpdate()->findOrFail($doctorId);

        if ($this->disponibilidad->tieneConflicto($doctorId, $inicio, $fin, $citaExcluirId)) {
            throw new ConflictoCitaException('El doctor ya tiene una cita activa en el horario solicitado.');
        }
    }
}

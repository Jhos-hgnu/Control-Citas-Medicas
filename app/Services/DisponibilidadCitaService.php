<?php

namespace App\Services;

use App\Enums\EstadoCita;
use App\Models\Cita;
use Carbon\CarbonInterface;

class DisponibilidadCitaService
{
    public function tieneConflicto(int $doctorId, CarbonInterface|string $inicio, CarbonInterface|string $fin, ?int $citaExcluirId = null): bool
    {
        return Cita::query()
            ->where('doctor_id', $doctorId)
            ->whereIn('estado', EstadoCita::activos())
            ->when($citaExcluirId, fn ($query, $id) => $query->whereKeyNot($id))
            ->where('inicio', '<', $fin)
            ->where('fin', '>', $inicio)
            ->exists();
    }
}

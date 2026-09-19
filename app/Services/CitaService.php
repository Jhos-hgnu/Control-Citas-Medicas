<?php

namespace App\Services;

use App\Models\Cita;
use Illuminate\Database\Eloquent\Collection;

class CitaService
{
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
        return $this->detail(Cita::query()->create([...$data, 'estado' => 'pendiente']));
    }

    public function update(Cita $cita, array $data): Cita
    {
        $cita->update($data);

        return $this->detail($cita);
    }

    public function updateEstado(Cita $cita, string $estado): Cita
    {
        $cita->update(['estado' => $estado]);

        return $this->detail($cita);
    }
}

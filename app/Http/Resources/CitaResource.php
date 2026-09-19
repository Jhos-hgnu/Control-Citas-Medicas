<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CitaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'inicio' => $this->inicio?->toIso8601String(), 'fin' => $this->fin?->toIso8601String(), 'motivo' => $this->motivo, 'estado' => $this->estado, 'paciente' => new PacienteResource($this->whenLoaded('paciente')), 'doctor' => new DoctorResource($this->whenLoaded('doctor')), 'created_at' => $this->created_at?->toIso8601String(), 'updated_at' => $this->updated_at?->toIso8601String()];
    }
}

<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DoctorResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return ['id' => $this->id, 'nombres' => $this->nombres, 'apellidos' => $this->apellidos, 'especialidad' => $this->especialidad, 'email' => $this->email];
    }
}

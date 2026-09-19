<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PacienteResource;
use App\Models\Paciente;

class PacienteController extends Controller
{
    public function index()
    {
        return PacienteResource::collection(Paciente::query()->orderBy('apellidos')->orderBy('nombres')->get());
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ListCitasRequest;
use App\Http\Requests\StoreCitaRequest;
use App\Http\Requests\UpdateCitaEstadoRequest;
use App\Http\Requests\UpdateCitaRequest;
use App\Http\Resources\CitaResource;
use App\Models\Cita;
use App\Services\CitaService;

class CitaController extends Controller
{
    public function __construct(private readonly CitaService $citas) {}

    public function index(ListCitasRequest $request)
    {
        return CitaResource::collection($this->citas->list($request->validated()));
    }

    public function store(StoreCitaRequest $request)
    {
        return (new CitaResource($this->citas->create($request->validated())))->response()->setStatusCode(201);
    }

    public function show(Cita $cita)
    {
        return new CitaResource($this->citas->detail($cita));
    }

    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        return new CitaResource($this->citas->update($cita, $request->validated()));
    }

    public function updateEstado(UpdateCitaEstadoRequest $request, Cita $cita)
    {
        return new CitaResource($this->citas->updateEstado($cita, $request->validated('estado')));
    }
}

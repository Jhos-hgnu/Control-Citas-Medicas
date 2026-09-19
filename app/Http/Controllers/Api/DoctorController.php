<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DoctorResource;
use App\Models\Doctor;

class DoctorController extends Controller
{
    public function index()
    {
        return DoctorResource::collection(Doctor::query()->orderBy('apellidos')->orderBy('nombres')->get());
    }
}

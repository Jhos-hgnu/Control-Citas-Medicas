@extends('layouts.app')

@section('title', 'Control de Citas Medicas')

@section('content')
    <section class="hero" aria-labelledby="page-title">
        <p class="eyebrow">Administracion clinica</p>
        <h1 id="page-title">Control de Citas Medicas</h1>
        <p class="hero-description">Sistema de gestion y programacion de citas.</p>
    </section>

    <section class="status-card" aria-labelledby="status-title">
        <div class="status-indicator" aria-hidden="true"></div>
        <div>
            <p class="status-label">Estado</p>
            <h2 id="status-title">Entorno base configurado</h2>
            <p>Laravel, Blade y Vite estan listos para las siguientes fases del proyecto.</p>
        </div>
    </section>
@endsection

@extends('layouts.app')

@section('title', 'Calendario de Citas')

@section('content')
    <section class="calendar-page" aria-labelledby="calendar-title">
        <div class="calendar-heading">
            <div>
                <p class="eyebrow">Programacion clinica</p>
                <h1 id="calendar-title">Calendario de citas</h1>
                <p class="hero-description">Consulta, programa y actualiza citas con datos sincronizados desde el sistema.</p>
            </div>
            <button class="button-primary" id="new-appointment" type="button">Nueva cita</button>
        </div>

        <div class="calendar-toolbar">
            <label for="doctor-filter">Filtrar por doctor</label>
            <select id="doctor-filter"><option value="">Todos los doctores</option></select>
            <div class="status-legend" aria-label="Estados de cita">
                <span><i class="status-dot pending"></i>Pendiente</span><span><i class="status-dot confirmed"></i>Confirmada</span><span><i class="status-dot cancelled"></i>Cancelada</span><span><i class="status-dot attended"></i>Atendida</span>
            </div>
        </div>
        <p id="calendar-message" class="calendar-message" role="status" aria-live="polite"></p>
        <div id="calendar" aria-label="Calendario de citas"></div>
    </section>

    <dialog id="appointment-dialog" class="appointment-dialog">
        <form id="appointment-form" method="dialog">
            <div class="dialog-heading"><h2>Nueva cita</h2><button class="dialog-close" value="cancel" aria-label="Cerrar">Cerrar</button></div>
            <div id="form-errors" class="form-errors" role="alert"></div>
            <label>Paciente<select id="patient-id" required></select></label>
            <label>Doctor<select id="form-doctor-id" required></select></label>
            <label>Inicio<input id="start-at" type="datetime-local" required></label>
            <label>Fin<input id="end-at" type="datetime-local" required></label>
            <label>Motivo<textarea id="reason" rows="3" maxlength="1000" required></textarea></label>
            <div class="dialog-actions"><button value="cancel" type="button" class="button-secondary" data-close>Cancelar</button><button class="button-primary" id="save-appointment" type="submit">Guardar cita</button></div>
        </form>
    </dialog>

    <dialog id="detail-dialog" class="appointment-dialog">
        <section>
            <div class="dialog-heading"><h2>Detalle de cita</h2><button class="dialog-close" type="button" data-close aria-label="Cerrar">Cerrar</button></div>
            <div id="appointment-detail"></div>
            <div id="status-actions" class="dialog-actions"></div>
        </section>
    </dialog>
@endsection

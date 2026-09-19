import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import timeGridPlugin from '@fullcalendar/timegrid';
import interactionPlugin from '@fullcalendar/interaction';

const calendarElement = document.querySelector('#calendar');

if (calendarElement) {
    const dialog = document.querySelector('#appointment-dialog');
    const detailDialog = document.querySelector('#detail-dialog');
    const filter = document.querySelector('#doctor-filter');
    const message = document.querySelector('#calendar-message');
    const errors = document.querySelector('#form-errors');
    const form = document.querySelector('#appointment-form');
    const statusColors = { pendiente: 'pending', confirmada: 'confirmed', cancelada: 'cancelled', atendida: 'attended' };
    const api = async (url, options = {}) => {
        try {
            const response = await fetch(url, { headers: { Accept: 'application/json', ...(options.body ? { 'Content-Type': 'application/json' } : {}) }, ...options });
            const data = await response.json();
            if (!response.ok) throw { status: response.status, data };
            return data;
        } catch (error) {
            if (error.status) throw error;
            throw { status: 0, data: { message: 'No fue posible conectar con el servidor.' } };
        }
    };
    const notify = (text, type = '') => { message.textContent = text; message.className = `calendar-message ${type}`; };
    const localInput = (date) => new Date(date.getTime() - date.getTimezoneOffset() * 60000).toISOString().slice(0, 16);
    const showError = (error) => {
        const list = error.data?.errors ? Object.values(error.data.errors).flat() : [error.data?.message || 'Ocurrio un error inesperado.'];
        errors.innerHTML = list.map((item) => `<p>${item}</p>`).join('');
        notify(list[0], 'error');
    };
    const calendar = new Calendar(calendarElement, {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin], initialView: 'dayGridMonth', timeZone: 'local', firstDay: 1,
        headerToolbar: { left: 'prev,next today', center: 'title', right: 'dayGridMonth,timeGridWeek' }, selectable: true, editable: true, eventResizableFromStart: false,
        events: async (info, success, failure) => {
            const params = new URLSearchParams({ desde: info.startStr, hasta: info.endStr });
            if (filter.value) params.set('doctor_id', filter.value);
            try {
                const response = await api(`/api/citas?${params}`);
                success(response.data.map((cita) => ({ id: cita.id, title: `${cita.paciente.nombres} ${cita.paciente.apellidos} · ${cita.motivo}`, start: cita.inicio, end: cita.fin, classNames: [`event-${statusColors[cita.estado]}`], editable: !['cancelada', 'atendida'].includes(cita.estado), extendedProps: cita })));
            } catch (error) { failure(error); notify(error.data.message, 'error'); }
        },
        select: (selection) => openForm(selection.start, selection.end),
        eventClick: async ({ event }) => openDetail(event.id),
        eventDrop: (info) => saveMove(info), eventResize: (info) => saveMove(info),
    });
    const loadOptions = async () => {
        const [doctores, pacientes] = await Promise.all([api('/api/doctores'), api('/api/pacientes')]);
        const doctorOptions = doctores.data.map((d) => `<option value="${d.id}">${d.apellidos}, ${d.nombres} - ${d.especialidad}</option>`).join('');
        filter.insertAdjacentHTML('beforeend', doctorOptions);
        document.querySelector('#form-doctor-id').innerHTML = doctorOptions;
        document.querySelector('#patient-id').innerHTML = pacientes.data.map((p) => `<option value="${p.id}">${p.apellidos}, ${p.nombres}</option>`).join('');
    };
    const openForm = (start = new Date(), end = new Date(start.getTime() + 30 * 60000)) => { errors.innerHTML = ''; form.reset(); document.querySelector('#start-at').value = localInput(start); document.querySelector('#end-at').value = localInput(end); dialog.showModal(); };
    const openDetail = async (id) => {
        try {
            const cita = (await api(`/api/citas/${id}`)).data;
            document.querySelector('#appointment-detail').innerHTML = `<dl><dt>Paciente</dt><dd>${cita.paciente.nombres} ${cita.paciente.apellidos}</dd><dt>Doctor</dt><dd>${cita.doctor.nombres} ${cita.doctor.apellidos} · ${cita.doctor.especialidad}</dd><dt>Inicio</dt><dd>${new Date(cita.inicio).toLocaleString()}</dd><dt>Fin</dt><dd>${new Date(cita.fin).toLocaleString()}</dd><dt>Motivo</dt><dd>${cita.motivo}</dd><dt>Estado</dt><dd>${cita.estado}</dd></dl>`;
            const transitions = { pendiente: ['confirmada', 'cancelada'], confirmada: ['atendida', 'cancelada'] };
            document.querySelector('#status-actions').innerHTML = (transitions[cita.estado] || []).map((estado) => `<button class="button-secondary" data-state="${estado}">${estado}</button>`).join('');
            document.querySelectorAll('[data-state]').forEach((button) => button.addEventListener('click', () => updateState(cita.id, button.dataset.state)));
            detailDialog.showModal();
        } catch (error) { notify(error.data.message, 'error'); }
    };
    const updateState = async (id, estado) => { try { await api(`/api/citas/${id}/estado`, { method: 'PATCH', body: JSON.stringify({ estado }) }); detailDialog.close(); calendar.refetchEvents(); notify('Estado actualizado.', 'success'); } catch (error) { notify(error.data.message, 'error'); } };
    const saveMove = async (info) => { const cita = info.event.extendedProps; try { await api(`/api/citas/${info.event.id}`, { method: 'PUT', body: JSON.stringify({ paciente_id: cita.paciente.id, doctor_id: cita.doctor.id, inicio: info.event.start.toISOString(), fin: info.event.end.toISOString(), motivo: cita.motivo }) }); calendar.refetchEvents(); notify('Cita reprogramada.', 'success'); } catch (error) { info.revert(); notify(error.data.message, 'error'); } };
    form.addEventListener('submit', async (event) => { event.preventDefault(); errors.innerHTML = ''; const save = document.querySelector('#save-appointment'); save.disabled = true; try { await api('/api/citas', { method: 'POST', body: JSON.stringify({ paciente_id: Number(document.querySelector('#patient-id').value), doctor_id: Number(document.querySelector('#form-doctor-id').value), inicio: document.querySelector('#start-at').value, fin: document.querySelector('#end-at').value, motivo: document.querySelector('#reason').value }) }); dialog.close(); calendar.refetchEvents(); notify('Cita creada.', 'success'); } catch (error) { showError(error); } finally { save.disabled = false; } });
    document.querySelector('#new-appointment').addEventListener('click', () => openForm()); filter.addEventListener('change', () => calendar.refetchEvents()); document.querySelectorAll('[data-close]').forEach((button) => button.addEventListener('click', () => button.closest('dialog').close()));
    loadOptions().then(() => calendar.render()).catch(showError);
}

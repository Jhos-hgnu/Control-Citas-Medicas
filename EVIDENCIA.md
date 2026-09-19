# Evidencias del Proyecto

## Evidencias visuales

Las capturas de pantalla y demostraciones del proyecto estan disponibles en [Google Drive - Evidencias del Sistema de Control de Citas Medicas](https://drive.google.com/drive/folders/1snO23LupPrZ7MGMfv7og9GHg3U6DLX5o?usp=drive_link).

# Feature 1 - Docker + MySQL + Schema

## Rama

`feature/docker-mysql-schema`

## Requisitos cubiertos

- RQNF-01: MySQL se ejecuta dentro de Docker con el volumen persistente `control-citas-medicas_mysql_data`.
- RQNF-02: El entorno se inicia con `docker compose up -d`.

Esta feature solo proporciona infraestructura y persistencia base. No implementa RQF-01, RQF-02, RQF-03, RQF-04, RQF-05, RQF-06, RQF-07, RQF-08, RQF-09 ni RQF-10.

## Archivos principales

- `docker-compose.yml`
- `docker/mysql/init/01-configure-app-user.sh`
- `database/migrations/2026_09_19_135142_create_pacientes_table.php`
- `database/migrations/2026_09_19_135143_create_doctores_table.php`
- `database/migrations/2026_09_19_135144_create_citas_table.php`
- `app/Models/Paciente.php`, `app/Models/Doctor.php`, `app/Models/Cita.php`
- `database/seeders/PacienteSeeder.php`, `database/seeders/DoctorSeeder.php`, `database/seeders/CitaSeeder.php`

## Contenedor y volumen

Comando ejecutado:

```bash
docker compose up -d
```

Resultado relevante de `docker compose ps`:

```text
control-citas-medicas-mysql-1  mysql:8.0  Up (healthy)  0.0.0.0:3307->3306/tcp
```

El volumen creado y comprobado con `docker volume ls` es:

```text
control-citas-medicas_mysql_data
```

## Migraciones y seeding

Comando ejecutado contra el MySQL Docker local:

```bash
php artisan migrate:fresh --seed
```

`php artisan migrate:status` confirma como ejecutadas las migraciones de `pacientes`, `doctores` y `citas`, junto con las migrations base de Laravel.

Los seeders insertan datos ficticios controlados:

- 3 pacientes
- 3 doctores
- 3 citas sin solapamientos obvios para un mismo doctor

Consulta Laravel ejecutada:

```text
pacientes => 3
doctores => 3
citas => 3
```

## Prueba de persistencia

Se ejecutaron, en este orden:

```bash
docker compose down
docker compose up -d
```

No se utilizo `-v`. Despues del reinicio, Laravel continuo leyendo 3 pacientes, 3 doctores y 3 citas desde MySQL, lo que confirma que el volumen conserva los datos.

## Commits

- `70619f6` chore(docker): configure persistent MySQL service [RQNF-01, RQNF-02]
- `f938d23` fix(docker): configure compatible MySQL authentication [RQNF-01]
- `39b6d1a` feat(database): add medical scheduling schema [RQNF-01]
- `ab35cef` fix(database): correct doctors migration rollback [RQNF-01]
- `c2661cf` feat(database): add initial scheduling seed data [RQNF-01]

## Capturas pendientes de adjuntar

- Docker Desktop con el contenedor MySQL en estado healthy.
- Salida de `docker compose ps`.
- Migraciones ejecutadas.
- Datos semilla presentes.

# Feature 2 - API REST de citas

## Rama y alcance

Rama: `feature/api-rest-citas`. Cubre RQF-01, RQF-05, RQF-06, RQF-07, RQF-08, RQNF-03 y RQNF-04 mediante controllers API, Form Requests, `CitaService`, Resources y MySQL Docker.

Endpoints: `GET/POST /api/citas`, `GET/PUT /api/citas/{cita}`, `PATCH /api/citas/{cita}/estado`, `GET /api/doctores` y `GET /api/pacientes`.

## Evidencia real

Con Laravel servido localmente y MySQL Docker activo, se consultaron los catalogos, se creo la cita `id: 4`, se reprogramo y se cambio a `confirmada`. El GET posterior devolvio:

```json
{"id":4,"inicio":"2026-10-15T15:00:00+00:00","fin":"2026-10-15T15:30:00+00:00","motivo":"Consulta API reprogramada","estado":"confirmada"}
```

La persistencia se comprobo mediante GET posterior a POST, PUT y PATCH. Las pruebas automatizadas usan la base aislada `citas_medicas_testing` en el mismo MySQL Docker; no usan SQLite.

## Capturas pendientes de adjuntar

- Requests y respuestas de la API.
- Suite de pruebas API.

# Feature 3 - Validación de conflictos y estados

## Rama y reglas

Rama: `feature/validacion-conflictos-estados`. Cubre RQF-03, RQF-05, RQNF-03, RQNF-04 y RQNF-07. Una cita activa es `pendiente` o `confirmada`; el solapamiento usa `inicio < fin_existente AND fin > inicio_existente`. Las citas canceladas y atendidas se conservan, pero no bloquean disponibilidad.

`CitaService` abre una transaccion, bloquea con `lockForUpdate()` la fila del doctor y consulta `DisponibilidadCitaService`. El bloqueo se libera al cerrar la transaccion y serializa operaciones concurrentes sobre el mismo doctor.

## Estados

`pendiente` permite `confirmada` y `cancelada`; `confirmada` permite `atendida` y `cancelada`; `cancelada` y `atendida` son terminales. Repetir el mismo estado es idempotente. Transiciones o reprogramaciones terminales devuelven `409`.

## Evidencia real

Con MySQL Docker y Laravel local, se creo una cita valida, se intento un POST solapado y se obtuvo `POST status: 409`. Tras cancelarla se creo una nueva cita en el mismo horario. Intentar `cancelada -> confirmada` devolvio `PATCH status: 409`.

La suite contra `citas_medicas_testing` paso con conflictos, estados y API existentes; no utiliza SQLite.

# Feature 4 - FullCalendar UI

## Rama e integracion

Rama: `feature/fullcalendar-ui`. La ruta `/citas` usa `@fullcalendar/core`, `@fullcalendar/daygrid`, `@fullcalendar/timegrid` y `@fullcalendar/interaction` instalados mediante npm.

FullCalendar solicita `GET /api/citas` usando el rango visible `desde` y `hasta`; el filtro carga doctores desde `GET /api/doctores`. Pacientes y doctores del formulario provienen de los endpoints de catalogo. POST, PUT y PATCH se envian a la API real y refrescan eventos desde el servidor.

## Comportamiento

- Vistas: mes y semana, con navegacion y hoy.
- Creacion: seleccion de rango o boton Nueva cita, POST real y refresco.
- Detalle: `GET /api/citas/{id}` al hacer clic.
- Estados: PATCH real, acciones visibles segun estado y colores centralizados.
- Reprogramacion: drag/drop y resize usan PUT. Ante 409, 422 o red se ejecuta `revert()`.
- Terminales: canceladas y atendidas no son arrastrables; el backend conserva la proteccion definitiva.
- Timezone: FullCalendar usa `local` y consume las fechas ISO 8601 con offset sin eliminarlo manualmente.

## Validaciones ejecutadas

`php artisan test` paso con 18 pruebas y 62 aserciones. `npm run build` compiló el bundle FullCalendar. La pagina `/citas` se cubre mediante prueba Laravel de respuesta y contenedor.

## Capturas requeridas

1. Vista mensual y semanal.
2. Formulario Nueva cita y detalle.
3. Filtro de doctor y leyenda de estados.
4. Drag/drop exitoso y conflicto 409 revertido.
5. Vista tablet y Docker healthy.

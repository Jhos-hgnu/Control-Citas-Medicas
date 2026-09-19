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

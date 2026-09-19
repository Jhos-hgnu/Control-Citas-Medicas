# API REST

Todas las rutas devuelven JSON. Las fechas se serializan en ISO 8601 y la aplicacion usa la zona horaria UTC configurada en Laravel. Los filtros `desde` y `hasta` aplican sobre `inicio`, inclusivamente.

## Citas

| Metodo | Ruta | Proposito | Respuestas |
| --- | --- | --- | --- |
| GET | `/api/citas` | Lista citas ordenadas por inicio. | 200, 422 |
| POST | `/api/citas` | Crea una cita con estado `pendiente`. | 201, 409, 422 |
| GET | `/api/citas/{cita}` | Obtiene el detalle. | 200, 404 |
| PUT | `/api/citas/{cita}` | Actualiza una cita. | 200, 404, 409, 422 |
| PATCH | `/api/citas/{cita}/estado` | Cambia el estado basico. | 200, 404, 409, 422 |

`GET /api/citas` acepta `doctor_id`, `paciente_id`, `desde` y `hasta`. Si ambos limites se envian, `hasta` no puede ser anterior a `desde`.

Body de creacion o actualizacion:

```json
{"paciente_id":1,"doctor_id":1,"inicio":"2026-10-15T09:00:00","fin":"2026-10-15T09:30:00","motivo":"Consulta general"}
```

Body de estado:

```json
{"estado":"confirmada"}
```

La respuesta de cita incluye `id`, `inicio`, `fin`, `motivo`, `estado`, `paciente`, `doctor` y timestamps.

### Conflicto de horario

POST y PUT devuelven `409` cuando existe una cita activa del mismo doctor cuyo intervalo cumple `nuevo_inicio < cita_existente.fin AND nuevo_fin > cita_existente.inicio`. Los estados activos son `pendiente` y `confirmada`; `cancelada` y `atendida` no bloquean disponibilidad. Las citas consecutivas estan permitidas. PUT excluye la propia cita y no permite reprogramar estados terminales.

### Transiciones de estado

| Estado actual | Estados permitidos |
| --- | --- |
| pendiente | confirmada, cancelada |
| confirmada | atendida, cancelada |
| cancelada | ninguno |
| atendida | ninguno |

Enviar el mismo estado es idempotente y devuelve `200`. Una transicion no permitida devuelve `409`.

## Catalogos

| Metodo | Ruta | Proposito | Respuesta |
| --- | --- | --- | --- |
| GET | `/api/doctores` | Lista doctores por apellidos y nombres. | 200 |
| GET | `/api/pacientes` | Lista pacientes por apellidos y nombres. | 200 |

Los catalogos incluyen campos aptos para filtros y formularios futuros.

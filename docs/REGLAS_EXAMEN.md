# Reglas del Examen

El desarrollo del modulo final debe respetar estas restricciones:

1. Debe existir al menos un merge real hacia `main`, visible con `git log --graph --all`.
2. MySQL debe ejecutarse dentro de Docker.
3. No se acepta SQLite, almacenamiento en memoria ni una base de datos externa como sustituto.
4. FullCalendar debe consumir datos reales desde la API de Laravel.
5. JavaScript no debe contener datos de citas simulados ni hardcodeados.
6. El flujo funcional debe ser: `FullCalendar -> Laravel REST API -> logica de negocio -> Eloquent -> MySQL Docker`.
7. La validacion de conflictos de horario debe ejecutarse en el servidor.
8. Los cambios de estado deben persistirse en MySQL.
9. Cada feature debe tener commits descriptivos, Pull Request y merge.
10. La evidencia debe almacenarse en el repositorio.

## Flujo Git previsto

```text
main
 |
 +-- feature/docker-mysql-schema
 |       PR -> main
 |       merge
 |
 +-- feature/api-rest-citas
 |       PR -> main
 |       merge
 |
 +-- feature/validacion-conflictos-estados
 |       PR -> main
 |       merge
 |
 +-- feature/fullcalendar-ui
         PR -> main
         merge
```

Cada feature se creara desde el `main` actualizado despues del merge de la anterior. Los commits deben indicar los requisitos aplicables, por ejemplo: `feat(citas): create appointment REST endpoint [RQF-01, RQF-07]`.

Esta fase no crea esas ramas ni implementa Docker, el esquema MySQL, endpoints funcionales de citas, conflictos, estados o FullCalendar.

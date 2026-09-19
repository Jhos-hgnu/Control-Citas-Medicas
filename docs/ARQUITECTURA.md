# Arquitectura

## Objetivo

La aplicacion se desarrollara con una separacion por capas que mantenga la presentacion, el caso de uso y la persistencia con responsabilidades concretas. La estructura estandar de Laravel se conserva y solo se ampliara cuando una feature aporte comportamiento real.

## Capas y dependencias

| Capa | Responsabilidad | Dependencias permitidas |
| --- | --- | --- |
| Presentacion | Blade, CSS y JavaScript. Muestra informacion y solicita recursos HTTP. | Rutas web y assets de Vite. |
| API / Controllers | Recibe solicitudes, coordina el caso de uso y devuelve respuestas HTTP o JSON. | Form Requests y servicios de aplicacion. |
| Form Requests | Autoriza y valida entradas HTTP significativas. | Reglas de validacion de Laravel. |
| Application / Services | Ejecuta reglas de negocio y coordina operaciones del dominio. | Modelos Eloquent y transacciones cuando correspondan. |
| Acceso a datos / Eloquent | Modelos, relaciones, casts y consultas orientadas al dominio. | MySQL a traves de la configuracion de Laravel. |
| MySQL Docker | Persistencia relacional de la aplicacion. | Migrations, restricciones e indices de Laravel. |

Las dependencias fluyen hacia abajo. La interfaz no accede directamente a la base de datos y los controladores no contienen reglas de negocio complejas ni SQL directo.

## Flujo futuro de una peticion

1. FullCalendar solicita o envia datos JSON a Laravel mediante HTTP.
2. La ruta API dirige la solicitud a un controller delgado.
3. El controller entrega la validacion a un Form Request y delega el trabajo a un servicio.
4. El servicio ejecuta reglas de negocio en servidor, incluida la futura comprobacion de conflictos de horario.
5. Eloquent persiste o consulta datos en MySQL ejecutado en Docker.
6. El controller devuelve una respuesta JSON coherente con el codigo HTTP apropiado.

Flujo esperado: `FullCalendar -> API -> Service -> Eloquent -> MySQL`.

## Reglas de implementacion

- Los controllers reciben la peticion, delegan y responden; deben permanecer delgados.
- Las validaciones significativas de endpoints se implementaran con Form Requests.
- Las reglas de negocio, incluida la validacion de doble reserva, se ejecutaran en el servidor y no solo en JavaScript.
- Los modelos contienen relaciones, casts, scopes utiles y comportamiento pequeno propio del modelo.
- Se usara Eloquent sin introducir un Repository generico. Un repositorio solo se agregara si resuelve una separacion real.
- Las fechas se manejaran con Carbon y casts de Laravel.
- Las operaciones futuras que modifiquen varios datos relacionados evaluaran `DB::transaction()` solo cuando sea necesario.
- La persistencia funcional se implementara en una fase posterior con MySQL dentro de Docker. SQLite no es una alternativa para el modulo final.

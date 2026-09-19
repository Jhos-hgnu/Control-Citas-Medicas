# Control de Citas Medicas

Base profesional en Laravel 12 para el futuro Sistema de Control de Citas Medicas. Esta fase solamente prepara la aplicacion, su identidad visual, documentacion y una verificacion tecnica de API.

## Tecnologias

- Laravel 12
- Blade
- JavaScript
- Vite
- MySQL mediante Docker (fase posterior)
- FullCalendar (fase posterior)

## Requisitos

- PHP 8.2 o superior
- Composer 2
- Node.js y npm

MySQL no se instala ni se ejecuta localmente. La persistencia funcional se configurara exclusivamente mediante Docker en la feature `feature/docker-mysql-schema`.

## Instalacion

1. Instalar dependencias PHP:

   ```bash
   composer install
   ```

2. Instalar dependencias frontend:

   ```bash
   npm install
   ```

3. Crear la configuracion local a partir de `.env.example` y completar los valores necesarios cuando la infraestructura MySQL Docker este disponible.

4. Generar la clave de aplicacion:

   ```bash
   php artisan key:generate
   ```

5. Ejecutar Laravel:

   ```bash
   php artisan serve
   ```

6. En otra terminal, ejecutar Vite:

   ```bash
   npm run dev
   ```

Para un build de produccion de los assets:

```bash
npm run build
```

## Rutas actuales

- `GET /`: pantalla inicial de la base tecnica.
- `GET /api/health`: endpoint tecnico que responde `{"status":"ok"}`.

No existen todavia endpoints funcionales de citas, doctores o pacientes.

## Arquitectura

La aplicacion evolucionara mediante capas: presentacion, API/controllers, Form Requests, servicios de aplicacion, Eloquent y MySQL Docker. Los controllers se mantendran delgados y las reglas de negocio se ejecutaran en servidor. Consulte [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md) para el detalle.

## Estrategia Git

Cada feature se desarrollara desde el `main` actualizado, se revisara mediante Pull Request y se integrara mediante merge. El orden previsto es:

1. `feature/docker-mysql-schema`
2. `feature/api-rest-citas`
3. `feature/validacion-conflictos-estados`
4. `feature/fullcalendar-ui`

Las restricciones del examen y el flujo Git completo estan en [docs/REGLAS_EXAMEN.md](docs/REGLAS_EXAMEN.md).

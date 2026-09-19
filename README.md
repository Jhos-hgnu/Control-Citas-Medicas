# Control de Citas Medicas

Sistema de Control de Citas Medicas construido con Laravel 12. La primera feature proporciona MySQL en Docker, el esquema relacional y datos semilla ficticios; no implementa aun la API funcional ni la interfaz de calendario.

## Tecnologias

- Laravel 12
- Blade
- JavaScript y Vite
- MySQL 8.0 mediante Docker
- FullCalendar (fase posterior)

## Requisitos

- PHP 8.2 o superior con `pdo_mysql`
- Composer 2
- Node.js y npm
- Docker Desktop con Docker Compose

MySQL se ejecuta exclusivamente dentro de Docker. No se requiere ni se utiliza una base MySQL local.

## Instalacion

1. Instalar dependencias PHP y frontend:

   ```bash
   composer install
   npm install
   ```

2. Crear la configuracion local a partir de `.env.example`. Los valores de desarrollo incluidos usan MySQL Docker en `127.0.0.1:3307`.

3. Generar la clave de aplicacion:

   ```bash
   php artisan key:generate
   ```

4. Iniciar MySQL:

   ```bash
   docker compose up -d
   ```

5. Verificar el contenedor:

   ```bash
   docker compose ps
   ```

6. Crear el esquema y datos semilla ficticios:

   ```bash
   php artisan migrate --seed
   ```

7. Iniciar Laravel y Vite en terminales separadas:

   ```bash
   php artisan serve
   npm run dev
   ```

Para compilar los assets de produccion:

```bash
npm run build
```

## Docker y persistencia

El servicio `mysql` publica el puerto `3307`, usa el volumen nombrado `mysql_data` y define un healthcheck de MySQL. La imagen `mysql:8.0` se utiliza por compatibilidad comprobada con el cliente PDO MySQL del entorno de desarrollo.

Para detener el contenedor sin borrar los datos:

```bash
docker compose down
```

Este comando conserva el volumen. No use `docker compose down -v` como comando habitual, porque elimina los datos del volumen.

## Esquema inicial

- `pacientes`: informacion basica de pacientes.
- `doctores`: informacion basica y especialidad de doctores.
- `citas`: referencias a paciente y doctor, rango `inicio`/`fin`, motivo y estado inicial `pendiente`.

Las claves foraneas de citas restringen el borrado de pacientes y doctores para preservar el historial. Los modelos Eloquent contienen las relaciones basicas y casts de fecha para `inicio` y `fin`.

## Rutas actuales

- `GET /`: pantalla inicial tecnica.
- `GET /api/health`: endpoint tecnico que responde `{"status":"ok"}`.

Los endpoints funcionales de citas, pacientes y doctores se exponen exclusivamente bajo `/api`.

## API

La API REST de citas, pacientes y doctores esta documentada en [docs/API.md](docs/API.md). Los conflictos de horario se validan en el servidor.

## Calendario

La interfaz en `/citas` usa FullCalendar con la API REST y MySQL Docker como fuente de datos. Inicie Docker, Laravel y Vite con `docker compose up -d`, `php artisan serve` y `npm run dev` para usarla en desarrollo.

## Arquitectura y evidencia

La arquitectura prevista esta documentada en [docs/ARQUITECTURA.md](docs/ARQUITECTURA.md). Las restricciones del examen estan en [docs/REGLAS_EXAMEN.md](docs/REGLAS_EXAMEN.md), la evidencia escrita en [EVIDENCIA.md](EVIDENCIA.md) y las [capturas visuales en Google Drive](https://drive.google.com/drive/folders/1snO23LupPrZ7MGMfv7og9GHg3U6DLX5o?usp=drive_link).

## Estrategia Git

Las features se crean desde `developer` y se publican para revision antes de un Pull Request y merge posterior hacia `main`. Esta feature no ha creado Pull Request ni merge.

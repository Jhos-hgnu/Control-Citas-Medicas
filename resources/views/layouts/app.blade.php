<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Base del Sistema de Control de Citas Medicas.">
        <title>@yield('title', 'Control de Citas Medicas')</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body>
        <div class="site-shell">
            <header class="site-header">
                <a class="brand" href="{{ url('/') }}">Control de Citas Medicas</a>
                <nav aria-label="Navegacion principal">
                    <a href="{{ url('/') }}">Inicio</a>
                </nav>
            </header>

            <main>
                @yield('content')
            </main>

            <footer class="site-footer">
                <p>Base tecnica para la gestion de citas medicas.</p>
            </footer>
        </div>
    </body>
</html>

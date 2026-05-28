<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIGVI - Install D</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-900 text-white">
    <div class="relative min-h-screen flex flex-col items-center justify-center bg-gradient-to-br from-gray-900 via-gray-800 to-gray-900">
        <!-- Círculo decorativo -->
        <div class="absolute top-0 left-0 w-64 h-64 bg-blue-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-purple-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute bottom-0 left-0 w-80 h-80 bg-indigo-600 rounded-full mix-blend-multiply filter blur-3xl opacity-20 animate-blob animation-delay-4000"></div>

        <div class="relative z-10 text-center max-w-4xl px-4">
            <h1 class="text-6xl md:text-7xl font-extrabold mb-6">
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">SIGVI</span>
            </h1>
            <p class="text-2xl md:text-3xl text-gray-300 mb-4">Sistema de Gestión de Ventas e Inventario</p>
            <p class="text-lg md:text-xl text-gray-400 mb-10 max-w-2xl mx-auto">La solución que <span class="text-white font-semibold">Install D</span> necesita para llevar su negocio de repuestos automotrices al siguiente nivel. Controla tu inventario, acelera tus ventas y toma decisiones inteligentes.</p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-primary-custom text-lg px-8 py-4">Ir al Panel</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary-custom text-lg px-8 py-4">Iniciar Sesión</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn-success-custom text-lg px-8 py-4">Crear Cuenta</a>
                        @endif
                    @endauth
                @endif
            </div>
        </div>

        <footer class="absolute bottom-4 text-gray-500 text-sm">
            © 2026 Install D - Todos los derechos reservados.
        </footer>
    </div>
</body>
</html>

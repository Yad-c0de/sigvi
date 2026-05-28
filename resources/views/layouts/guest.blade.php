<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'SIGVI') }} - Install D</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-gray-800 to-gray-900">
        <!-- Logo / Marca -->
        <div class="mb-8">
            <a href="/" class="text-5xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-purple-500">
                SIGVI
            </a>
            <p class="text-center text-gray-400 mt-2 text-sm">Sistema de Gestión de Ventas e Inventario</p>
        </div>

        <!-- Tarjeta de Contenido -->
        <div class="w-full sm:max-w-md px-6 py-8 bg-white shadow-lg overflow-hidden sm:rounded-2xl border border-gray-100">
            {{ $slot }}
        </div>

        <p class="mt-8 text-center text-gray-500 text-xs">
            &copy; {{ date('Y') }} Install D. Todos los derechos reservados.
        </p>
    </div>
</body>
</html>

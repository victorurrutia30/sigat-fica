<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIGAT-FICA') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-utec-bg-light font-sans text-utec-gray-dark antialiased">
    <div class="flex min-h-screen flex-col items-center justify-center px-4 py-8">
        <div class="mb-6 text-center">
            <a href="/" class="inline-flex flex-col items-center gap-3">
                <span class="flex h-24 w-24 items-center justify-center rounded-2xl border border-utec-gray-medium bg-white p-3 shadow-sm">
                    <x-application-logo class="max-h-16 w-auto object-contain" />
                </span>

                <span>
                    <span class="block text-sm font-bold uppercase tracking-[0.22em] text-utec-primary">
                        SIGAT-FICA
                    </span>
                    <span class="mt-1 block text-xs text-gray-500">
                        Sistema de Gestión de Tutorías
                    </span>
                </span>
            </a>
        </div>

        <div class="w-full max-w-md overflow-hidden rounded-xl border border-utec-gray-medium bg-white px-6 py-6 shadow-sm">
            {{ $slot }}
        </div>

        <p class="mt-6 text-center text-xs text-gray-500">
            UTEC · FICA · Programa de Tutores
        </p>
    </div>
</body>

</html>
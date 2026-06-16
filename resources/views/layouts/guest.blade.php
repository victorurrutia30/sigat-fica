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
    <main class="min-h-screen lg:grid lg:grid-cols-[0.95fr_1.05fr]">
        <section class="relative hidden overflow-hidden bg-utec-primary px-12 py-12 text-white lg:flex lg:flex-col lg:justify-between">
            <div class="pointer-events-none absolute -right-20 -top-20 h-64 w-64 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute right-14 top-20 h-32 w-32 rounded-full border border-white/10"></div>
            <div class="pointer-events-none absolute -bottom-16 -left-16 h-56 w-56 rounded-full bg-utec-primary-dark/60"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-4 rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                    <span class="flex h-14 w-14 items-center justify-center rounded-xl bg-white p-1.5">
                        <x-application-logo class="max-h-12 w-auto object-contain" />
                    </span>

                    <span>
                        <span class="block text-[10px] font-medium uppercase tracking-[0.24em] text-white/60">
                            FICA · UTEC
                        </span>
                        <span class="mt-1 block text-base font-semibold text-white">
                            SIGAT-FICA
                        </span>
                    </span>
                </div>
            </div>

            <div class="relative z-10 max-w-xl">
                <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-white/55">
                    Sistema interno
                </p>

                <h1 class="text-4xl font-semibold leading-tight xl:text-5xl">
                    Gestión académica del Programa de Tutores
                </h1>

                <p class="mt-6 text-base leading-7 text-white/75">
                    Acceso restringido para Coordinación y Tutores autorizados.
                    Administra asignaciones, seguimiento de casos y consolidados desde una plataforma centralizada.
                </p>

                <div class="mt-8 grid gap-3 text-sm text-white/75">
                    <div class="flex items-center gap-3">
                        <span class="h-2 w-2 rounded-full bg-white/70"></span>
                        Propuesta de asignación y aprobación administrativa.
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="h-2 w-2 rounded-full bg-white/70"></span>
                        Seguimiento de estudiantes no evaluados.
                    </div>

                    <div class="flex items-center gap-3">
                        <span class="h-2 w-2 rounded-full bg-white/70"></span>
                        Control de consolidados y cumplimiento.
                    </div>
                </div>
            </div>

            <div class="relative z-10 text-xs text-white/50">
                UTEC · Facultad de Informática y Ciencias Aplicadas
            </div>
        </section>

        <section class="flex min-h-screen items-center justify-center px-4 py-8 sm:px-6 lg:px-8">
            <div class="w-full max-w-md">
                <div class="mb-7 text-center lg:hidden">
                    <a href="/" class="inline-flex flex-col items-center gap-3">
                        <span class="flex h-20 w-20 items-center justify-center rounded-2xl border border-utec-gray-medium bg-white p-1.5 shadow-sm">
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

                <div class="rounded-2xl border border-utec-gray-medium bg-white p-6 shadow-sm sm:p-8">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs text-gray-500">
                    Uso restringido para usuarios autorizados.
                </p>
            </div>
        </section>
    </main>
</body>

</html>
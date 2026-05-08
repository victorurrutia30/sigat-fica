<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIGAT-FICA</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased bg-gray-50 text-gray-900">
    <div class="min-h-screen">
        <header class="border-b border-gray-200 bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-blue-700">
                        SIGAT-FICA
                    </p>
                    <h1 class="text-lg font-bold text-gray-900">
                        Sistema de Gestión de Tutorías
                    </h1>
                </div>

                <nav class="flex items-center gap-3">
                    @auth
                    <a
                        href="{{ auth()->user()->rol === 'coordinacion' ? route('dashboard') : route('mis-asignaciones') }}"
                        class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        Ir al sistema
                    </a>
                    @else
                    <a
                        href="{{ route('login') }}"
                        class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                        Iniciar sesión
                    </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
                <div class="flex flex-col justify-center">
                    <span class="mb-4 inline-flex w-fit rounded-full bg-blue-50 px-3 py-1 text-sm font-medium text-blue-700">
                        Facultad de Informática y Ciencias Aplicadas
                    </span>

                    <h2 class="text-4xl font-bold tracking-tight text-gray-900 sm:text-5xl">
                        Control interno del Programa de Tutores
                    </h2>

                    <p class="mt-6 text-lg leading-8 text-gray-600">
                        Prototipo web para gestionar asignaciones de tutores, seguimiento de estudiantes no evaluados,
                        consolidado de casos y trazabilidad administrativa del programa.
                    </p>

                    <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                        @auth
                        <a
                            href="{{ auth()->user()->rol === 'coordinacion' ? route('dashboard') : route('mis-asignaciones') }}"
                            class="inline-flex justify-center rounded-md bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                            Continuar
                        </a>
                        @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex justify-center rounded-md bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm hover:bg-blue-800">
                            Entrar al sistema
                        </a>
                        @endauth
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
                    <h3 class="text-lg font-semibold text-gray-900">
                        Alcance del Sprint 1
                    </h3>

                    <div class="mt-6 grid gap-4">
                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <p class="font-semibold text-gray-900">Autenticación por rol</p>
                            <p class="mt-1 text-sm text-gray-600">
                                Acceso diferenciado para Coordinación y Tutor usando correo institucional.
                            </p>
                        </div>

                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <p class="font-semibold text-gray-900">Base académica</p>
                            <p class="mt-1 text-sm text-gray-600">
                                Migraciones y modelos principales para ciclos, tutores, materias, secciones y seguimiento.
                            </p>
                        </div>

                        <div class="rounded-xl border border-gray-100 bg-gray-50 p-4">
                            <p class="font-semibold text-gray-900">Navegación inicial</p>
                            <p class="mt-1 text-sm text-gray-600">
                                Menús visuales separados por rol y vistas base para dashboard y asignaciones.
                            </p>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-gray-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-gray-500 sm:px-6 lg:px-8">
                SIGAT-FICA — Prototipo académico UTEC.
            </div>
        </footer>
    </div>
</body>

</html>
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

<body class="bg-utec-bg-light font-sans text-utec-gray-dark antialiased">
    <div class="min-h-screen">
        <header class="border-b border-utec-gray-medium bg-white">
            <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-4 sm:px-6 lg:px-8">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-wide text-utec-primary">
                        SIGAT-FICA
                    </p>
                    <h1 class="text-lg font-bold text-utec-gray-dark">
                        Sistema de Gestión de Tutorías
                    </h1>
                </div>

                <nav class="flex items-center gap-3">
                    @auth
                    <a
                        href="{{ auth()->user()->rol === 'coordinacion' ? route('dashboard') : route('mis-asignaciones') }}"
                        class="btn-primary">
                        Ir al sistema
                    </a>
                    @else
                    <a
                        href="{{ route('login') }}"
                        class="btn-primary">
                        Iniciar sesión
                    </a>
                    @endauth
                </nav>
            </div>
        </header>

        <main>
            <section class="mx-auto grid max-w-7xl gap-10 px-4 py-16 sm:px-6 lg:grid-cols-2 lg:px-8 lg:py-24">
                <div class="flex flex-col justify-center">
                    <span class="mb-4 inline-flex w-fit rounded-full bg-utec-primary-soft px-3 py-1 text-sm font-medium text-utec-primary">
                        Facultad de Informática y Ciencias Aplicadas
                    </span>

                    <h2 class="text-4xl font-bold tracking-tight text-utec-gray-dark sm:text-5xl">
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
                            class="btn-primary px-5 py-3">
                            Continuar
                        </a>
                        @else
                        <a
                            href="{{ route('login') }}"
                            class="btn-primary px-5 py-3">
                            Entrar al sistema
                        </a>
                        @endauth
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-semibold text-utec-gray-dark">
                            Alcance del Sprint 1
                        </h3>

                        <div class="mt-6 grid gap-4">
                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                                <p class="font-semibold text-utec-primary">Autenticación por rol</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    Acceso diferenciado para Coordinación y Tutor usando correo institucional.
                                </p>
                            </div>

                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                                <p class="font-semibold text-utec-primary">Base académica</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    Migraciones y modelos principales para ciclos, tutores, materias, secciones y seguimiento.
                                </p>
                            </div>

                            <div class="rounded-xl border border-utec-gray-medium bg-utec-bg-light p-4">
                                <p class="font-semibold text-utec-primary">Navegación inicial</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    Menús visuales separados por rol y vistas base para dashboard y asignaciones.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <footer class="bg-utec-gray-dark">
            <div class="mx-auto max-w-7xl px-4 py-6 text-sm text-white sm:px-6 lg:px-8">
                SIGAT-FICA · UTEC / FICA · 2026
            </div>
        </footer>
    </div>
</body>

</html>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIGAT-FICA</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen overflow-x-hidden bg-white font-sans text-utec-gray-dark antialiased">
    <main class="min-h-screen bg-white lg:h-screen lg:overflow-hidden">
        <section class="grid min-h-screen w-full bg-white lg:h-screen lg:min-h-0 lg:grid-cols-[0.95fr_1.05fr]">

            <div class="relative flex min-h-[540px] flex-col justify-center overflow-hidden bg-utec-primary px-6 py-10 sm:px-10 lg:h-screen lg:min-h-0 lg:px-14 lg:py-12 xl:px-16">
                <div class="pointer-events-none absolute -right-16 -top-16 h-56 w-56 rounded-full border border-white/10"></div>
                <div class="pointer-events-none absolute right-6 top-8 h-32 w-32 rounded-full border border-white/10"></div>
                <div class="pointer-events-none absolute -bottom-12 -left-12 h-44 w-44 rounded-full bg-utec-primary-dark/60"></div>

                <div class="relative z-10 max-w-xl">
                    <div class="mb-8 inline-flex items-center gap-3 rounded-2xl border border-white/15 bg-white/10 px-4 py-3">
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-white p-2">
                            <img
                                src="{{ asset('images/logo-utec.png') }}"
                                alt="UTEC"
                                class="max-h-9 w-auto object-contain">
                        </span>

                        <span>
                            <span class="block text-[10px] font-medium uppercase tracking-widest text-white/60">
                                FICA · UTEC
                            </span>
                            <span class="mt-1 block text-sm font-semibold text-white">
                                SIGAT-FICA
                            </span>
                        </span>
                    </div>

                    <p class="mb-3 text-xs font-semibold uppercase tracking-[0.24em] text-white/55">
                        SIGAT-FICA
                    </p>

                    <h1 class="max-w-xl text-4xl font-semibold leading-tight text-white sm:text-5xl xl:text-6xl">
                        Sistema de Gestión de Tutorías
                    </h1>

                    <p class="mt-6 max-w-lg text-base leading-7 text-white/75 xl:text-lg xl:leading-8">
                        Plataforma interna para carga académica, asignación de tutores,
                        seguimiento de estudiantes no evaluados y control de consolidados.
                    </p>

                    <div class="mt-9 grid gap-4 xl:gap-5">
                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 11a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M16 11a3 3 0 1 0 0-6a3 3 0 0 0 0 6Z" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M3 20a5 5 0 0 1 10 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M11 20a5 5 0 0 1 10 0" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-white">Asignación de tutores</p>
                                <p class="mt-1 text-sm leading-6 text-white/70">
                                    Validación de DTCs, horarios y aprobación administrativa.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M8 6h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M8 12h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M8 18h11" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M4.5 6h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" />
                                    <path d="M4.5 12h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" />
                                    <path d="M4.5 18h.01" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-white">Seguimiento de casos</p>
                                <p class="mt-1 text-sm leading-6 text-white/70">
                                    Registro de estudiantes no evaluados, gestiones, causas y cierres.
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/15 text-white">
                                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                    <path d="M5 19V9" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M12 19V5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M19 19v-7" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                    <path d="M4 19h16" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-base font-semibold text-white">Control de consolidados</p>
                                <p class="mt-1 text-sm leading-6 text-white/70">
                                    Tablero de cumplimiento, observaciones y exportación institucional.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex min-h-[620px] flex-col bg-white lg:h-screen lg:min-h-0">
                <div class="relative min-h-[390px] flex-1 overflow-hidden lg:min-h-0">
                    @if(file_exists(public_path('images/IMG-WELCOME.png')))
                    <img
                        src="{{ asset('images/IMG-WELCOME.png') }}"
                        alt="Facultad de Informática y Ciencias Aplicadas"
                        class="h-full w-full object-cover">
                    @else
                    <div class="flex h-full w-full items-center justify-center bg-gradient-to-br from-utec-primary-soft via-white to-utec-gray-medium/30">
                        <div class="text-center">
                            <div class="mx-auto flex h-28 w-28 items-center justify-center rounded-2xl border border-utec-primary/15 bg-white shadow-sm">
                                <span class="text-5xl font-semibold text-utec-primary">S</span>
                            </div>
                            <div class="mx-auto my-5 h-0.5 w-12 rounded-full bg-utec-primary"></div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-utec-primary-light">
                                Sistema de Tutorías
                            </p>
                        </div>
                    </div>
                    @endif

                    <div class="pointer-events-none absolute inset-0 bg-gradient-to-t from-utec-primary/60 via-transparent to-transparent"></div>

                    <div class="absolute bottom-4 left-4 right-4 rounded-xl bg-white/95 p-4 shadow-sm backdrop-blur sm:bottom-5 sm:left-5 sm:right-5 sm:p-5">
                        <p class="mb-1 text-[10px] font-semibold uppercase tracking-[0.18em] text-utec-primary">
                            Plataforma interna
                        </p>
                        <p class="text-lg font-semibold leading-snug text-utec-gray-dark sm:text-xl">
                            Gestión académica del Programa de Tutores
                        </p>
                        <p class="mt-2 text-base leading-7 text-gray-600">
                            Coordinación administra propuestas, tutores, casos, consolidados y trazabilidad.
                        </p>
                        <div class="mt-4 h-0.5 w-8 rounded-full bg-utec-primary"></div>
                    </div>
                </div>

                <div class="shrink-0 border-t border-utec-gray-medium/40 bg-white px-5 py-5 sm:px-8 lg:py-6">
                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <p class="text-base font-semibold text-utec-gray-dark">
                                Acceso al sistema
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                Uso restringido para Coordinación y Tutores autorizados.
                            </p>
                        </div>

                        @auth
                        <a
                            href="{{ auth()->user()->rol === 'coordinacion' ? route('dashboard') : route('mis-asignaciones') }}"
                            class="btn-primary inline-flex w-full items-center justify-center gap-2 whitespace-nowrap sm:w-auto">
                            Ir al sistema
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        @else
                        <a
                            href="{{ route('login') }}"
                            class="btn-primary inline-flex w-full items-center justify-center gap-2 whitespace-nowrap sm:w-auto">
                            Iniciar sesión
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M5 12h14" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" />
                                <path d="m13 6 6 6-6 6" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
    </main>
</body>

</html>
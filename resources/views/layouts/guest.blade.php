<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'SIGAT-FICA') }} — Acceso</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-white font-sans antialiased">

        <div class="flex min-h-screen">

            <div class="flex w-full flex-col justify-center px-8 py-10 sm:px-12 lg:w-[420px] lg:flex-none xl:w-[460px]">

                <div class="mb-6 flex items-center gap-3">
                    <img
                        src="{{ asset('images/LOGO.png') }}"
                        alt="UTEC"
                        class="h-9 w-9 flex-shrink-0 rounded-full object-cover shadow-sm"
                    />
                    <div class="leading-tight">
                        <p class="text-sm font-bold text-utec-gray-dark">SIGAT-FICA</p>
                        <p class="text-[11px] text-utec-gray-medium">Universidad Tecnológica</p>
                    </div>
                </div>

                <div class="mb-6 border-t border-utec-gray-medium"></div>

                {{ $slot }}

            </div>

            <div class="relative hidden flex-1 flex-col justify-between bg-utec-primary p-12 lg:flex overflow-hidden">

                <div class="pointer-events-none absolute right-0 top-0 opacity-[0.15]">
                    <svg width="220" height="220" viewBox="0 0 220 220" fill="none" xmlns="http://www.w3.org/2000/svg">
                        @php $cols = 8; $rows = 8; $gap = 26; @endphp
                        @for ($r = 0; $r < $rows; $r++)
                            @for ($c = 0; $c < $cols; $c++)
                                <circle
                                    cx="{{ $c * $gap + 14 }}"
                                    cy="{{ $r * $gap + 14 }}"
                                    r="2.5"
                                    fill="white"
                                />
                            @endfor
                        @endfor
                    </svg>
                </div>

                <div class="inline-flex w-fit items-center gap-2 rounded-full bg-white/10 px-3.5 py-1.5">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-400 shadow-[0_0_6px_1px_rgba(52,211,153,0.7)]"></span>
                    <span class="text-[10px] font-semibold uppercase tracking-widest text-white/75">
                        Sistema institucional UTEC
                    </span>
                </div>

                <div>
                    <h1 class="mb-4 text-5xl font-bold leading-[1.1] text-white">
                        Gestión de tutorías<br>FICA
                    </h1>
                    <p class="max-w-xs text-sm leading-relaxed text-white/55">
                        Plataforma interna para la asignación, seguimiento y control del programa
                        de tutores de la Facultad de Informática.
                    </p>
                </div>

                <div>
                    <div class="rounded-xl bg-white/10 p-4 backdrop-blur-sm">
                        <p class="mb-3 text-[10px] font-semibold uppercase tracking-widest text-white/40">
                            Coordinadora del programa
                        </p>
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3">
                                <img
                                    src="{{ asset('images/CIRA.jpg') }}"
                                    alt="Ing. Cira Jazmine Alas Alvarenga"
                                    class="h-10 w-10 flex-shrink-0 rounded-full object-cover object-top ring-2 ring-white/20"
                                />
                                <div>
                                    <p class="text-sm font-semibold leading-snug text-white">
                                        Ing. Cira Jazmine<br>Alas Alvarenga
                                    </p>
                                    <p class="mt-0.5 text-[11px] text-white/45">
                                        Facultad de Informática y ciencias aplicadas · FICA
                                    </p>
                                </div>
                            </div>
                            <span class="flex-shrink-0 rounded-full bg-white/10 px-3 py-1 text-[11px] text-white/60">
                                Coordinación
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </body>
</html>

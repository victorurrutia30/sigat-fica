<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SIGAT-FICA</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white h-screen flex items-center justify-center overflow-hidden">

    <div class="grid grid-cols-2 w-full max-w-5xl mx-auto rounded-2xl overflow-hidden shadow-xl"
         style="height: 88vh; max-height: 620px;">

        <div class="bg-utec-primary flex flex-col justify-between p-12 relative overflow-hidden">

            <div class="absolute -top-12 -right-12 w-52 h-52 rounded-full border border-white/10 pointer-events-none"></div>
            <div class="absolute top-4 right-4 w-32 h-32 rounded-full border border-white/6 pointer-events-none"></div>
            <div class="absolute -bottom-10 -left-10 w-40 h-40 rounded-full pointer-events-none"
                 style="background: rgba(61,13,34,0.55)"></div>

            <div class="relative z-10">
                <div class="inline-flex items-center gap-2 bg-white/10 border border-white/15
                            rounded-full px-3 py-1.5 mb-8">
                    <div class="w-1.5 h-1.5 rounded-full bg-utec-primary-soft"></div>
                    <span class="text-[10px] text-white/70 uppercase tracking-widest font-medium">
                        FICA · UTEC
                    </span>
                </div>

                <h1 class="text-[30px] font-medium text-white leading-snug mb-4">
                    Sistema de gestión<br>
                    de tutorías<br>
                </h1>

                <p class="text-[13px] text-white/65 leading-relaxed max-w-[240px] mb-8">
                    Asignación, seguimiento y control de consolidados para el programa de tutores.
                </p>

                {{-- Features --}}
                <div class="flex flex-col gap-4">
                    @foreach([
                        ['ti-users-group',    'Asignación de tutores',   'Validación de DTCs y horarios'],
                        ['ti-clipboard-list', 'Seguimiento de casos',    'Gestiones y causas por estudiante'],
                        ['ti-chart-bar',      'Control de consolidados', 'Tablero de cumplimiento por periodo'],
                    ] as [$icon, $title, $desc])
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-white/15 flex items-center justify-center flex-shrink-0">
                            <i class="ti {{ $icon }} text-white text-sm" aria-hidden="true"></i>
                        </div>
                        <div>
                            <p class="text-[12.5px] font-semibold text-white">{{ $title }}</p>
                            <p class="text-[11.5px] text-white/65">{{ $desc }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="relative z-10 flex gap-2">
                <span class="bg-white/8 border border-white/12 rounded-md px-2.5 py-1
                             text-[10px] text-white/50 uppercase tracking-widest">SIGAT-FICA</span>
                <span class="bg-white/8 border border-white/12 rounded-md px-2.5 py-1
                             text-[10px] text-white/50 uppercase tracking-widest">2026</span>
            </div>
        </div>

        <div class="flex flex-col" style="min-height: 0;">

            <div class="relative overflow-hidden" style="flex: 1; min-height: 0;">

                @if(file_exists(public_path('images/IMG-WELCOME.png')))
                    <img src="{{ asset('images/IMG-WELCOME.png') }}" alt="UTEC"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full flex items-center justify-center
                                bg-gradient-to-br from-utec-primary-soft to-utec-gray-medium/30">
                        <div class="text-center opacity-40">
                            <div class="text-[100px] font-light text-utec-primary leading-none">U</div>
                            <div class="w-10 h-0.5 bg-utec-primary mx-auto my-2"></div>
                            <div class="text-[10px] uppercase tracking-[.18em] text-utec-primary-light">
                                Universidad Tecnológica
                            </div>
                        </div>
                    </div>
                @endif

                <div class="absolute inset-0 bg-gradient-to-t from-utec-primary/55 to-transparent pointer-events-none"></div>

                <div class="absolute bottom-4 left-4 right-4 bg-white/95 backdrop-blur-sm rounded-xl p-4">
                    <p class="text-[9px] font-semibold uppercase tracking-[.16em] text-utec-primary mb-1">
                        Sistema institucional
                    </p>
                    <p class="text-[15px] font-medium text-utec-gray-dark leading-snug mb-2">
                        Plataforma de tutorías<br>SIGAT-FICA
                    </p>
                    <div class="w-6 h-0.5 bg-utec-primary rounded-full"></div>
                </div>
            </div>

            <div class="bg-white border-t border-utec-gray-medium/40
                        px-8 py-5 flex items-center justify-between gap-4 flex-shrink-0">
                <div>
                    <p class="text-[14px] font-medium text-utec-gray-dark">¿Listo para comenzar?</p>
                    <p class="text-[12px] text-utec-gray-medium">Acceso restringido al personal autorizado</p>
                </div>

                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="btn-primary flex items-center gap-2 whitespace-nowrap">
                        Ir al sistema
                        <i class="ti ti-arrow-right" aria-hidden="true"></i>
                    </a>
                @else
                   <a href="{{ route('login') }}"
                        id="btn-login"
                        class="btn-primary flex items-center gap-2 whitespace-nowrap">
                            <i class="ti ti-arrow-right" id="btn-icon" aria-hidden="true"></i>
                            <span id="btn-texto">Iniciar sesión</span>
                    </a>
                @endauth
            </div>
        </div>

    </div>
</body>
</html>
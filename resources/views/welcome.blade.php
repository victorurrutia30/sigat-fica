<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UTEC Virtual — Gestión de Tutorías</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo-utec.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500;600&family=Figtree:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .font-serif-custom { font-family: 'Cormorant Garamond', serif; }
        .hero-title { font-size: clamp(56px, 7vw, 88px); line-height: 0.95; letter-spacing: -0.02em; }
        .bg-grid {
            background-image:
                linear-gradient(rgba(90,21,51,0.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(90,21,51,0.04) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="bg-utec-bg-light text-utec-gray-dark h-screen overflow-hidden">

    {{-- Decorative ambient blobs --}}
    <div class="fixed -top-28 -left-28 w-[500px] h-[500px] rounded-full pointer-events-none
                bg-[radial-gradient(circle,rgba(90,21,51,0.10)_0%,transparent_70%)]"></div>
    <div class="fixed -bottom-24 -right-24 w-[400px] h-[400px] rounded-full pointer-events-none
                bg-[radial-gradient(circle,rgba(61,13,34,0.06)_0%,transparent_70%)]"></div>
    <div class="bg-grid fixed inset-0 pointer-events-none"></div>

    {{-- Header --}}
    <header class="fixed top-0 left-0 right-0 z-50 flex items-center px-16 py-7">
        <a href="/" class="flex items-center gap-3 no-underline">
            @if(file_exists(public_path('images/LOGO.png')))
                <img src="{{ asset('images/LOGO.png') }}" alt="UTEC" class="w-8 h-8 object-contain">
            @else
                <div class="w-8 h-8 rounded-md flex items-center justify-center
                            bg-utec-primary text-white text-xs font-bold">
                    UV
                </div>
            @endif
            <div>
                <div class="text-[13px] font-semibold uppercase tracking-[0.06em] text-utec-gray-dark">
                    UTEC <span class="text-utec-primary">Virtual</span>
                </div>
                <div class="text-[10px] uppercase tracking-[0.15em] text-utec-gray-medium font-light">
                    Universidad Tecnológica
                </div>
            </div>
        </a>
    </header>

    {{-- Main two-column layout --}}
    <main class="h-screen grid grid-cols-2">

        {{-- Left: hero content --}}
        <div class="flex flex-col justify-center px-16">

            <p class="text-[10px] uppercase tracking-[0.2em] font-medium mb-5 text-utec-primary">
                Sistema de gestión de tutorías — UTEC Virtual
            </p>

            <h1 class="hero-title font-serif-custom font-normal mb-7 text-utec-gray-dark">
                Gestión de<br>
                <span class="text-utec-primary">Tutorías</span>
            </h1>

            <p class="text-[13px] leading-[1.75] max-w-[340px] mb-8 font-light text-utec-gray-dark/70">
                Administra, asigna y da seguimiento a las tutorías académicas
                de forma eficiente desde una sola plataforma institucional.
            </p>

            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}"
                       class="btn-primary self-start text-[11px] uppercase tracking-[0.14em] px-9 py-3.5 rounded-sm">
                        Ir al sistema
                    </a>
                @else
                    <a href="{{ route('login') }}"
                       class="btn-primary self-start text-[11px] uppercase tracking-[0.14em] px-9 py-3.5 rounded-sm">
                        Iniciar sesión
                    </a>
                @endauth
            @endif
        </div>

        {{-- Right: image panel --}}
        <div class="relative overflow-hidden flex items-center justify-center">
            @if(file_exists(public_path('images/01-A.png')))
                <img src="{{ asset('images/01-A.png') }}" alt="UTEC Virtual"
                     class="w-full h-full object-cover grayscale-[15%]">
            @else
                <div class="w-full h-full flex items-center justify-center
                            bg-gradient-to-br from-utec-primary-soft to-utec-gray-medium/20">
                    <div class="text-center opacity-30">
                        <div class="font-serif-custom font-light leading-none text-utec-primary"
                             style="font-size:140px;">U</div>
                        <div class="mx-auto mb-4 bg-utec-primary h-[3px] w-[60px]"></div>
                        <div class="text-[11px] uppercase tracking-[0.2em] text-utec-gray-medium">
                            Universidad Tecnológica
                        </div>
                    </div>
                </div>
            @endif

            {{-- Floating info card --}}
            <div class="card absolute bottom-12 shadow-xl"
                 style="left:50%; transform:translateX(-50%); min-width:220px; max-width:260px;">
                <div class="card-body">
                    <div class="text-[9px] uppercase tracking-[0.18em] font-semibold mb-1.5 text-utec-primary">
                        Sistema institucional
                    </div>
                    <div class="font-serif-custom text-[18px] font-medium text-utec-gray-dark leading-tight">
                        Plataforma de tutorías<br>UTEC Virtual
                    </div>
                    <div class="text-[11px] text-utec-gray-medium mt-1.5">
                        Gestión académica integral TEXT
                    </div>
                    <div class="mt-3 bg-utec-primary h-[2px] w-8"></div>
                </div>
            </div>
        </div>
    </main>

    {{-- Side decorative label --}}
    <div class="fixed right-6 top-1/2 -translate-y-1/2 rotate-90
                text-[9px] uppercase tracking-[0.22em] text-utec-gray-medium/60
                whitespace-nowrap pointer-events-none select-none">
        UTEC
    </div>

</body>
</html>
<nav x-data="{ open: false }" class="bg-utec-primary shadow-md">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">

            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ auth()->user()->rol === 'coordinacion' ? route('dashboard') : route('mis-asignaciones') }}">
                        <div class="flex items-center gap-3">
                            <img src="{{ asset('images/LOGO.png') }}" alt="SIGAT-FICA" class="block h-9 w-auto" />
                            <div class="hidden leading-tight sm:block">
                                <p class="text-sm font-bold text-white">SIGAT-FICA</p>
                                <p class="text-[11px] text-white/50">Programa de Tutores</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="hidden sm:-my-px sm:ms-10 sm:flex sm:items-center sm:gap-0.5">
                    @if (auth()->user()->rol === 'coordinacion')
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('dashboard')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('ciclos.index') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('ciclos.*')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Ciclos
                        </a>
                        <a href="{{ route('tutores.index') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('tutores.*')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Tutores
                        </a>
                        <a href="{{ route('materias.index') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('materias.*')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Materias
                        </a>
                        <a href="{{ route('carga-academica.create') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('carga-academica.*')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Carga académica
                        </a>
                        <a href="{{ route('propuestas.index') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('propuestas.*')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Propuestas
                        </a>
                        <span class="inline-flex items-center border-b-2 border-transparent px-3 py-2 text-sm font-medium text-white/30 cursor-default">
                            Periodos
                        </span>
                    @endif

                    @if (auth()->user()->rol === 'tutor')
                        <a href="{{ route('mis-asignaciones') }}"
                           class="inline-flex items-center border-b-2 px-3 py-2 text-sm font-medium transition-colors
                                  {{ request()->routeIs('mis-asignaciones')
                                     ? 'border-white text-white'
                                     : 'border-transparent text-white/65 hover:border-white/40 hover:text-white' }}">
                            Mis asignaciones
                        </a>
                        <span class="inline-flex items-center border-b-2 border-transparent px-3 py-2 text-sm font-medium text-white/30 cursor-default">Casos</span>
                        <span class="inline-flex items-center border-b-2 border-transparent px-3 py-2 text-sm font-medium text-white/30 cursor-default">Consolidado</span>
                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2.5 rounded-lg px-3 py-2 text-sm transition hover:bg-white/10 focus:outline-none">
                            <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-xs font-bold text-white ring-2 ring-white/20">
                                {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                            </div>
                            <div class="hidden text-left lg:block">
                                <div class="text-sm font-semibold text-white">
                                    {{ auth()->user()->nombre }}
                                </div>
                                <div class="text-[11px] text-white/50">
                                    {{ auth()->user()->rol === 'coordinacion' ? 'Coordinación' : 'Tutor' }}
                                </div>
                            </div>
                            <svg class="h-4 w-4 text-white/40" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            Perfil
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link
                                :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();">
                                Cerrar sesión
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <div class="-me-2 flex items-center sm:hidden">
                <button
                    @click="open = ! open"
                    class="inline-flex items-center justify-center rounded-md p-2 text-white/60 transition hover:bg-white/10 hover:text-white focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden border-t border-white/10">
        <div class="space-y-0.5 pb-3 pt-2 px-3">
            @if (auth()->user()->rol === 'coordinacion')
                <a href="{{ route('dashboard') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('dashboard') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Dashboard
                </a>
                <a href="{{ route('ciclos.index') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('ciclos.*') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Ciclos
                </a>
                <a href="{{ route('tutores.index') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('tutores.*') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Tutores
                </a>
                <a href="{{ route('materias.index') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('materias.*') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Materias
                </a>
                <a href="{{ route('carga-academica.create') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('carga-academica.*') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Carga académica
                </a>
                <a href="{{ route('propuestas.index') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('propuestas.*') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Propuestas
                </a>
                <span class="block border-l-2 border-transparent px-3 py-2 text-sm font-medium text-white/30">Periodos</span>
            @endif

            @if (auth()->user()->rol === 'tutor')
                <a href="{{ route('mis-asignaciones') }}"
                   class="block border-l-2 px-3 py-2 text-sm font-medium transition
                          {{ request()->routeIs('mis-asignaciones') ? 'border-white bg-white/10 text-white' : 'border-transparent text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white' }}">
                    Mis asignaciones
                </a>
                <span class="block border-l-2 border-transparent px-3 py-2 text-sm font-medium text-white/30">Casos</span>
                <span class="block border-l-2 border-transparent px-3 py-2 text-sm font-medium text-white/30">Consolidado</span>
            @endif
        </div>

        <div class="border-t border-white/10 pb-3 pt-4">
            <div class="flex items-center gap-3 px-4">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-white/20 text-sm font-bold text-white ring-2 ring-white/20">
                    {{ strtoupper(substr(auth()->user()->nombre, 0, 1)) }}
                </div>
                <div>
                    <div class="text-sm font-semibold text-white">{{ auth()->user()->nombre }}</div>
                    <div class="text-xs text-white/50">{{ auth()->user()->correo }}</div>
                    <div class="mt-0.5 text-[10px] font-semibold uppercase tracking-wide text-white/40">
                        {{ auth()->user()->rol === 'coordinacion' ? 'Coordinación' : 'Tutor' }}
                    </div>
                </div>
            </div>

            <div class="mt-3 space-y-0.5 px-3">
                <a href="{{ route('profile.edit') }}"
                   class="block border-l-2 border-transparent px-3 py-2 text-sm font-medium text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white transition">
                    Perfil
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            onclick="event.preventDefault(); this.closest('form').submit();"
                            class="block w-full text-left border-l-2 border-transparent px-3 py-2 text-sm font-medium text-white/65 hover:bg-white/10 hover:border-white/40 hover:text-white transition">
                        Cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
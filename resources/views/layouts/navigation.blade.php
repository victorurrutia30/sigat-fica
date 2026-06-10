<nav x-data="{ open: false }" class="border-b border-utec-gray-medium bg-white">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 justify-between">
            <div class="flex">
                <div class="flex shrink-0 items-center">
                    <a href="{{ auth()->user()->rol === 'coordinacion' ? route('dashboard') : route('mis-asignaciones') }}">
                        <div class="flex items-center gap-3">
                            <x-application-logo class="block h-9 w-auto fill-current text-utec-primary" />
                            <div class="hidden leading-tight sm:block">
                                <p class="text-sm font-bold text-utec-primary">SIGAT-FICA</p>
                                <p class="text-xs text-gray-500">Programa de Tutores</p>
                            </div>
                        </div>
                    </a>
                </div>

                <div class="hidden space-x-7 sm:-my-px sm:ms-10 sm:flex">
                    @if (auth()->user()->rol === 'coordinacion')
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        Dashboard
                    </x-nav-link>

                    <x-nav-link :href="route('ciclos.index')" :active="request()->routeIs('ciclos.*')">
                        Ciclos
                    </x-nav-link>

                    <x-nav-link :href="route('tutores.index')" :active="request()->routeIs('tutores.*')">
                        Tutores
                    </x-nav-link>

                    <x-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                        Materias
                    </x-nav-link>

                    <x-nav-link :href="route('carga-academica.create')" :active="request()->routeIs('carga-academica.*')">
                        Carga académica
                    </x-nav-link>

                    <x-nav-link :href="route('propuestas.index')" :active="request()->routeIs('propuestas.*')">
                        Propuestas
                    </x-nav-link>

                    <x-nav-link :href="route('periodos.index')" :active="request()->routeIs('periodos.*')">
                        Periodos
                    </x-nav-link>

                    <x-nav-link :href="route('causas.index')" :active="request()->routeIs('causas.*')">
                        Causas
                    </x-nav-link>

                    <x-nav-link :href="route('consolidados.index')" :active="request()->routeIs('consolidados.*')">
                        Consolidados
                    </x-nav-link>

                    <x-nav-link :href="route('tablero.index')" :active="request()->routeIs('tablero.*')">
                        Tablero
                    </x-nav-link>

                    @endif

                    @if (auth()->user()->rol === 'tutor')
                    <x-nav-link :href="route('mis-asignaciones')" :active="request()->routeIs('mis-asignaciones')">
                        Mis asignaciones
                    </x-nav-link>

                    <x-nav-link :href="route('consolidado.index')" :active="request()->routeIs('consolidado.*')">
                        Consolidado
                    </x-nav-link>

                    <x-nav-link :href="route('casos.index')" :active="request()->routeIs('casos.*')">
                        Casos
                    </x-nav-link>


                    @endif
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center rounded-md border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-600 transition hover:text-utec-primary focus:outline-none">
                            <div class="text-left">
                                <div class="font-semibold">{{ auth()->user()->nombre }}</div>
                                <div class="text-xs text-gray-400">
                                    {{ auth()->user()->rol === 'coordinacion' ? 'Coordinación' : 'Tutor' }}
                                </div>
                            </div>

                            <div class="ms-2">
                                <svg class="h-4 w-4 fill-current" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
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
                    class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 transition hover:bg-utec-primary-soft hover:text-utec-primary focus:bg-utec-primary-soft focus:text-utec-primary focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{ 'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{ 'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <div :class="{ 'block': open, 'hidden': ! open }" class="hidden sm:hidden">
        <div class="space-y-1 pb-3 pt-2">
            @if (auth()->user()->rol === 'coordinacion')
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('ciclos.index')" :active="request()->routeIs('ciclos.*')">
                Ciclos
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tutores.index')" :active="request()->routeIs('tutores.*')">
                Tutores
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('materias.index')" :active="request()->routeIs('materias.*')">
                Materias
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('carga-academica.create')" :active="request()->routeIs('carga-academica.*')">
                Carga académica
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('propuestas.index')" :active="request()->routeIs('propuestas.*')">
                Propuestas
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('periodos.index')" :active="request()->routeIs('periodos.*')">
                Periodos
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('causas.index')" :active="request()->routeIs('causas.*')">
                Causas
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('consolidados.index')" :active="request()->routeIs('consolidados.*')">
                Consolidados
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('tablero.index')" :active="request()->routeIs('tablero.*')">
                Tablero
            </x-responsive-nav-link>


            @endif

            @if (auth()->user()->rol === 'tutor')
            <x-responsive-nav-link :href="route('mis-asignaciones')" :active="request()->routeIs('mis-asignaciones')">
                Mis asignaciones
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('consolidado.index')" :active="request()->routeIs('consolidado.*')">
                Consolidado
            </x-responsive-nav-link>

            <x-responsive-nav-link :href="route('casos.index')" :active="request()->routeIs('casos.*')">
                Casos
            </x-responsive-nav-link>


            @endif
        </div>

        <div class="border-t border-utec-gray-medium pb-1 pt-4">
            <div class="px-4">
                <div class="text-base font-medium text-utec-gray-dark">{{ auth()->user()->nombre }}</div>
                <div class="text-sm font-medium text-gray-500">{{ auth()->user()->correo }}</div>
                <div class="mt-1 text-xs font-medium uppercase tracking-wide text-utec-primary-light">
                    {{ auth()->user()->rol === 'coordinacion' ? 'Coordinación' : 'Tutor' }}
                </div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    Perfil
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link
                        :href="route('logout')"
                        onclick="event.preventDefault(); this.closest('form').submit();">
                        Cerrar sesión
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
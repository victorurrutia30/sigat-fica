<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-bold text-utec-primary">
                    Mi consolidado
                </h2>
                <p class="text-sm text-gray-500">
                    Revisión y entrega del consolidado del periodo activo.
                </p>
            </div>

            <a href="{{ route('casos.index') }}" class="btn-secondary">
                Ver casos
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            @if(session('success'))
            <div class="alert-success mb-4">
                {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="alert-error mb-4">
                {{ session('error') }}
            </div>
            @endif

            @if($mensajeBloqueo)
            <div class="alert-warning">
                {{ $mensajeBloqueo }}
            </div>
            @else
            <div class="mb-6 grid gap-4 md:grid-cols-4">
                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Periodo activo</p>
                        <p class="mt-2 text-lg font-bold text-utec-primary">
                            {{ $periodo->nombre }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Límite:
                            {{ $periodo->fecha_limite_consolidado->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Estado de entrega</p>
                        <p class="mt-2">
                            @if($consolidado->estado_entrega === 'entregado')
                            <span class="badge-success">Entregado</span>
                            @elseif($consolidado->estado_entrega === 'con_observaciones')
                            <span class="badge-warning">Con observaciones</span>
                            @else
                            <span class="badge-muted">Pendiente</span>
                            @endif
                        </p>

                        @if($consolidado->entregado_en)
                        <p class="mt-2 text-sm text-gray-500">
                            {{ $consolidado->entregado_en->format('d/m/Y H:i') }}
                        </p>
                        @endif
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Casos cerrados</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['cerrados'] }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            de {{ $diagnostico['total'] }} registrados
                        </p>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <p class="text-xs text-gray-500">Casos incompletos</p>
                        <p class="mt-2 text-3xl font-bold text-utec-primary">
                            {{ $diagnostico['incompletos'] }}
                        </p>
                        <p class="mt-1 text-sm text-gray-500">
                            Deben quedar en 0 para entregar.
                        </p>
                    </div>
                </div>
            </div>

            @if($consolidado->observaciones_coord)
            <div class="alert-info mb-6">
                <span class="font-semibold">Observaciones de Coordinación:</span>
                {{ $consolidado->observaciones_coord }}
            </div>
            @endif

            @if($diagnostico['incompletos'] > 0)
            <div class="alert-warning mb-6">
                No puedes entregar todavía. Revisa los casos incompletos indicados abajo.
            </div>
            @endif

            <div class="mb-6 grid gap-6 lg:grid-cols-3">
                <div class="card lg:col-span-2">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Estado de casos
                        </h3>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-utec-gray-medium">
                            <thead>
                                <tr>
                                    <th class="th-utec">Estudiante</th>
                                    <th class="th-utec">Sección</th>
                                    <th class="th-utec">Causa</th>
                                    <th class="th-utec">Resultado</th>
                                    <th class="th-utec">Gestiones</th>
                                    <th class="th-utec">Estado</th>
                                    <th class="th-utec text-right">Acciones</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-utec-gray-medium bg-white">
                                @forelse($casos as $caso)
                                <tr class="hover:bg-utec-primary-soft">
                                    <td class="td-utec">
                                        <div class="font-semibold">
                                            {{ $caso->estudiante?->nombre_completo }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $caso->estudiante?->carne }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        <div class="font-semibold">
                                            {{ $caso->seccion?->materia?->codigo }}
                                            —
                                            {{ $caso->seccion?->numero_seccion }}
                                        </div>
                                        <div class="text-xs text-gray-500">
                                            {{ $caso->seccion?->materia?->nombre }}
                                        </div>
                                    </td>

                                    <td class="td-utec">
                                        @if($caso->causa)
                                        {{ $caso->causa->nombre }}
                                        @else
                                        <span class="badge-warning">Falta</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        @if($caso->resultado_final)
                                        {{ ucfirst($caso->resultado_final) }}
                                        @else
                                        <span class="badge-warning">Falta</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        @if($caso->gestiones->isNotEmpty())
                                        {{ $caso->gestiones->count() }}
                                        @else
                                        <span class="badge-warning">Falta</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        @if($caso->cerrado && $caso->causa_id && $caso->resultado_final && $caso->gestiones->isNotEmpty())
                                        <span class="badge-success">Completo</span>
                                        @else
                                        <span class="badge-warning">Incompleto</span>
                                        @endif
                                    </td>

                                    <td class="td-utec">
                                        <div class="flex justify-end">
                                            <a href="{{ route('casos.show', $caso) }}" class="link-utec">
                                                Ver caso
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                        No tienes casos registrados para este periodo.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h3 class="text-base font-semibold text-utec-gray-dark">
                            Entrega del consolidado
                        </h3>
                    </div>

                    <div class="card-body">
                        @if($consolidado->estado_entrega === 'entregado')
                        <div class="alert-success">
                            El consolidado ya fue entregado.
                        </div>

                        <dl class="mt-5 space-y-3 text-sm">
                            <div>
                                <dt class="font-medium text-gray-500">Fecha de entrega</dt>
                                <dd class="text-utec-gray-dark">
                                    {{ $consolidado->entregado_en?->format('d/m/Y H:i') }}
                                </dd>
                            </div>

                            <div>
                                <dt class="font-medium text-gray-500">Constancia sin casos</dt>
                                <dd class="text-utec-gray-dark">
                                    {{ $consolidado->sin_casos ? 'Sí' : 'No' }}
                                </dd>
                            </div>
                        </dl>
                        @else
                        <form method="POST" action="{{ route('consolidado.entregar') }}">
                            @csrf
                            @method('PATCH')

                            @if($diagnostico['total'] === 0)
                            <div class="mb-4 rounded-md border border-orange-200 bg-orange-50 p-4 text-sm text-orange-800">
                                No hay casos registrados. Para entregar, debes confirmar expresamente que no hubo estudiantes no evaluados.
                            </div>

                            <label class="mb-4 flex items-start gap-2 text-sm text-utec-gray-dark">
                                <input type="hidden" name="confirmar_sin_casos" value="0">
                                <input
                                    type="checkbox"
                                    name="confirmar_sin_casos"
                                    value="1"
                                    class="mt-1 rounded border-utec-gray-medium text-utec-primary focus:ring-utec-primary-light"
                                    @checked(old('confirmar_sin_casos'))>
                                <span>
                                    Confirmo que no hubo estudiantes no evaluados en mis secciones asignadas para este periodo.
                                </span>
                            </label>

                            @error('confirmar_sin_casos')
                            <p class="form-error mb-4">{{ $message }}</p>
                            @enderror
                            @else
                            <input type="hidden" name="confirmar_sin_casos" value="0">
                            @endif

                            @if($diagnostico['incompletos'] > 0)
                            <div class="mb-4 rounded-md border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                                Debes completar todos los casos antes de entregar.
                            </div>
                            @else
                            <div class="mb-4 rounded-md border border-green-200 bg-green-50 p-4 text-sm text-green-800">
                                El consolidado está listo para entrega.
                            </div>
                            @endif

                            <button
                                type="submit"
                                class="btn-primary w-full"
                                onclick="return confirm('¿Seguro que deseas entregar el consolidado?')">
                                Entregar consolidado
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>

            @if($diagnostico['incompletos'] > 0)
            <div class="card">
                <div class="card-header">
                    <h3 class="text-base font-semibold text-utec-gray-dark">
                        Detalle de campos faltantes
                    </h3>
                </div>

                <div class="card-body">
                    <div class="space-y-3">
                        @foreach($diagnostico['detalle_incompletos'] as $item)
                        <div class="rounded-lg border border-red-200 bg-red-50 p-4 text-sm text-red-800">
                            <div class="font-semibold">
                                {{ $item['caso']->estudiante?->nombre_completo }}
                                —
                                {{ $item['caso']->seccion?->materia?->codigo }}
                                /
                                Sección {{ $item['caso']->seccion?->numero_seccion }}
                            </div>

                            <div class="mt-1">
                                Falta:
                                {{ implode(', ', $item['faltantes']) }}.
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            @endif
        </div>
    </div>
</x-app-layout>
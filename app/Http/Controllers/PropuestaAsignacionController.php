<?php

namespace App\Http\Controllers;

use App\Http\Requests\GuardarItemPropuestaRequest;
use App\Http\Requests\RegistrarRespuestaDecanoRequest;
use App\Models\ItemPropuesta;
use App\Models\PropuestaAsignacion;
use App\Services\PropuestaAsignacionService;
use App\Exports\AsignacionTutoresExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropuestaAsignacionController extends Controller
{
    public function index(
        Request $request,
        PropuestaAsignacionService $propuestaService
    ): View|RedirectResponse {
        try {
            $propuesta = $propuestaService->obtenerOCrearParaCicloActivo($request->user()->id);
            $ciclo = $propuesta->ciclo;
            $seccionesCandidatas = $propuestaService->seccionesCandidatas($ciclo);
            $tutores = $propuestaService->tutoresElegibles();

            return view('coordinacion.propuestas.index', [
                'propuesta' => $propuesta,
                'ciclo' => $ciclo,
                'seccionesCandidatas' => $seccionesCandidatas,
                'tutores' => $tutores,
                'itemsPorSeccion' => $propuesta->items->keyBy('seccion_id'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return redirect()
                ->route('ciclos.index')
                ->withErrors($exception->errors())
                ->with('error', 'Debe existir un ciclo activo antes de crear la propuesta de asignación.');
        }
    }

    public function store(
        GuardarItemPropuestaRequest $request,
        PropuestaAsignacionService $propuestaService
    ): RedirectResponse {
        $propuesta = $propuestaService->obtenerOCrearParaCicloActivo($request->user()->id);

        $propuestaService->asignarTutor(
            propuesta: $propuesta,
            seccionId: (int) $request->validated('seccion_id'),
            tutorId: (int) $request->validated('tutor_id'),
            observaciones: $request->validated('observaciones'),
            aula: $request->validated('aula'),
            usuarioId: $request->user()->id
        );

        return redirect()
            ->route('propuestas.index')
            ->with('success', 'Asignación guardada correctamente.');
    }

    public function destroy(
        ItemPropuesta $itemPropuesta,
        Request $request,
        PropuestaAsignacionService $propuestaService
    ): RedirectResponse {
        if ($request->user()?->rol !== 'coordinacion') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $propuestaService->quitarItem(
            item: $itemPropuesta,
            usuarioId: $request->user()->id
        );

        return redirect()
            ->route('propuestas.index')
            ->with('success', 'Asignación eliminada correctamente.');
    }

    public function registrarRespuestaDecano(
        RegistrarRespuestaDecanoRequest $request,
        PropuestaAsignacion $propuesta,
        PropuestaAsignacionService $propuestaService
    ): RedirectResponse {
        $propuestaService->registrarRespuestaDecano(
            propuesta: $propuesta,
            estadoAprobacion: $request->validated('estado_aprobacion'),
            observaciones: $request->validated('observaciones_decano'),
            fechaRespuesta: $request->validated('fecha_respuesta_decano'),
            usuarioId: $request->user()->id
        );

        return redirect()
            ->route('propuestas.index')
            ->with('success', 'Respuesta del Decano registrada correctamente.');
    }

    public function publicar(
        PropuestaAsignacion $propuesta,
        Request $request,
        PropuestaAsignacionService $propuestaService
    ): RedirectResponse {
        if ($request->user()?->rol !== 'coordinacion') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        $propuestaService->publicar(
            propuesta: $propuesta,
            usuarioId: $request->user()->id
        );

        return redirect()
            ->route('propuestas.index')
            ->with('success', 'Propuesta publicada correctamente. Los tutores ya pueden ver sus asignaciones.');
    }

    public function exportar(
        Request $request,
        PropuestaAsignacionService $propuestaService
    ): BinaryFileResponse|RedirectResponse {
        if ($request->user()?->rol !== 'coordinacion') {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        try {
            $propuesta = $propuestaService->obtenerOCrearParaCicloActivo($request->user()->id);
        } catch (\Illuminate\Validation\ValidationException $exception) {
            return redirect()
                ->route('ciclos.index')
                ->withErrors($exception->errors())
                ->with('error', 'Debe existir un ciclo activo antes de exportar la propuesta.');
        }

        if ($propuesta->items->isEmpty()) {
            return redirect()
                ->route('propuestas.index')
                ->with('error', 'No se puede exportar una propuesta sin asignaciones.');
        }

        $propuesta->forceFill([
            'enviado_en' => now(),
        ])->save();

        $nombreArchivo = 'asignacion-tutores-'
            . ($propuesta->ciclo?->nombre ?? 'ciclo')
            . '-'
            . now()->format('Ymd-His')
            . '.xlsx';

        return Excel::download(
            new AsignacionTutoresExport($propuesta),
            $nombreArchivo
        );
    }
}

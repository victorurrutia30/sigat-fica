<?php

namespace App\Http\Controllers;

use App\Http\Requests\GestionCasoRequest;
use App\Models\CasoSeguimiento;
use App\Models\GestionCaso;
use App\Services\CasoSeguimientoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GestionCasoController extends Controller
{
    public function create(
        CasoSeguimiento $casoSeguimiento,
        Request $request,
        CasoSeguimientoService $casoService
    ): View {
        $casoService->validarAccesoTutor($casoSeguimiento, $request->user());

        if ($casoSeguimiento->cerrado) {
            abort(403, 'No se pueden agregar gestiones a un caso cerrado.');
        }

        $casoSeguimiento->load([
            'periodoEvaluacion.ciclo',
            'seccion.materia',
            'estudiante',
            'tutor',
            'causa',
            'gestiones',
        ]);

        return view('tutor.gestiones.create', [
            'caso' => $casoSeguimiento,
        ]);
    }

    public function store(
        GestionCasoRequest $request,
        CasoSeguimiento $casoSeguimiento,
        CasoSeguimientoService $casoService
    ): RedirectResponse {
        $casoService->validarAccesoTutor($casoSeguimiento, $request->user());

        if ($casoSeguimiento->cerrado) {
            return redirect()
                ->route('casos.show', $casoSeguimiento)
                ->with('error', 'No se pueden agregar gestiones a un caso cerrado.');
        }

        $datos = $request->validated();

        GestionCaso::create([
            'caso_seguimiento_id' => $casoSeguimiento->id,
            'registrado_por' => $request->user()->id,
            'fecha_gestion' => $datos['fecha_gestion'],
            'medio_contacto' => $datos['medio_contacto'],
            'accion_realizada' => $datos['accion_realizada'],
            'resultado' => $datos['resultado'] ?? null,
        ]);

        return redirect()
            ->route('casos.show', $casoSeguimiento)
            ->with('success', 'Gestión registrada correctamente.');
    }
}

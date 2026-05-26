<?php

namespace App\Http\Controllers;

use App\Models\ItemPropuesta;
use App\Models\Tutor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MisAsignacionesController extends Controller
{
    public function index(Request $request): View
    {
        $usuario = $request->user();

        $tutor = Tutor::query()
            ->where('usuario_id', $usuario->id)
            ->first();

        $asignaciones = collect();

        if ($tutor) {
            $asignaciones = ItemPropuesta::query()
                ->with([
                    'propuestaAsignacion.ciclo',
                    'seccion.materia',
                    'seccion.horarios',
                    'tutor',
                ])
                ->where('tutor_id', $tutor->id)
                ->whereHas('propuestaAsignacion', function ($query) {
                    $query->where('publicado', true)
                        ->whereHas('ciclo', function ($cicloQuery) {
                            $cicloQuery->where('activo', true);
                        });
                })
                ->get()
                ->sortBy([
                    fn($a, $b) => strcmp(
                        $a->seccion?->materia?->nombre ?? '',
                        $b->seccion?->materia?->nombre ?? ''
                    ),
                    fn($a, $b) => strcmp(
                        (string) ($a->seccion?->numero_seccion ?? ''),
                        (string) ($b->seccion?->numero_seccion ?? '')
                    ),
                ])
                ->values();
        }

        return view('tutor.mis-asignaciones', [
            'tutor' => $tutor,
            'asignaciones' => $asignaciones,
        ]);
    }
}

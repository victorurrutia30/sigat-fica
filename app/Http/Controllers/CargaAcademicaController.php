<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImportarCargaAcademicaRequest;
use App\Imports\CargaAcademicaImport;
use App\Models\Ciclo;
use App\Models\ImportacionCargaAcademica;
use App\Services\CargaAcademica\ProcesarCargaAcademicaService;
use App\Services\CargaAcademica\ResultadoCargaAcademica;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class CargaAcademicaController extends Controller
{
    public function create(): View
    {
        $ciclos = Ciclo::query()
            ->orderByDesc('activo')
            ->orderByDesc('anio')
            ->orderByDesc('periodo')
            ->get();

        $ultimasImportaciones = ImportacionCargaAcademica::query()
            ->with(['ciclo', 'usuario'])
            ->latest()
            ->take(5)
            ->get();

        return view('coordinacion.carga-academica.importar', compact(
            'ciclos',
            'ultimasImportaciones'
        ));
    }

    public function store(ImportarCargaAcademicaRequest $request): RedirectResponse
    {
        $archivo = $request->file('archivo');
        $ciclo = Ciclo::findOrFail($request->integer('ciclo_id'));

        $resultado = new ResultadoCargaAcademica();

        $importacion = ImportacionCargaAcademica::create([
            'ciclo_id' => $ciclo->id,
            'usuario_id' => Auth::id(),
            'nombre_archivo' => $archivo->getClientOriginalName(),
            'hash_archivo' => hash_file('sha256', $archivo->getRealPath()),
            'estado' => 'fallido',
        ]);

        try {
            $import = new CargaAcademicaImport(
                ciclo: $ciclo,
                resultado: $resultado,
                procesador: app(ProcesarCargaAcademicaService::class)
            );

            Excel::import($import, $archivo);

            if ($resultado->contadores()['hojas_procesadas'] === 0) {
                $resultado->registrarError(
                    hoja: 'Archivo',
                    fila: 'General',
                    mensaje: 'No se procesó ninguna hoja válida. Verifique que el archivo contenga las hojas VIRTUALES Y SETACO, INFO o CCAA.'
                );
            }
        } catch (\Throwable $e) {
            $resultado->registrarError(
                hoja: 'Archivo',
                fila: 'General',
                mensaje: 'No se pudo procesar el archivo Excel. Verifique que el formato corresponda a la carga académica institucional.',
                datos: [
                    'detalle_tecnico' => $e->getMessage(),
                ]
            );
        }

        $this->actualizarImportacion($importacion, $resultado);

        $mensaje = match ($resultado->estado()) {
            'procesado' => 'Carga académica importada correctamente.',
            'procesado_con_observaciones' => 'Carga académica procesada con observaciones. Revise el resumen.',
            default => 'No se pudo completar la importación de carga académica.',
        };

        return redirect()
            ->route('carga-academica.create')
            ->with($resultado->estado() === 'fallido' ? 'error' : 'success', $mensaje)
            ->with('importacion_id', $importacion->id)
            ->with('resumen_importacion', $resultado->resumen())
            ->with('errores_importacion', $resultado->errores());
    }

    private function actualizarImportacion(
        ImportacionCargaAcademica $importacion,
        ResultadoCargaAcademica $resultado
    ): void {
        $contadores = $resultado->contadores();

        $importacion->update([
            'hojas_procesadas' => $contadores['hojas_procesadas'],
            'filas_leidas' => $contadores['filas_leidas'],
            'filas_importadas' => $contadores['filas_importadas'],
            'filas_ignoradas' => $contadores['filas_ignoradas'],
            'filas_error' => $contadores['filas_error'],
            'materias_creadas' => $contadores['materias_creadas'],
            'materias_actualizadas' => $contadores['materias_actualizadas'],
            'secciones_creadas' => $contadores['secciones_creadas'],
            'secciones_actualizadas' => $contadores['secciones_actualizadas'],
            'horarios_creados' => $contadores['horarios_creados'],
            'estado' => $resultado->estado(),
            'resumen_json' => $resultado->resumen(),
            'errores_json' => $resultado->errores(),
        ]);
    }
}

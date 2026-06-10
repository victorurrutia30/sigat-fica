<?php

namespace App\Http\Controllers;

use App\Http\Requests\DocenteDetectadoTutorRequest;
use App\Models\Ciclo;
use App\Models\Seccion;
use App\Models\Tutor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DocenteDetectadoController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $ciclo = Ciclo::query()
            ->where('activo', true)
            ->first();

        if (! $ciclo) {
            return redirect()
                ->route('ciclos.index')
                ->with('error', 'Activa un ciclo académico antes de revisar docentes detectados.');
        }

        $busqueda = request('busqueda');
        $categoria = request('categoria');
        $estado = request('estado');

        $docentes = DB::table('secciones')
            ->select([
                'codigo_docente_titular',
                DB::raw('MIN(nombre_titular) as nombre_titular'),
                DB::raw('MIN(correo_titular) as correo_titular'),
                DB::raw('MIN(categoria_docente_titular) as categoria_docente_titular'),
                DB::raw('COUNT(*) as total_secciones'),
                DB::raw('COUNT(DISTINCT materia_id) as total_materias'),
            ])
            ->where('ciclo_id', $ciclo->id)
            ->whereNotNull('codigo_docente_titular')
            ->where('codigo_docente_titular', '!=', '')
            ->when($busqueda, function ($query) use ($busqueda) {
                $query->where(function ($subquery) use ($busqueda) {
                    $subquery->where('codigo_docente_titular', 'like', "%{$busqueda}%")
                        ->orWhere('nombre_titular', 'like', "%{$busqueda}%")
                        ->orWhere('correo_titular', 'like', "%{$busqueda}%")
                        ->orWhere('categoria_docente_titular', 'like', "%{$busqueda}%");
                });
            })
            ->when($categoria, function ($query) use ($categoria) {
                $query->where('categoria_docente_titular', $categoria);
            })
            ->when($estado === 'registrados', function ($query) {
                $query->whereExists(function ($subquery) {
                    $subquery->selectRaw('1')
                        ->from('tutores')
                        ->whereColumn('tutores.codigo_empleado', 'secciones.codigo_docente_titular');
                });
            })
            ->when($estado === 'no_registrados', function ($query) {
                $query->whereNotExists(function ($subquery) {
                    $subquery->selectRaw('1')
                        ->from('tutores')
                        ->whereColumn('tutores.codigo_empleado', 'secciones.codigo_docente_titular');
                });
            })
            ->groupBy('codigo_docente_titular')
            ->orderByRaw('MIN(nombre_titular)')
            ->paginate(15)
            ->withQueryString();

        $codigos = $docentes->getCollection()
            ->pluck('codigo_docente_titular')
            ->filter()
            ->values();

        $tutoresPorCodigo = Tutor::withTrashed()
            ->whereIn('codigo_empleado', $codigos)
            ->get()
            ->keyBy('codigo_empleado');

        $seccionesPorDocente = Seccion::query()
            ->with(['materia:id,codigo,nombre', 'horarios'])
            ->where('ciclo_id', $ciclo->id)
            ->whereIn('codigo_docente_titular', $codigos)
            ->orderBy('codigo_docente_titular')
            ->orderBy('materia_id')
            ->orderBy('numero_seccion')
            ->get([
                'id',
                'materia_id',
                'numero_seccion',
                'modalidad',
                'aula',
                'codigo_docente_titular',
                'requiere_tutor',
            ])
            ->groupBy('codigo_docente_titular');

        $categorias = Seccion::query()
            ->where('ciclo_id', $ciclo->id)
            ->whereNotNull('categoria_docente_titular')
            ->where('categoria_docente_titular', '!=', '')
            ->select('categoria_docente_titular')
            ->distinct()
            ->orderBy('categoria_docente_titular')
            ->pluck('categoria_docente_titular');

        return view('coordinacion.docentes-detectados.index', compact(
            'ciclo',
            'docentes',
            'tutoresPorCodigo',
            'seccionesPorDocente',
            'categorias',
            'busqueda',
            'categoria',
            'estado'
        ));
    }

    public function crearTutor(DocenteDetectadoTutorRequest $request, string $codigoDocente): RedirectResponse
    {
        $ciclo = Ciclo::query()
            ->where('activo', true)
            ->firstOrFail();

        $codigoDocente = strtoupper(trim((string) $request->validated('codigo_docente')));

        $seccionDocente = Seccion::query()
            ->where('ciclo_id', $ciclo->id)
            ->where('codigo_docente_titular', $codigoDocente)
            ->whereNotNull('nombre_titular')
            ->orderBy('id')
            ->firstOrFail();

        $correo = strtolower(trim((string) $seccionDocente->correo_titular));

        if ($correo === '') {
            return redirect()
                ->route('docentes-detectados.index')
                ->with('error', 'No se puede crear el tutor porque el docente detectado no tiene correo institucional registrado.');
        }

        $correoYaExiste = Tutor::withTrashed()
            ->where('correo_institucional', $correo)
            ->exists();

        if ($correoYaExiste) {
            return redirect()
                ->route('docentes-detectados.index')
                ->with('error', 'Ya existe un tutor con el correo institucional del docente detectado.');
        }

        $categoria = strtoupper(trim((string) $seccionDocente->categoria_docente_titular));
        $esDtc = $categoria === 'DTC';

        Tutor::create([
            'codigo_empleado' => $codigoDocente,
            'nombre_completo' => trim((string) $seccionDocente->nombre_titular),
            'correo_institucional' => $correo,
            'departamento' => null,
            'categoria_docente' => $categoria ?: null,
            'fecha_contratacion' => null,
            'tiempo_completo' => $esDtc,
            'habilitado_para_tutorias' => $esDtc,
            'es_excepcion_tutoria' => false,
            'motivo_excepcion_tutoria' => null,
            'origen_registro' => 'carga_academica',
            'activo' => true,
        ]);

        if (! $esDtc) {
            return redirect()
                ->route('docentes-detectados.index')
                ->with('success', 'Tutor creado desde carga académica. Como no es DTC, queda sin habilitación para tutorías hasta que Coordinación registre una excepción autorizada.');
        }

        return redirect()
            ->route('docentes-detectados.index')
            ->with('success', 'Tutor DTC creado correctamente desde carga académica.');
    }
}

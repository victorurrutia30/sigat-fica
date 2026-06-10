<?php

use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\CicloController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\MisAsignacionesController;
use App\Http\Controllers\PeriodoEvaluacionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropuestaAsignacionController;
use App\Http\Controllers\TutorController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CausaController;
use App\Http\Controllers\CasoSeguimientoController;
use App\Http\Controllers\GestionCasoController;
use App\Http\Controllers\ConsolidadoController;
use App\Http\Controllers\TableroCumplimientoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\NotificacionController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\DocenteDetectadoController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'rol:coordinacion'])
    ->name('dashboard');

Route::middleware(['auth', 'verified', 'rol:coordinacion'])->group(function () {

    Route::get('tablero-cumplimiento', [TableroCumplimientoController::class, 'index'])
        ->name('tablero.index');

    Route::resource('ciclos', CicloController::class);

    Route::resource('materias', MateriaController::class);

    Route::get('docentes-detectados', [DocenteDetectadoController::class, 'index'])
        ->name('docentes-detectados.index');

    Route::post('docentes-detectados/{codigoDocente}/crear-tutor', [DocenteDetectadoController::class, 'crearTutor'])
        ->name('docentes-detectados.crear-tutor');

    Route::patch('tutores/{tutor}/reactivar', [TutorController::class, 'reactivar'])
        ->withTrashed()
        ->name('tutores.reactivar');

    Route::resource('tutores', TutorController::class)
        ->parameters(['tutores' => 'tutor']);

    Route::resource('usuarios', UsuarioController::class)
        ->except(['show'])
        ->parameters(['usuarios' => 'usuario']);

    Route::resource('causas', CausaController::class)
        ->parameters(['causas' => 'causa']);

    Route::resource('periodos', PeriodoEvaluacionController::class)
        ->parameters(['periodos' => 'periodoEvaluacion']);

    Route::get('consolidados', [ConsolidadoController::class, 'coordinacionIndex'])
        ->name('consolidados.index');

    Route::get('consolidados/periodos/{periodoEvaluacion}/exportar-institucional', [ConsolidadoController::class, 'exportarPeriodoInstitucional'])
        ->name('consolidados.periodos.exportar-institucional');

    Route::get('consolidados/{consolidado}', [ConsolidadoController::class, 'coordinacionShow'])
        ->name('consolidados.show');

    Route::patch('consolidados/{consolidado}/observacion', [ConsolidadoController::class, 'guardarObservacion'])
        ->name('consolidados.observacion');

    Route::get('carga-academica/importar', [CargaAcademicaController::class, 'create'])
        ->name('carga-academica.create');

    Route::post('carga-academica/importar', [CargaAcademicaController::class, 'store'])
        ->name('carga-academica.store');

    Route::get('propuestas', [PropuestaAsignacionController::class, 'index'])
        ->name('propuestas.index');

    Route::get('propuestas/exportar', [PropuestaAsignacionController::class, 'exportar'])
        ->name('propuestas.exportar');

    Route::post('propuestas/items', [PropuestaAsignacionController::class, 'store'])
        ->name('propuestas.items.store');

    Route::delete('propuestas/items/{itemPropuesta}', [PropuestaAsignacionController::class, 'destroy'])
        ->name('propuestas.items.destroy');

    Route::patch('propuestas/{propuesta}/respuesta-decano', [PropuestaAsignacionController::class, 'registrarRespuestaDecano'])
        ->name('propuestas.respuesta-decano');

    Route::patch('propuestas/{propuesta}/publicar', [PropuestaAsignacionController::class, 'publicar'])
        ->name('propuestas.publicar');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('notificaciones', [NotificacionController::class, 'index'])
        ->name('notificaciones.index');

    Route::patch('notificaciones/leer-todas', [NotificacionController::class, 'marcarTodasLeidas'])
        ->name('notificaciones.marcar-todas-leidas');

    Route::patch('notificaciones/{notificacion}/leer', [NotificacionController::class, 'marcarLeida'])
        ->name('notificaciones.marcar-leida');
});

Route::get('/mis-asignaciones', [MisAsignacionesController::class, 'index'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('mis-asignaciones');

Route::resource('casos', CasoSeguimientoController::class)
    ->only(['index', 'create', 'store', 'show'])
    ->parameters(['casos' => 'casoSeguimiento'])
    ->middleware(['auth', 'rol:tutor']);

Route::get('casos/{casoSeguimiento}/cierre', [CasoSeguimientoController::class, 'cierre'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('casos.cierre');

Route::patch('casos/{casoSeguimiento}/cerrar', [CasoSeguimientoController::class, 'cerrar'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('casos.cerrar');

Route::get('casos/{casoSeguimiento}/gestiones/create', [GestionCasoController::class, 'create'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('gestiones.create');

Route::post('casos/{casoSeguimiento}/gestiones', [GestionCasoController::class, 'store'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('gestiones.store');

Route::get('consolidado', [ConsolidadoController::class, 'index'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('consolidado.index');

Route::patch('consolidado/entregar', [ConsolidadoController::class, 'entregar'])
    ->middleware(['auth', 'rol:tutor'])
    ->name('consolidado.entregar');

require __DIR__ . '/auth.php';

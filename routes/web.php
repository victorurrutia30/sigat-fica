<?php

use App\Http\Controllers\CargaAcademicaController;
use App\Http\Controllers\CicloController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TutorController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified', 'rol:coordinacion'])->name('dashboard');

Route::middleware(['auth', 'verified', 'rol:coordinacion'])->group(function () {
    Route::resource('ciclos', CicloController::class);
    Route::resource('materias', MateriaController::class);
    Route::resource('tutores', TutorController::class)
        ->parameters(['tutores' => 'tutor']);
    Route::get('carga-academica/importar', [CargaAcademicaController::class, 'create'])
        ->name('carga-academica.create');
    Route::post('carga-academica/importar', [CargaAcademicaController::class, 'store'])
        ->name('carga-academica.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/mis-asignaciones', function () {
    return view('tutor.mis-asignaciones');
})->middleware(['auth', 'rol:tutor'])->name('mis-asignaciones');

require __DIR__ . '/auth.php';

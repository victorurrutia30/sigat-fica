<?php

use App\Http\Controllers\CicloController;
use App\Http\Controllers\MateriaController;
use App\Http\Controllers\ProfileController;
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

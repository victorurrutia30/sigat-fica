<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class CausaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahora = now();

        $causas = [
            [
                'nombre' => 'Situación económica',
                'descripcion' => 'El estudiante reporta dificultades económicas que afectan su continuidad académica.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Problemas de salud',
                'descripcion' => 'El estudiante indica una condición de salud propia o familiar que impidió presentarse a la evaluación.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Problemas laborales',
                'descripcion' => 'El estudiante informa incompatibilidad laboral o cambios de horario que afectaron su asistencia.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Falta de comunicación',
                'descripcion' => 'No fue posible establecer comunicación efectiva con el estudiante durante el seguimiento.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Dificultad académica',
                'descripcion' => 'El estudiante manifiesta dificultad para comprender los contenidos de la asignatura.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Problemas de conectividad',
                'descripcion' => 'El estudiante reporta limitaciones de acceso a internet o equipo tecnológico.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Retiro en proceso',
                'descripcion' => 'El estudiante informa que está gestionando o considerando el retiro de la asignatura.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'nombre' => 'Motivo no especificado',
                'descripcion' => 'El estudiante no brinda una causa concreta durante el seguimiento realizado.',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        DB::table('causas')->upsert(
            $causas,
            ['nombre'],
            ['descripcion', 'activo', 'updated_at']
        );
    }
}

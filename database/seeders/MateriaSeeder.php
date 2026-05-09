<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahora = now();

        $materias = [
            [
                'codigo' => 'FICA-PRG01',
                'nombre' => 'Programación I',
                'creditos' => 4,
                'ciclo_plan' => 1,
                'departamento' => 'Programación',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-MAT01',
                'nombre' => 'Matemática I',
                'creditos' => 4,
                'ciclo_plan' => 1,
                'departamento' => 'Matemática',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-SIS01',
                'nombre' => 'Introducción a los Sistemas Informáticos',
                'creditos' => 3,
                'ciclo_plan' => 1,
                'departamento' => 'Sistemas',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-PRG02',
                'nombre' => 'Programación II',
                'creditos' => 4,
                'ciclo_plan' => 2,
                'departamento' => 'Programación',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-MAT02',
                'nombre' => 'Matemática II',
                'creditos' => 4,
                'ciclo_plan' => 2,
                'departamento' => 'Matemática',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-BD01',
                'nombre' => 'Bases de Datos I',
                'creditos' => 4,
                'ciclo_plan' => 3,
                'departamento' => 'Bases de Datos',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-WEB01',
                'nombre' => 'Desarrollo Web I',
                'creditos' => 4,
                'ciclo_plan' => 4,
                'departamento' => 'Desarrollo Web',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-RED01',
                'nombre' => 'Redes de Computadoras I',
                'creditos' => 4,
                'ciclo_plan' => 5,
                'departamento' => 'Redes',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-ISW01',
                'nombre' => 'Ingeniería de Software I',
                'creditos' => 4,
                'ciclo_plan' => 6,
                'departamento' => 'Ingeniería de Software',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
            [
                'codigo' => 'FICA-BD02',
                'nombre' => 'Bases de Datos II',
                'creditos' => 4,
                'ciclo_plan' => 6,
                'departamento' => 'Bases de Datos',
                'activo' => true,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ],
        ];

        DB::table('materias')->upsert(
            $materias,
            ['codigo'],
            ['nombre', 'creditos', 'ciclo_plan', 'departamento', 'activo', 'updated_at']
        );
    }
}

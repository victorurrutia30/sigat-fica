<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class TutorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahora = now();

        $tutores = [
            [
                'correo_usuario' => 'tutor.demo@utec.edu.sv',
                'codigo_empleado' => 'DTC-001',
                'nombre_completo' => 'Tutor Demo Uno',
                'correo_institucional' => 'tutor.demo@utec.edu.sv',
                'departamento' => 'Informática',
                'fecha_contratacion' => '2020-01-15',
            ],
            [
                'correo_usuario' => 'tutor.02.demo@utec.edu.sv',
                'codigo_empleado' => 'DTC-002',
                'nombre_completo' => 'Tutor Demo Dos',
                'correo_institucional' => 'tutor.02.demo@utec.edu.sv',
                'departamento' => 'Ingeniería de Software',
                'fecha_contratacion' => '2021-02-01',
            ],
            [
                'correo_usuario' => 'tutor.03.demo@utec.edu.sv',
                'codigo_empleado' => 'DTC-003',
                'nombre_completo' => 'Tutor Demo Tres',
                'correo_institucional' => 'tutor.03.demo@utec.edu.sv',
                'departamento' => 'Redes y Telecomunicaciones',
                'fecha_contratacion' => '2019-07-10',
            ],
            [
                'correo_usuario' => 'tutor.04.demo@utec.edu.sv',
                'codigo_empleado' => 'DTC-004',
                'nombre_completo' => 'Tutor Demo Cuatro',
                'correo_institucional' => 'tutor.04.demo@utec.edu.sv',
                'departamento' => 'Bases de Datos',
                'fecha_contratacion' => '2022-03-20',
            ],
            [
                'correo_usuario' => 'tutor.05.demo@utec.edu.sv',
                'codigo_empleado' => 'DTC-005',
                'nombre_completo' => 'Tutor Demo Cinco',
                'correo_institucional' => 'tutor.05.demo@utec.edu.sv',
                'departamento' => 'Programación',
                'fecha_contratacion' => '2023-01-10',
            ],
        ];

        foreach ($tutores as $tutor) {
            $usuario = DB::table('users')
                ->where('correo', $tutor['correo_usuario'])
                ->first();

            if (! $usuario) {
                continue;
            }

            DB::table('tutores')->updateOrInsert(
                ['codigo_empleado' => $tutor['codigo_empleado']],
                [
                    'usuario_id' => $usuario->id,
                    'nombre_completo' => $tutor['nombre_completo'],
                    'correo_institucional' => $tutor['correo_institucional'],
                    'departamento' => $tutor['departamento'],
                    'fecha_contratacion' => $tutor['fecha_contratacion'],
                    'tiempo_completo' => true,
                    'activo' => true,
                    'deleted_at' => null,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]
            );
        }
    }
}

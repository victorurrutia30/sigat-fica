<?php

namespace Database\Seeders;

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

        $usuario = DB::table('users')
            ->where('correo', 'tutor.demo@utec.edu.sv')
            ->first();

        if (! $usuario) {
            return;
        }

        DB::table('tutores')->updateOrInsert(
            ['codigo_empleado' => 'DTC-001'],
            [
                'usuario_id' => $usuario->id,
                'nombre_completo' => 'Tutor Demo',
                'correo_institucional' => 'tutor.demo@utec.edu.sv',
                'departamento' => 'Informática',
                'categoria_docente' => 'DTC',
                'fecha_contratacion' => '2020-01-15',
                'tiempo_completo' => true,
                'habilitado_para_tutorias' => true,
                'es_excepcion_tutoria' => false,
                'motivo_excepcion_tutoria' => null,
                'origen_registro' => 'seeder',
                'activo' => true,
                'deleted_at' => null,
                'created_at' => $ahora,
                'updated_at' => $ahora,
            ]
        );
    }
}

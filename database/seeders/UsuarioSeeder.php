<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $ahora = now();

        $usuarios = [
            [
                'nombre' => 'Coordinación Demo',
                'correo' => 'coordinacion.demo@utec.edu.sv',
                'rol' => 'coordinacion',
            ],
            [
                'nombre' => 'Tutor Demo Uno',
                'correo' => 'tutor.demo@utec.edu.sv',
                'rol' => 'tutor',
            ],
            [
                'nombre' => 'Tutor Demo Dos',
                'correo' => 'tutor.02.demo@utec.edu.sv',
                'rol' => 'tutor',
            ],
            [
                'nombre' => 'Tutor Demo Tres',
                'correo' => 'tutor.03.demo@utec.edu.sv',
                'rol' => 'tutor',
            ],
            [
                'nombre' => 'Tutor Demo Cuatro',
                'correo' => 'tutor.04.demo@utec.edu.sv',
                'rol' => 'tutor',
            ],
            [
                'nombre' => 'Tutor Demo Cinco',
                'correo' => 'tutor.05.demo@utec.edu.sv',
                'rol' => 'tutor',
            ],
        ];

        foreach ($usuarios as $usuario) {
            DB::table('users')->updateOrInsert(
                ['correo' => $usuario['correo']],
                [
                    'nombre' => $usuario['nombre'],
                    'password' => Hash::make('Password123'),
                    'rol' => $usuario['rol'],
                    'activo' => true,
                    'updated_at' => $ahora,
                    'created_at' => $ahora,
                ]
            );
        }
    }
}

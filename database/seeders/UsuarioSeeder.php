<?php

namespace Database\Seeders;

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
                'nombre' => 'Tutor Demo',
                'correo' => 'tutor.demo@utec.edu.sv',
                'rol' => 'tutor',
            ],
        ];

        foreach ($usuarios as $usuario) {
            DB::table('users')->updateOrInsert(
                ['correo' => $usuario['correo']],
                [
                    'nombre' => $usuario['nombre'],
                    'email_verified_at' => $ahora,
                    'password' => Hash::make('Password123'),
                    'rol' => $usuario['rol'],
                    'activo' => true,
                    'created_at' => $ahora,
                    'updated_at' => $ahora,
                ]
            );
        }
    }
}

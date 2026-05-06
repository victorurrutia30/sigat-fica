<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['correo' => 'coordinacion.demo@utec.edu.sv'],
            [
                'nombre' => 'Coordinación Demo',
                'password' => Hash::make('Password123'),
                'rol' => 'coordinacion',
                'activo' => true,
            ]
        );

        User::updateOrCreate(
            ['correo' => 'tutor.demo@utec.edu.sv'],
            [
                'nombre' => 'Usuario Tutor Demo',
                'password' => Hash::make('Password123'),
                'rol' => 'tutor',
                'activo' => true,
            ]
        );

        $this->call([
            MateriaSeeder::class,
            CausaSeeder::class,
        ]);
    }
}

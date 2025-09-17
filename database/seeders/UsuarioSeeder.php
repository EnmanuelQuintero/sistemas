<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;

class UsuarioSeeder extends Seeder
{
    public function run(): void
    {
        Usuario::create([
            'nombre' => 'admin',
            'password' => Hash::make('123456'),
            'rol' => 'admin',
            'estado' => true,
        ]);
    }
}

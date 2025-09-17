<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Area;

class AreasSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            ['nombre' => 'Recursos Humanos', 'activo' => true],
            ['nombre' => 'Finanzas', 'activo' => true],
            ['nombre' => 'Tecnología', 'activo' => true],
            ['nombre' => 'Producción', 'activo' => true],
            ['nombre' => 'Ventas y Marketing', 'activo' => true],
        ];

        foreach ($areas as $area) {
            Area::create($area);
        }
    }
}

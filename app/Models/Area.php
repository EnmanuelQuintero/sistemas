<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    protected $fillable = [
        'nombre',
        'activo',
    ];

    public function empleados()
    {
        return $this->hasMany(Empleado::class, 'id_area');
    }
}

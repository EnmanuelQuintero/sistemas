<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'foto',
        'nombre',
        'cedula',
        'ingreso',
        'inss',
        'id_area',
        'cargo',
        'salario',
        'finalizo',
        'estado',   // nuevo
        'activo',   // nuevo
    ];

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_area');
    }

    public function dias()
    {
        return $this->hasMany(Dia::class, 'empleado_id');
    }

    public function extras()
    {
        return $this->hasMany(Extra::class, 'empleado_id');
    }

    public function detalleNominas()
    {
        return $this->hasMany(DetalleNomina::class, 'empleado_id');
    }
}

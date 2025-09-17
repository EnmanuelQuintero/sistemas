<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetalleNomina extends Model
{
    protected $table = 'detallenominas';

    protected $fillable = [
        'nomina_id',
        'empleado_id',
        'inss',
        'ir',
        'inatec',
        'patronal',
    ];

    public function nomina()
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}

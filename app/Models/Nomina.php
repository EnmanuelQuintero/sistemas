<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nomina extends Model
{
    protected $table = 'nominas';

    protected $fillable = [
        'mes',
        'quincena',
        'observaciones',
    ];


    public function detalles()
    {
        return $this->hasMany(DetalleNomina::class, 'nomina_id')->with('empleado.dias', 'empleado.extras');
    }

}

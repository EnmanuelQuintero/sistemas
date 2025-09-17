<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dia extends Model
{
    protected $table = 'dias';

    protected $fillable = [
        'empleado_id',
        'tipo',
        'fecha',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}

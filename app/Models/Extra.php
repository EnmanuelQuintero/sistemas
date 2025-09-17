<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extra extends Model
{
    protected $table = 'extras';

    protected $fillable = [
        'empleado_id',
        'fecha',
        'cantidad',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }
}

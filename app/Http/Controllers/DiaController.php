<?php

namespace App\Http\Controllers;
use App\Models\Dia;
use App\Models\Extra;
use Illuminate\Http\Request;

class DiaController extends Controller
{


    public function store(Request $request)
    {
        $dias = $request->all();

        foreach($dias as $diaData) {
            validator($diaData, [
                'empleado_id' => 'required|exists:empleados,id',
                'tipo' => 'required|in:1,2,3',
                'fecha' => 'required|date',
            ])->validate();

            // Eliminar cualquier hora extra registrada si ahora es un día normal
            Extra::where('empleado_id', $diaData['empleado_id'])
                ->where('fecha', $diaData['fecha'])
                ->delete();

            Dia::updateOrCreate(
                ['empleado_id' => $diaData['empleado_id'], 'fecha' => $diaData['fecha']],
                ['tipo' => $diaData['tipo']]
            );
        }

        return response()->json(['success' => true]);
    }



    public function registrados($empleadoId) {
        $dias = Dia::where('empleado_id', $empleadoId)->get(['fecha','tipo']);
        return response()->json($dias);
    }
}

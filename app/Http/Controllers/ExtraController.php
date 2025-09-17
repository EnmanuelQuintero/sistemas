<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Extra;
class ExtraController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->all(); // array de {empleado_id, fecha, cantidad}

        foreach($data as $extra){
            validator($extra, [
                'empleado_id' => 'required|exists:empleados,id',
                'fecha' => 'required|date',
                'cantidad' => 'required|numeric|min:0',
            ])->validate();

            Extra::updateOrCreate(
                ['empleado_id' => $extra['empleado_id'], 'fecha' => $extra['fecha']],
                ['cantidad' => $extra['cantidad']]
            );
        }

        return response()->json(['success' => true]);
    }

    public function registrados($empleadoId)
    {
        $extras = Extra::where('empleado_id', $empleadoId)->get(['fecha', 'cantidad']);
        return response()->json($extras);
    }

}

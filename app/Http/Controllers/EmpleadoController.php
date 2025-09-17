<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\Area;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    public function index()
    {
        $empleados = Empleado::with('area')->paginate(10);
        return view('empleados.index', compact('empleados'));
    }

    public function create()
    {
        $areas = Area::all();
        return view('empleados.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:255|unique:empleados',
            'ingreso' => 'required|date',
            'inss' => 'required|string|max:255',
            'id_area' => 'required|exists:areas,id',
            'cargo' => 'required|string|max:255',
            'salario' => 'required|numeric',
            'foto' => 'nullable|image|max:2048',
            'estado' => 'nullable|string|max:255',
            'activo' => 'nullable|boolean',
        ]);

        $empleado = $request->all();

        if ($request->hasFile('foto')) {
            $empleado['foto'] = $request->file('foto')->store('empleados', 'public');
        }

        // valores por defecto
        $empleado['estado'] = $empleado['estado'] ?? 'trabajando';
        $empleado['activo'] = $empleado['activo'] ?? 1;

        Empleado::create($empleado);

        return redirect()->route('empleados.index')->with('success', 'Empleado creado correctamente');
    }

    public function edit(Empleado $empleado)
    {
        $areas = Area::all();
        return view('empleados.edit', compact('empleado', 'areas'));
    }

    public function update(Request $request, Empleado $empleado)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'cedula' => 'required|string|max:255|unique:empleados,cedula,' . $empleado->id,
            'ingreso' => 'required|date',
            'inss' => 'required|string|max:255',
            'id_area' => 'required|exists:areas,id',
            'cargo' => 'required|string|max:255',
            'salario' => 'required|numeric',
            'foto' => 'nullable|image|max:2048',
            'estado' => 'nullable|string|max:255',
            'activo' => 'nullable|boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('empleados', 'public');
        }

        // valores por defecto si no vienen
        $data['estado'] = $data['estado'] ?? $empleado->estado;
        $data['activo'] = $data['activo'] ?? $empleado->activo;

        $empleado->update($data);

        return redirect()->route('empleados.index')->with('success', 'Empleado actualizado');
    }

    public function destroy(Empleado $empleado)
    {
        $empleado->delete();
        return redirect()->route('empleados.index')->with('success', 'Empleado eliminado');
    }
}

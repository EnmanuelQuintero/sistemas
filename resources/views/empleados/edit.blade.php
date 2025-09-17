@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-8 px-6 bg-white shadow-lg rounded-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6 border-b pb-2">✏️ Editar Empleado</h1>

    <form action="{{ route('empleados.update', $empleado) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <!-- Foto -->
        <div class="flex flex-col items-center">
            @if($empleado->foto)
                <img src="{{ asset('storage/'.$empleado->foto) }}" class="h-28 w-28 rounded-full object-cover shadow-md">
            @else
                <div class="h-28 w-28 rounded-full bg-gray-200 flex items-center justify-center text-gray-400 shadow-md">
                    N/A
                </div>
            @endif

            <input type="file" name="foto"
                   class="mt-3 w-full md:w-1/2 border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        <!-- Nombre -->
        <div>
            <label class="block text-sm font-semibold text-gray-700">👤 Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre) }}"
                   class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                   required>
        </div>

        <!-- Grid con 2 columnas -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <!-- Cédula -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">🆔 Cédula</label>
                <input type="text" name="cedula" value="{{ old('cedula', $empleado->cedula) }}"
                       class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <!-- Fecha de Ingreso -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">📅 Fecha de Ingreso</label>
                <input type="date" name="ingreso" value="{{ old('ingreso', $empleado->ingreso) }}"
                       class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <!-- INSS -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">💳 INSS</label>
                <input type="text" name="inss" value="{{ old('inss', $empleado->inss) }}"
                       class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <!-- Área -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">🏢 Área</label>
                <select name="id_area"
                        class="mt-2 w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}" {{ $empleado->id_area == $area->id ? 'selected' : '' }}>
                            {{ $area->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Cargo -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">💼 Cargo</label>
                <input type="text" name="cargo" value="{{ old('cargo', $empleado->cargo) }}"
                       class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <!-- Salario -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">💵 Salario</label>
                <input type="number" step="0.01" name="salario" value="{{ old('salario', $empleado->salario) }}"
                       class="mt-2 w-full border rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                       required>
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">⚡ Estado</label>
                <select name="estado"
                        class="mt-2 w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="trabajando" {{ $empleado->estado=='trabajando' ? 'selected' : '' }}>Trabajando</option>
                    <option value="suspendido" {{ $empleado->estado=='suspendido' ? 'selected' : '' }}>Suspendido</option>
                    <option value="terminado" {{ $empleado->estado=='terminado' ? 'selected' : '' }}>Terminado</option>
                </select>
            </div>

            <!-- Activo -->
            <div>
                <label class="block text-sm font-semibold text-gray-700">✅ Activo</label>
                <select name="activo"
                        class="mt-2 w-full border rounded-lg px-3 py-2 text-sm bg-white focus:ring-2 focus:ring-blue-500 focus:outline-none">
                    <option value="1" {{ $empleado->activo==1 ? 'selected' : '' }}>Sí</option>
                    <option value="0" {{ $empleado->activo==0 ? 'selected' : '' }}>No</option>
                </select>
            </div>

        </div> <!-- fin grid -->

        <!-- Botones -->
        <div class="flex justify-end space-x-3 pt-4 border-t">
            <button type="submit"
                    class="bg-blue-600 text-white px-5 py-2 rounded-lg hover:bg-blue-700 transition">
                💾 Actualizar
            </button>
            <a href="{{ route('empleados.index') }}"
               class="bg-gray-200 text-gray-700 px-5 py-2 rounded-lg hover:bg-gray-300 transition">
                ❌ Cancelar
            </a>
        </div>
    </form>
</div>
@endsection

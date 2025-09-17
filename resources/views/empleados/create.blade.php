@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 px-4 bg-white shadow rounded-lg">
    <h1 class="text-xl font-bold mb-4">Registrar Empleado</h1>

    <form action="{{ route('empleados.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- Foto redonda arriba --}}
        <div class="flex flex-col items-center">
            <div class="w-32 h-32 rounded-full bg-gray-200 flex items-center justify-center overflow-hidden">
                <span class="text-gray-400 text-sm">Sin foto</span>
            </div>
            <input type="file" name="foto" class="mt-3 w-full border rounded-lg px-3 py-2">
        </div>

        {{-- Nombre --}}
        <div>
            <label class="block text-sm font-medium">Nombre</label>
            <input type="text" name="nombre" value="{{ old('nombre') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
        </div>

        {{-- Grid de 2 columnas --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Cédula</label>
                <input type="text" name="cedula" value="{{ old('cedula') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Fecha de Ingreso</label>
                <input type="date" name="ingreso" value="{{ old('ingreso') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">INSS</label>
                <input type="text" name="inss" value="{{ old('inss') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Área</label>
                <select name="id_area" class="mt-1 w-full border rounded-lg px-3 py-2" required>
                    <option value="">Seleccione un área</option>
                    @foreach($areas as $area)
                        <option value="{{ $area->id }}">{{ $area->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium">Cargo</label>
                <input type="text" name="cargo" value="{{ old('cargo') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>

            <div>
                <label class="block text-sm font-medium">Salario</label>
                <input type="number" step="0.01" name="salario" value="{{ old('salario') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm font-medium">Estado</label>
                <select name="estado" class="mt-1 w-full border rounded-lg px-3 py-2">
                    <option value="trabajando" {{ old('estado')=='trabajando' ? 'selected' : '' }}>Trabajando</option>
                    <option value="suspendido" {{ old('estado')=='suspendido' ? 'selected' : '' }}>Suspendido</option>
                    <option value="terminado" {{ old('estado')=='terminado' ? 'selected' : '' }}>Terminado</option>
                </select>
            </div>
        </div>

        {{-- Botones --}}
        <div class="flex items-center justify-start space-x-3">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Guardar</button>
            <a href="{{ route('empleados.index') }}" class="text-gray-600 hover:underline">Cancelar</a>
        </div>
    </form>
</div>
@endsection

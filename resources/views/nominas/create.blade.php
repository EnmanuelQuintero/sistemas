@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-6 px-4 bg-white shadow rounded-lg">
    <h1 class="text-xl font-bold mb-4">Generar Nómina</h1>

    <form action="{{ route('nominas.store') }}" method="POST" class="space-y-5">
        @csrf

        <div>
            <label class="block text-sm font-medium">Quincena</label>
            <select name="quincena" class="mt-1 w-full border rounded-lg px-3 py-2" required>
                <option value="primera">Primera Quincena</option>
                <option value="segunda">Segunda Quincena</option>
            </select>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Mes</label>
                <select name="mes" class="mt-1 w-full border rounded-lg px-3 py-2" required>
                    @for ($m=1; $m<=12; $m++)
                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium">Año</label>
                <input type="number" name="anio" value="{{ date('Y') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>
            <div>
                <label class="block text-sm font-medium">Fecha Fin</label>
                <input type="date" name="fecha_fin" class="mt-1 w-full border rounded-lg px-3 py-2" required>
            </div>
        </div>

        <button class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700">Generar</button>
        <a href="{{ route('nominas.index') }}" class="ml-3 text-gray-600 hover:underline">Cancelar</a>
    </form>
</div>
@endsection

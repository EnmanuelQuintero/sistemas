@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4 bg-white rounded-lg shadow">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">
        Preview - {{ $quincena == 1 ? 'Primera' : 'Segunda' }} Quincena de 
        {{ \Carbon\Carbon::create($anio, $mes)->translatedFormat('F Y') }}
    </h1>

    <table class="w-full border border-gray-300 text-sm">
        <thead class="bg-gray-100">
            <tr>
                <th class="border px-2 py-1 text-left">Empleado</th>
                <th class="border px-2 py-1 text-right">Salario Quincenal</th>
                <th class="border px-2 py-1 text-right">INSS</th>
                <th class="border px-2 py-1 text-right">IR</th>
                <th class="border px-2 py-1 text-right">Neto a Pagar</th>
            </tr>
        </thead>
        <tbody>
            @foreach($datosNomina as $dato)
                <tr>
                    <td class="border px-2 py-1">{{ $dato['empleado'] }}</td>
                    <td class="border px-2 py-1 text-right">{{ number_format($dato['salario_quincenal'], 2) }}</td>
                    <td class="border px-2 py-1 text-right">{{ number_format($dato['inss'], 2) }}</td>
                    <td class="border px-2 py-1 text-right">{{ number_format($dato['ir'], 2) }}</td>
                    <td class="border px-2 py-1 text-right font-semibold">{{ number_format($dato['neto'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-6 flex gap-2">
        <a href="{{ route('nominas.index') }}" 
           class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
           ⬅ Volver
        </a>
        <form action="{{ route('nominas.store') }}" method="POST">
            @csrf
            <input type="hidden" name="mes" value="{{ $mes }}">
            <input type="hidden" name="anio" value="{{ $anio }}">
            <input type="hidden" name="quincena" value="{{ $quincena }}">
            <button class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                ✅ Generar Nómina Oficial
            </button>
        </form>
    </div>

</div>
@endsection

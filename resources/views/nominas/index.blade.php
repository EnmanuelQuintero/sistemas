@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4" x-data="{ view: 'grid' }">

    <h1 class="text-2xl font-bold mb-6 text-gray-800">Nómina Quincenal</h1>

    {{-- Botones de vista --}}
    <div class="mb-4 flex gap-2">
        <button @click="view = 'grid'" 
                :class="{'bg-blue-600 text-white': view === 'grid', 'bg-gray-200 text-gray-800': view !== 'grid'}"
                class="px-4 py-2 rounded transition">Cuadrícula</button>

        <button @click="view = 'list'" 
                :class="{'bg-blue-600 text-white': view === 'list', 'bg-gray-200 text-gray-800': view !== 'list'}"
                class="px-4 py-2 rounded transition">Lista</button>
    </div>

    @php
        use Carbon\Carbon;

        $hoy = Carbon::now();
        $mesActual = $hoy->month;
        $anioActual = $hoy->year;
        $quincenaHoy = $hoy->day <= 15 ? 1 : 2;

        $quincenas = [
            1 => ['inicio' => Carbon::create($anioActual, $mesActual, 1), 'fin' => Carbon::create($anioActual, $mesActual, 15)],
            2 => ['inicio' => Carbon::create($anioActual, $mesActual, 16), 'fin' => Carbon::create($anioActual, $mesActual, $hoy->daysInMonth)],
        ];

        $nominasAgrupadas = $nominasAnteriores->groupBy(function($nomina) {
            return Carbon::parse($nomina->created_at)->format('Y-m');
        });

        $mesesFuturos = collect();
        for($i = 1; $i <= 3; $i++) {
            $fecha = Carbon::create($anioActual, $mesActual, 1)->addMonths($i);
            $mesesFuturos->push($fecha);
        }
    @endphp

    {{-- Función para tarjetas de nómina --}}
    @php
        function renderNominaCard($nomina, $q, $isCurrent=false) {
            return view('components.nomina-card', compact('nomina','q','isCurrent'))->render();
        }
    @endphp

    {{-- Mes actual --}}
    <h2 class="text-xl font-semibold mb-4">{{ Carbon::create($anioActual, $mesActual)->translatedFormat('F Y') }}</h2>
    <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4' : 'flex flex-col gap-2 mb-8'">
        @foreach([1,2] as $q)
            @php
                if(isset($nominaActual) && $nominaActual->mes == $mesActual && $nominaActual->quincena == $q) {
                    $nominaExistente = $nominaActual;
                } else {
                    $nominaExistente = $nominasAnteriores->where('mes', $mesActual)->where('quincena', $q)->first();
                }
            @endphp

            {{-- Tarjeta --}}
            @if($q <= $quincenaHoy)
                @if($nominaExistente)
                    <div class="block p-4 bg-gray-100 rounded-lg shadow transition space-y-2">
                        <p class="font-semibold text-gray-800">{{ $q == 1 ? 'Primera' : 'Segunda' }} Quincena</p>
                        <div class="flex gap-2">
                            <a href="{{ route('nominas.show', $nominaExistente) }}" 
                               class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 transition text-sm">👁 Ver</a>
                            @if($q == $quincenaHoy)
                                <form action="{{ route('nominas.store') }}" method="POST">
                                    @csrf
                                    <button class="bg-yellow-500 text-white px-3 py-1 rounded hover:bg-yellow-600 transition text-sm">🔄 Actualizar</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @else
                    @if($q == $quincenaHoy)
                        <div class="block p-4 bg-gray-100 rounded-lg shadow transition space-y-2">
                            <p class="font-semibold text-gray-800">{{ $q == 1 ? 'Primera' : 'Segunda' }} Quincena</p>
                            <form action="{{ route('nominas.store') }}" method="POST">
                                @csrf
                                <button class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition text-sm">➕ Generar Nómina</button>
                            </form>
                        </div>
                    @endif
                @endif
            @endif
        @endforeach
    </div>

    {{-- Nóminas anteriores --}}
    @foreach($nominasAgrupadas as $mes => $listaNominas)
        @php $fecha = Carbon::createFromFormat('Y-m', $mes); @endphp
        @if($fecha->month != $mesActual || $fecha->year != $anioActual)
            <h2 class="text-xl font-semibold mb-4">{{ $fecha->translatedFormat('F Y') }}</h2>
            <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4' : 'flex flex-col gap-2 mb-8'">
                @foreach($listaNominas as $nomina)
                    <a href="{{ route('nominas.show', $nomina) }}" 
                       class="block p-4 bg-gray-100 rounded-lg hover:bg-gray-200 shadow transition">
                        <p class="font-semibold text-gray-800">{{ $nomina->quincena == 1 ? 'Primera' : 'Segunda' }} Quincena</p>
                        <p class="text-gray-600 text-sm">Generada</p>
                    </a>
                @endforeach
            </div>
        @endif
    @endforeach

    {{-- Meses futuros bloqueados --}}
    @foreach($mesesFuturos as $fecha)
        <h2 class="text-xl font-semibold mb-4">{{ $fecha->translatedFormat('F Y') }}</h2>
        <div :class="view === 'grid' ? 'grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4' : 'flex flex-col gap-2 mb-8'">
            @foreach([1,2] as $q)
                <div class="block p-4 bg-gray-200 rounded-lg shadow opacity-70 cursor-not-allowed">
                    <p class="font-semibold text-gray-600">{{ $q == 1 ? 'Primera' : 'Segunda' }} Quincena</p>
                    <p class="text-gray-500 text-sm">No disponible</p>
                </div>
            @endforeach
        </div>
    @endforeach

</div>
@endsection

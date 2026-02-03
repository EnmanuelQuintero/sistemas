@extends('layouts.app')

@section('content')
    @php
        use App\Models\Dia;
        use App\Models\Extra;
    @endphp

    <div class="max-w-7xl mx-auto py-6 px-4 bg-white shadow rounded-lg space-y-6">

        <!-- Header -->
        <div class="flex justify-between items-center">
            <h1 class="text-2xl font-bold text-gray-800">
                Nómina - {{ $nomina->quincena == 1 ? 'Primera' : 'Segunda' }} Quincena 
                {{ \Carbon\Carbon::parse($nomina->fecha_inicio)->translatedFormat('F Y') }}
            </h1>
            <a href="{{ route('nominas.index') }}" 
            class="bg-gray-600 text-white px-4 py-2 rounded-lg hover:bg-gray-700 transition">
                ⬅ Volver
            </a>
        </div>

        <!-- Tabla -->
        <div class="overflow-x-auto">
            <table class="table-auto w-full text-xs sm:text-sm md:text-base border-collapse border border-gray-300">
                <thead class="bg-gray-100 text-gray-800">
                    <tr class="text-center">
                        <th class="border px-3 py-2">#</th>
                        <th class="border px-3 py-2">Nombre del trabajador</th>
                        <th class="border px-3 py-2">Cargo</th>
                        <th class="border px-3 py-2"># INSS</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Salario mensual</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Salario diario</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Días trabajados</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Salario quincenal</th>
                        <th class="border px-3 py-2">Hrs extras</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Monto horas extras</th>
                        <th class="border px-3 py-2">Días subsidio</th>
                        <th class="border px-3 py-2">Feriado</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Total devengado</th>
                        <th class="border px-3 py-2">INSS</th>
                        <th class="border px-3 py-2">IR</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Deducción</th>
                        <th class="border px-3 py-2 whitespace-nowrap">Neto a pagar</th>
                        <th class="border px-3 py-2">INATEC</th>
                        <th class="border px-3 py-2 whitespace-nowrap">INSS Patronal</th>
                        <th class="border px-3 py-2">Recibí conforme</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($nomina->detalles as $index => $detalle)
                    @php
                        $empleado = $detalle->empleado;

                        // ===== Cálculos base (SIN redondear) =====
                        $salarioMensual = (float) $empleado->salario;

                        $salarioDiario = $salarioMensual / 30;
                        $salarioHora   = $salarioDiario / 8;

                        // ===== Fechas =====
                        $year = $nomina->created_at->year;

                        $fechaInicio = $nomina->quincena == 1
                            ? \Carbon\Carbon::create($year, $nomina->mes, 1)
                            : \Carbon\Carbon::create($year, $nomina->mes, 16);

                        $fechaFin = $nomina->quincena == 1
                            ? \Carbon\Carbon::create($year, $nomina->mes, 15)
                            : \Carbon\Carbon::create($year, $nomina->mes)->endOfMonth();

                        // ===== Días trabajados =====
                        $diasTrabajados = Dia::where('empleado_id', $empleado->id)
                            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                            ->whereIn('tipo', [1,2,3])
                            ->count();

                        // ===== Ajuste especial febrero (14 → 15 días) =====
                        $totalDiasRango = $fechaInicio->diffInDays($fechaFin) + 1;

                        // Si es febrero y trabajó todos los días de la quincena (14), pagar 15
                        if ($fechaInicio->month == 2 && $totalDiasRango == 15 && $diasTrabajados == 14) {
                            $diasTrabajados = 15;
                        }

                        // ===== Horas extras =====
                        $horasExtrasCantidad = Extra::where('empleado_id', $empleado->id)
                            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
                            ->sum('cantidad');

                        $horasExtras = ($horasExtrasCantidad * $salarioHora) * 2;

                        // ===== Devengado =====
                        $salarioBase = $salarioDiario * $diasTrabajados;

                        
                        $salarioQuincenal = $salarioBase;

                        $feriado = 0;
                        $subsidioDias = 0;

                        $totalDevengado = $salarioBase + $horasExtras + $feriado;

                        // ===== Deducciones =====
                        $inss = (float) $detalle->inss;
                        $ir   = (float) $detalle->ir;

                        $totalDeduccion = $inss + $ir;

                        // ===== Totales finales (REDONDEAR SOLO AQUÍ) =====
                        $neto = round($totalDevengado - $totalDeduccion, 2);

                        $inatec = (float) $detalle->inatec;
                        $inssPatronal = (float) $detalle->patronal;
                    @endphp

                        <tr class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                            <td class="border px-3 py-2 text-center">{{ $index+1 }}</td>
                            <td class="border px-3 py-2">{{ $detalle->empleado->nombre }}</td>
                            <td class="border px-3 py-2">{{ $detalle->empleado->cargo }}</td>
                            <td class="border px-3 py-2 text-center">{{ $detalle->empleado->inss }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($detalle->empleado->salario,2) }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($detalle->empleado->salario/30,2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ $diasTrabajados ?? 0 }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($salarioQuincenal ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ $horasExtrasCantidad ?? 0 }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($horasExtras ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-center">{{ $subsidioDias ?? 0 }}</td>
                            <td class="border px-3 py-2 text-right">C$ {{ number_format($feriado ?? 0,2) }}</td>
                            <td class="border px-3 py-2 font-semibold text-right whitespace-nowrap">C$ {{ number_format($totalDevengado ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($inss ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($ir ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($totalDeduccion ?? 0,2) }}</td>
                            <td class="border px-3 py-2 font-semibold text-right whitespace-nowrap">C$ {{ number_format($neto ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-right">C$ {{ number_format($inatec ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-right whitespace-nowrap">C$ {{ number_format($inssPatronal ?? 0,2) }}</td>
                            <td class="border px-3 py-2 text-center">________________</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


    </div>
@endsection

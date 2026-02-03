<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Nomina;
use App\Models\DetalleNomina;
use App\Models\Empleado;
use App\Models\Dia;
use App\Models\Extra;
use Carbon\Carbon;

class NominaController extends Controller
{
    // Mostrar index con botón generar/actualizar nómina
    public function index()
    {
        $hoy = Carbon::now();
        $mesActual = $hoy->month;
        $quincenaActual = $hoy->day <= 15 ? 1 : 2;

        // Obtener la nómina actual
        $nominaActual = Nomina::where('mes', $mesActual)
                            ->where('quincena', $quincenaActual)
                            ->first();

        // Obtener todas las nóminas anteriores (ordenadas de más reciente a más antiguo)
        $nominasAnteriores = Nomina::where(function($q) use ($mesActual, $quincenaActual){
            $q->where('mes', '<', $mesActual)
            ->orWhere(function($q2) use ($mesActual, $quincenaActual){
                $q2->where('mes', $mesActual)
                    ->where('quincena', '<', $quincenaActual);
            });
        })
        ->orderBy('mes', 'desc')
        ->orderBy('quincena', 'desc')
        ->get();

        return view('nominas.index', compact('nominaActual', 'nominasAnteriores'));
    }

    // Formulario para generar nueva nómina
    public function create()
    {
        return view('nominas.create');
    }

    // Genera la nómina quincenal
    // Generar o actualizar nómina
    public function store()
    {
        $hoy = Carbon::now();
        $mes = $hoy->month;
        $year = $hoy->year;

        $quincena = $hoy->day <= 15 ? 1 : 2;

        // Obtener nómina existente o crear nueva
        $nomina = Nomina::firstOrCreate(
            ['mes' => $mes, 'quincena' => $quincena],
            ['observaciones' => 'Nómina generada automáticamente']
        );

        $empleados = Empleado::where('activo', 1)->get();

        foreach ($empleados as $empleado) {

            // ===== Rango de fechas =====
            $inicioDia = $quincena === 1 ? 1 : 16;
            $finDia = $quincena === 1 ? 15 : Carbon::create($year, $mes)->daysInMonth;

            $inicioFecha = Carbon::create($year, $mes, $inicioDia);
            $finFecha    = Carbon::create($year, $mes, $finDia);

            // ===== Salarios base =====
            $salarioDiario = $empleado->salario / 30;
            $salarioHora   = $salarioDiario / 8;

            // ===== Días trabajados =====
            $diasTrabajados = Dia::where('empleado_id', $empleado->id)
                ->whereBetween('fecha', [$inicioFecha, $finFecha])
                ->whereIn('tipo', [1,2,3])
                ->count();

            // ===== Ajuste especial febrero (14 → 15) =====
            $totalDiasRango = $inicioFecha->diffInDays($finFecha) + 1;

            if ($mes == 2 && $totalDiasRango == 15 && $diasTrabajados == 14) {
                $diasTrabajados = 15;
            }

            // ===== Horas extras =====
            $horasExtrasCantidad = Extra::where('empleado_id', $empleado->id)
                ->whereBetween('fecha', [$inicioFecha, $finFecha])
                ->sum('cantidad');

            $horasExtras = ($horasExtrasCantidad * $salarioHora) * 2;

            // ===== Devengado =====
            $salarioQuincenal = ($salarioDiario * $diasTrabajados) + $horasExtras;

            // ===== Deducciones =====
            $inss = $salarioQuincenal * 0.07;
            $ir   = $this->calcularIR($salarioQuincenal * 2); // salario mensual
            $inatec = $salarioQuincenal * 0.02;
            $patronal = $salarioQuincenal * 0.20;

            // ===== Guardar detalle =====
            DetalleNomina::updateOrCreate(
                ['nomina_id' => $nomina->id, 'empleado_id' => $empleado->id],
                [
                    'inss' => $inss,
                    'ir' => $ir,
                    'inatec' => $inatec,
                    'patronal' => $patronal,
                ]
            );
        }

        return redirect()
            ->route('nominas.index')
            ->with('success', 'Nómina generada/actualizada correctamente');
    }


    private function calcularIR($salarioMensual)
    {
        // Helper local para forzar 2 decimales exactos
        $m = fn($v) => (float) number_format($v, 2, '.', '');

        $salarioAnual     = $m($salarioMensual * 12);
        $inssAnual        = $m($salarioAnual * 0.07);
        $rentaNetaAnual   = $m($salarioAnual - $inssAnual);

        $tabla = [
            ['min' => 0,         'max' => 100000,   'tasa' => 0.0,  'cuota' => 0],
            ['min' => 100001.00, 'max' => 200000,   'tasa' => 0.15, 'cuota' => 0],
            ['min' => 200001.00, 'max' => 350000,   'tasa' => 0.20, 'cuota' => 15000],
            ['min' => 350001.00, 'max' => 500000,   'tasa' => 0.25, 'cuota' => 45000],
            ['min' => 500001.00, 'max' => INF,      'tasa' => 0.30, 'cuota' => 82500],
        ];

        $irAnual = 0;

        foreach ($tabla as $tramo) {
            if ($rentaNetaAnual >= $tramo['min'] && $rentaNetaAnual <= $tramo['max']) {
                $irAnual = $m(
                    ($rentaNetaAnual - $tramo['min']) * $tramo['tasa'] + $tramo['cuota']
                );
                break;
            }
        }

        $irMensual   = $m($irAnual / 12);
        $irQuincenal = $m($irMensual / 2);
        $irQuincenal = round($irQuincenal, 1);
        //dd($irQuincenal);
        return $irQuincenal;
    }


    // Muestra detalle de la nómina
    // Mostrar detalle de nómina
    public function show(Nomina $nomina)
    {
        $nomina->load('detalles.empleado'); // Cargar relaciones
        return view('nominas.show', compact('nomina'));
    }
    // Cierra la nómina
    public function cerrar(Nomina $nomina)
    {
        $nomina->estado = 'cerrada';
        $nomina->save();

        return redirect()->route('nominas.index')->with('success', 'Nómina cerrada correctamente.');
    }

    public function preview($anio, $mes, $quincena)
{
    $empleados = Empleado::all();

    $datosNomina = [];

    foreach ($empleados as $empleado) {
        $salarioMensual = $empleado->salario;
        $salarioQuincenal = $salarioMensual / 2;

        // INSS (6.25%)
        $inss = $salarioQuincenal * 0.0625;

        // IR usando la fórmula que armamos (versión simplificada anualizada)
        $salarioAnual = $salarioMensual * 12;
        $inssAnual = $salarioAnual * 0.0625;
        $rentaNetaAnual = $salarioAnual - $inssAnual;

        $irAnual = 0;
        if ($rentaNetaAnual > 200000 && $rentaNetaAnual <= 350000) {
            $exceso = $rentaNetaAnual - 200000;
            $irAnual = ($exceso * 0.20) + 15000;
        }
        // Aquí puedes agregar los otros rangos de la tabla de IR…

        $irQuincenal = $irAnual / 24; // porque son 24 quincenas al año

        // Neto
        $neto = $salarioQuincenal - $inss - $irQuincenal;

        $datosNomina[] = [
            'empleado' => $empleado->nombre,
            'salario_quincenal' => $salarioQuincenal,
            'inss' => $inss,
            'ir' => $irQuincenal,
            'neto' => $neto,
        ];
    }

    return view('nominas.preview', compact('anio', 'mes', 'quincena', 'datosNomina'));
}
}

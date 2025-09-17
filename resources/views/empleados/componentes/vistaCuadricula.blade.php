<div id="view-grid" class="hidden grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
    @foreach($empleados as $empleado)
    <div class="bg-white shadow rounded-lg p-4 flex flex-col items-center cursor-pointer hover:ring-2 hover:ring-blue-300 transition duration-200 ease-in-out"
         onclick='mostrarEmpleado(@json($empleado))'>

        <!-- Imagen -->
        @if($empleado->foto)
            <img src="{{ asset('storage/'.$empleado->foto) }}" class="h-24 w-24 md:h-28 md:w-28 rounded-full object-cover mb-3">
        @else
            <div class="h-24 w-24 md:h-28 md:w-28 rounded-full bg-gray-200 flex items-center justify-center mb-3 text-gray-400">N/A</div>
        @endif

        <!-- Nombre -->
        <h2 class="font-semibold text-md md:text-sm text-center text-gray-800 mb-1">{{ $empleado->nombre }}</h2>

        <!-- Cargo y Área -->
        <p class="text-sm md:text-base text-gray-500 text-center">{{ $empleado->cargo }}</p>
        <p class="text-sm md:text-base text-gray-500 text-center">{{ $empleado->area->nombre }}</p>

        <!-- Salario -->
        <p class="mt-2 font-bold text-green-600 text-center">${{ number_format($empleado->salario, 2) }}</p>

        <!-- Botones -->
        <div class="flex flex-col w-full mt-4 space-y-2">
            <a href="{{ route('empleados.edit', $empleado) }}"
               class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-center text-sm md:text-base w-full"
               onclick="event.stopPropagation();">
               Editar
            </a>

            <form action="{{ route('empleados.destroy', $empleado) }}" method="POST" class="w-full" onclick="event.stopPropagation();">
                @csrf
                @method('DELETE')
                <button type="submit"
                        class="bg-red-600 text-white px-3 py-2 rounded hover:bg-red-700 text-center text-sm md:text-base w-full"
                        onclick="return confirm('¿Eliminar este empleado?')">
                    Eliminar
                </button>
            </form>

            <button onclick="event.stopPropagation(); abrirModal({{ $empleado->id }}, '{{ $empleado->nombre }}')"
                    class="bg-indigo-600 text-white px-3 py-2 rounded hover:bg-indigo-700 text-center text-sm md:text-base w-full">
                Registrar Días
            </button>
        </div>
    </div>
    @endforeach
</div>

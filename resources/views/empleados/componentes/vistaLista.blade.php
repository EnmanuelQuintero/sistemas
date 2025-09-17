<div id="view-list" class="overflow-x-auto bg-white rounded-lg shadow">
    <table class="w-full text-sm text-left border-collapse">
        <thead class="bg-gray-100 text-gray-700">
            <tr>
                <th class="px-4 py-3">Foto</th>
                <th class="px-4 py-3">Nombre</th>
                <th class="px-4 py-3">Cédula</th>
                <th class="px-4 py-3">Área</th>
                <th class="px-4 py-3">Cargo</th>
                <th class="px-4 py-3">Salario</th>
                <th class="px-4 py-3 text-center">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($empleados as $empleado)
            <tr class="border-t hover:bg-gray-50 cursor-pointer"
                onclick='mostrarEmpleado(@json($empleado))'>

                <td class="px-4 py-3">
                    @if($empleado->foto)
                        <img src="{{ asset('storage/'.$empleado->foto) }}" class="h-10 w-10 rounded-full object-cover">
                    @else
                        <span class="text-gray-400">N/A</span>
                    @endif
                </td>
                <td class="px-4 py-3">{{ $empleado->nombre }}</td>
                <td class="px-4 py-3">{{ $empleado->cedula }}</td>
                <td class="px-4 py-3">{{ $empleado->area->nombre }}</td>
                <td class="px-4 py-3">{{ $empleado->cargo }}</td>
                <td class="px-4 py-3">${{ number_format($empleado->salario, 2) }}</td>
                <td class="px-4 py-3 text-center">
                    <div class="flex flex-col space-y-2 items-center">
                    <!-- Botón Editar -->
                    <!-- Botón Editar -->
                    <button 
                        onclick="event.stopPropagation(); abrirEditarEmpleado({{ $empleado->id }})"
                        class="bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700 text-center text-sm w-full">
                        Editar
                    </button>



                        <form action="{{ route('empleados.destroy', $empleado) }}" method="POST" class="w-full">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 text-center text-sm w-full"
                                    onclick="event.stopPropagation(); return confirm('¿Eliminar este empleado?')">
                                Eliminar
                            </button>
                        </form>

                        <button onclick="event.stopPropagation(); abrirModal({{ $empleado->id }}, '{{ $empleado->nombre }}')"
                                class="bg-indigo-600 text-white px-3 py-1 rounded hover:bg-indigo-700 text-center text-sm w-full">
                            Registrar Días
                        </button>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>

    </table>
</div>

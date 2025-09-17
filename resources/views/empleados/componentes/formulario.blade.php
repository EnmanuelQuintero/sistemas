<form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @csrf
    @if($method) @method($method) @endif

    <!-- Todos tus campos aquí (foto, nombre, etc.) -->
    <div>
        <label class="block text-sm font-medium">Nombre</label>
        <input type="text" name="nombre" value="{{ old('nombre', $empleado->nombre ?? '') }}" class="mt-1 w-full border rounded-lg px-3 py-2" required>
    </div>
    <!-- ... resto de campos ... -->

    <!-- Botones -->
    <div class="col-span-1 md:col-span-2 flex justify-end gap-3 mt-4">
        <button type="button" onclick="cerrarSidebar()" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
            Cancelar
        </button>
        <button class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            {{ $buttonText }}
        </button>
    </div>
</form>

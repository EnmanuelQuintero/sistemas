<!-- Modal Día -->
<div id="modal-dia" class="fixed inset-0 bg-black bg-opacity-70 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg w-[520px] p-6 relative shadow-lg">
        <h2 class="text-xl font-bold mb-4 text-center">Registrar Día</h2>
        <p id="empleado-nombre" class="mb-4 text-gray-800 text-center"></p>

        <!-- Encabezado calendario (Lunes a Domingo) -->
        <div class="grid grid-cols-7 gap-2 mb-2 text-center text-sm font-semibold text-gray-600">
            <div>Lun</div>
            <div>Mar</div>
            <div>Mié</div>
            <div>Jue</div>
            <div>Vie</div>
            <div>Sáb</div>
            <div>Dom</div>
        </div>

        <!-- Contenedor de cuadritos -->
        <div id="dias-container" class="grid grid-cols-7 gap-2"></div>

        <!-- Botones de acción -->
        <div class="flex justify-end space-x-2 mt-4">
            <button onclick="cerrarModal()" 
                    class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                Cancelar
            </button>
            <button onclick="guardarDias()" 
                    class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                Guardar
            </button>
        </div>

        <!-- Botón cerrar -->
        <button onclick="cerrarModal()" class="absolute top-2 right-2 text-gray-400 hover:text-gray-700 text-2xl">
            &times;
        </button>
    </div>
</div>

<script>
    window.diaStoreUrl = "{{ route('dia.store') }}";
    window.extrasStoreUrl = "{{ route('extras.store') }}";
    window.csrfToken = "{{ csrf_token() }}";
</script>

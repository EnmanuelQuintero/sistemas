@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 px-4">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Empleados</h1>
        <div class="flex items-center space-x-2">
            <!-- Botón para cambiar vista -->
            <button onclick="setView('list')" id="btn-list" 
                class="px-3 py-1 rounded border text-sm bg-blue-600 text-white hover:bg-blue-700">
                Lista
            </button>
            <button onclick="setView('grid')" id="btn-grid"
                class="px-3 py-1 rounded border text-sm bg-gray-200 hover:bg-gray-300">
                Cuadrícula
            </button>
            <button onclick="abrirCrearEmpleado()" 
                class="mb-4 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                ➕ Crear Empleado
            </button>

        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    <!-- CONTENEDOR FLEX: Lista/Grid + Panel derecho -->
    <div class="flex space-x-6">

        <!-- IZQUIERDA: lista y grid -->
        <div class="flex-1">
            <!-- Vista tipo LISTA -->
            @include("empleados.componentes.vistaLista")

            <!-- Vista tipo CUADRÍCULA -->
            @include("empleados.componentes.vistaCuadricula")
        </div>
<!-- Panel derecho donde se mostrará la info -->
<div id="panel-empleado" class="bg-white shadow rounded-lg p-4"></div>

    </div>

    <!-- Paginación -->
    <div class="mt-4">
        {{ $empleados->links() }}
    </div>
</div>
@include("empleados.componentes.modalDia")



<!-- Panel lateral -->
<div id="sidebar" class="hidden fixed top-0 right-0 w-full sm:w-1/2 lg:w-1/3 h-full bg-white shadow-2xl z-50 overflow-y-auto transition-transform transform translate-x-full">
    <div class="flex justify-between items-center p-4 border-b">
        <h2 id="sidebar-title" class="text-lg font-bold text-gray-700">Panel</h2>
        <button onclick="cerrarSidebar()" class="text-gray-500 hover:text-gray-700">✖</button>
    </div>
    <div id="sidebar-content" class="p-4">
        <!-- Aquí se carga dinámicamente el contenido -->
    </div>
</div>

<script>
    function abrirSidebar(titulo, contenido) {
        document.getElementById('sidebar-title').innerHTML = titulo;
        document.getElementById('sidebar-content').innerHTML = contenido;
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.remove("hidden");
        sidebar.classList.remove("translate-x-full");
    }

    function cerrarSidebar() {
        const sidebar = document.getElementById('sidebar');
        sidebar.classList.add("translate-x-full");
        setTimeout(() => {
            sidebar.classList.add("hidden");
        }, 300);
    }

    function abrirCrearEmpleado() {
        fetch("{{ route('empleados.create') }}")
            .then(res => res.text())
            .then(html => {
                abrirSidebar("➕ Crear Empleado", html);
            });
    }

    function abrirEditarEmpleado(id) {
        fetch(`/empleados/${id}/edit`)
            .then(res => res.text())
            .then(html => {
                abrirSidebar("✏️ Editar Empleado", html);
            });
    }




</script>




<!-- Script para alternar vistas -->
<script>
    function setView(view) {
        const list = document.getElementById('view-list');
        const grid = document.getElementById('view-grid');
        const btnList = document.getElementById('btn-list');
        const btnGrid = document.getElementById('btn-grid');

        if(view === 'list') {
            list.classList.remove('hidden');
            grid.classList.add('hidden');
            btnList.classList.add('bg-blue-600','text-white');
            btnList.classList.remove('bg-gray-200');
            btnGrid.classList.remove('bg-blue-600','text-white');
            btnGrid.classList.add('bg-gray-200');
        } else {
            list.classList.add('hidden');
            grid.classList.remove('hidden');
            btnGrid.classList.add('bg-blue-600','text-white');
            btnGrid.classList.remove('bg-gray-200');
            btnList.classList.remove('bg-blue-600','text-white');
            btnList.classList.add('bg-gray-200');
        }
    }
</script>

<script>
let empleadoId = null;
let dias = [];
const opciones = [
    {valor: 1, label: 'Com', color: 'bg-yellow-200', tipo: 'dia'},
    {valor: 2, label: 'Vac', color: 'bg-green-200', tipo: 'dia'},
    {valor: 3, label: 'Tra', color: 'bg-blue-200', tipo: 'dia'},
    {valor: 4, label: 'HE', color: 'bg-purple-200', tipo: 'extra'}
];

function abrirModal(id, nombre) {
    empleadoId = id;
    document.getElementById('empleado-nombre').textContent = "Empleado: " + nombre;
    const modal = document.getElementById('modal-dia');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');

    let hoy = new Date();
    let diaActual = hoy.getDate();
    let mes = hoy.getMonth();
    let anio = hoy.getFullYear();
    let inicio = diaActual <= 14 ? 1 : 15;
    let fin = diaActual <= 14 ? 14 : new Date(anio, mes + 1, 0).getDate();

    dias = [];
    const container = document.getElementById('dias-container');
    container.innerHTML = '';

    // Traer días y extras con Promise.all
    Promise.all([
        fetch(`/dias-registrados/${empleadoId}`).then(res => res.json()),
        fetch(`/extras-registrados/${empleadoId}`).then(res => res.json())
    ]).then(([registrados, extras]) => {

        for(let i = inicio; i <= fin; i++){
            let fecha = new Date(anio, mes, i).toISOString().slice(0,10);

            let registrado = registrados.find(d => d.fecha === fecha);
            let extra = extras.find(e => e.fecha === fecha);

            let tipo = registrado ? registrado.tipo : (extra ? 4 : null);
            let label = i;
            let color = 'bg-gray-100';
            let horas = extra ? extra.cantidad : null;

            if(extra){
                label = `HE ${horas}h`;
                color = 'bg-purple-200';
            } else if(registrado){
                let opcion = opciones.find(o => o.valor == tipo);
                label = opcion.label;
                color = opcion.color;
            }

            let diaObj = {fecha: fecha, tipo: tipo, label: label, color: color, horas: horas};
            dias.push(diaObj);

            let btn = document.createElement('button');
            btn.className = `${color} rounded p-2 text-sm w-full h-12 flex items-center justify-center relative`;
            btn.textContent = label;

            let select = document.createElement('select');
            select.className = "absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer";
            select.innerHTML = `<option value="">-</option>
                                <option value="1">Compensado</option>
                                <option value="2">Vacaciones</option>
                                <option value="3">Trabajado</option>
                                <option value="4">Horas Extras</option>`;
            select.value = tipo || '';

            select.addEventListener('change', function(){
                let opcion = opciones.find(o => o.valor == this.value);
                if(opcion){
                    diaObj.tipo = opcion.valor;
                    diaObj.color = opcion.color;

                    if(opcion.tipo === 'extra'){
                        let h = parseFloat(prompt("Ingrese horas extras (decimales permitidos):"));
                        if(!isNaN(h) && h > 0){
                            diaObj.horas = h;
                            diaObj.label = `${opcion.label} ${h}h`;
                            btn.textContent = diaObj.label;
                        } else {
                            diaObj.tipo = null;
                            diaObj.horas = null;
                            diaObj.label = i;
                            diaObj.color = 'bg-gray-100';
                            btn.textContent = diaObj.label;
                        }
                    } else {
                        diaObj.horas = null;
                        diaObj.label = opcion.label;
                        btn.textContent = diaObj.label;
                    }

                    btn.className = `${diaObj.color} rounded p-2 text-sm w-full h-12 flex items-center justify-center relative`;
                }
            });

            btn.appendChild(select);
            container.appendChild(btn);
        }
    });
}

function cerrarModal(){
    const modal = document.getElementById('modal-dia');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'items-center', 'justify-center');
}

function guardarDias(){
    let payloadDias = dias.filter(d => d.tipo != null && d.tipo !== 4).map(d => ({
        empleado_id: empleadoId,
        fecha: d.fecha,
        tipo: d.tipo
    }));

    let payloadExtras = dias.filter(d => d.tipo === 4).map(d => ({
        empleado_id: empleadoId,
        fecha: d.fecha,
        cantidad: d.horas
    }));

    fetch('{{ route("dia.store") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(payloadDias)
    });

    fetch('{{ route("extras.store") }}', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': '{{ csrf_token() }}'},
        body: JSON.stringify(payloadExtras)
    }).then(res=>{
        if(res.ok){
            alert('Días y Horas Extras guardados correctamente');
            cerrarModal();
            location.reload();
        }
    });
}
</script>


<script>
    let empleadoSeleccionado = null;

    function mostrarEmpleado(empleado) {
        const panel = document.getElementById('panel-empleado');

        // Si ya está seleccionado, deselecciona
        if (empleadoSeleccionado && empleadoSeleccionado.id === empleado.id) {
            panel.innerHTML = '';
            empleadoSeleccionado = null;
            return;
        }

        empleadoSeleccionado = empleado;

        // Mostrar info principal
        panel.innerHTML = `
            <div class="flex flex-col items-center p-4">
                ${empleado.foto ? `<img src="/storage/${empleado.foto}" class="h-32 w-32 rounded-full object-cover mb-4">` :
                `<div class="h-32 w-32 rounded-full bg-gray-200 flex items-center justify-center mb-4 text-gray-400">N/A</div>`}

                <h2 class="text-xl font-bold mb-6">${empleado.nombre}</h2>

                <div class="grid grid-cols-2 gap-4 w-full mb-6">
                    <div class="space-y-2">
                        <p><span class="font-semibold">Cédula:</span> ${empleado.cedula}</p>
                        <p><span class="font-semibold">Área:</span> ${empleado.area.nombre}</p>
                        <p><span class="font-semibold">Cargo:</span> ${empleado.cargo}</p>
                    </div>
                    <div class="space-y-2">
                        <p><span class="font-semibold">Salario:</span> $${parseFloat(empleado.salario).toFixed(2)}</p>
                        <p><span class="font-semibold">Ingreso:</span> ${empleado.ingreso}</p>
                        <p><span class="font-semibold">Estado:</span> ${empleado.estado ?? 'Trabajando'}</p>
                        <p><span class="font-semibold">Activo:</span> ${empleado.activo ? 'Sí' : 'No'}</p>
                    </div>
                </div>

                <h3 class="text-lg font-semibold mb-2 w-full">Días registrados</h3>
                <div id="dias-panel" class="grid grid-cols-5 gap-2 w-full"></div>
            </div>
        `;

        // Cargar días y horas extras (solo lectura)
        const diasPanel = document.getElementById('dias-panel');
        diasPanel.innerHTML = '';

        const hoy = new Date();
        const diaActual = hoy.getDate();
        const mes = hoy.getMonth();
        const anio = hoy.getFullYear();
        const inicio = diaActual <= 15 ? 1 : 16;
        const fin = diaActual <= 15 ? 15 : new Date(anio, mes + 1, 0).getDate();

        Promise.all([
            fetch(`/dias-registrados/${empleado.id}`).then(res => res.json()),
            fetch(`/extras-registrados/${empleado.id}`).then(res => res.json())
        ]).then(([diasRegistrados, extras]) => {
            for (let i = inicio; i <= fin; i++) {
                const fecha = new Date(anio, mes, i).toISOString().slice(0,10);

                const dia = diasRegistrados.find(d => d.fecha === fecha);
                const extra = extras.find(e => e.fecha === fecha);

                let tipo = dia ? dia.tipo : (extra ? 4 : null);
                let label = i;
                let color = 'bg-gray-100';

                if(extra){
                    label = `HE ${extra.cantidad}h`;
                    color = 'bg-purple-200';
                } else if(dia){
                    const opcion = opciones.find(o => o.valor == tipo);
                    label = opcion.label;
                    color = opcion.color;
                }

                const btn = document.createElement('div');
                btn.className = `${color} rounded p-2 text-sm h-12 flex items-center justify-center`;
                btn.textContent = label;
                diasPanel.appendChild(btn);
            }
        });
    }


</script>
@endsection

let empleadoId = null;
let dias = [];

const opciones = [
    { valor: 1, label: 'Com', color: 'bg-yellow-200', tipo: 'dia' },
    { valor: 2, label: 'Vac', color: 'bg-green-200', tipo: 'dia' },
    { valor: 3, label: 'Tra', color: 'bg-blue-200', tipo: 'dia' },
    { valor: 4, label: 'HE',  color: 'bg-purple-200', tipo: 'extra' }
];

// Validación de día: Lunes-Sábado = normal, Domingo = especial
function esDomingo(fechaStr) {
    const fecha = new Date(fechaStr + 'T00:00:00');
    return fecha.getDay() === 0;
}

window.abrirModal = function (id, nombre) {
    empleadoId = id;
    document.getElementById('empleado-nombre').textContent = "Empleado: " + nombre;

    const modal = document.getElementById('modal-dia');
    modal.classList.remove('hidden');
    modal.classList.add('flex', 'items-center', 'justify-center');

    const hoy = new Date();
    const diaActual = hoy.getDate();
    const mes = hoy.getMonth();
    const anio = hoy.getFullYear();

    const inicio = diaActual <= 14 ? 1 : 15;
    const fin = diaActual <= 14 ? 14 : new Date(anio, mes + 1, 0).getDate();

    dias = [];
    const container = document.getElementById('dias-container');
    container.innerHTML = '';

    Promise.all([
        fetch(`/dias-registrados/${empleadoId}`).then(r => r.json()),
        fetch(`/extras-registrados/${empleadoId}`).then(r => r.json())
    ]).then(([registrados, extras]) => {

        // Calcular offset del primer día
        const primerDia = new Date(anio, mes, inicio);
        let offset = primerDia.getDay();
        offset = offset === 0 ? 6 : offset - 1; // Lunes=0
        for (let j = 0; j < offset; j++) {
            const empty = document.createElement('div');
            container.appendChild(empty);
        }

        for (let i = inicio; i <= fin; i++) {
            const fecha = new Date(anio, mes, i).toISOString().slice(0, 10);
            const domingo = esDomingo(fecha);

            const registrado = registrados.find(d => d.fecha === fecha);
            const extra = extras.find(e => e.fecha === fecha);

            let tipo = registrado ? registrado.tipo : (extra ? 4 : null);
            let label = i;
            let color = 'bg-gray-100';
            let horas = extra ? extra.cantidad : null;

            if (extra) {
                label = `HE ${horas}h`;
                color = 'bg-purple-200';
            } else if (registrado) {
                const op = opciones.find(o => o.valor == tipo);
                label = op.label;
                color = op.color;
            }

            const diaObj = { fecha, tipo, label, color, horas };
            dias.push(diaObj);

            const btn = document.createElement('button');

            // Resaltar domingos
            if (domingo) {
                btn.className = 'bg-red-100 text-red-600 rounded p-2 text-sm w-full h-12 flex items-center justify-center relative';
            } else {
                btn.className = `${color} rounded p-2 text-sm w-full h-12 flex items-center justify-center relative`;
            }

            btn.textContent = label;

            const select = document.createElement('select');
            select.className = "absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer";
            select.innerHTML = `
                <option value="">-</option>
                <option value="1">Compensado</option>
                <option value="2">Vacaciones</option>
                <option value="3">Trabajado</option>
                <option value="4">Horas Extras</option>
            `;
            select.value = tipo ?? '';

            select.addEventListener('change', function () {
                const op = opciones.find(o => o.valor == this.value);
                if (!op) return;

                diaObj.tipo = op.valor;
                diaObj.color = op.color;

                if (op.tipo === 'extra') {
                    const h = parseFloat(prompt('Ingrese horas extras (decimales permitidos):'));
                    if (!isNaN(h) && h > 0) {
                        diaObj.horas = h;
                        diaObj.label = `${op.label} ${h}h`;
                    } else {
                        diaObj.tipo = null;
                        diaObj.horas = null;
                        diaObj.label = i;
                        diaObj.color = 'bg-gray-100';
                    }
                } else {
                    diaObj.horas = null;
                    diaObj.label = op.label;
                }

                btn.textContent = diaObj.label;
                btn.className = domingo
                    ? 'bg-red-100 text-red-600 rounded p-2 text-sm w-full h-12 flex items-center justify-center relative'
                    : `${diaObj.color} rounded p-2 text-sm w-full h-12 flex items-center justify-center relative`;
            });

            btn.appendChild(select);
            container.appendChild(btn);
        }
    });
};

window.cerrarModal = function () {
    const modal = document.getElementById('modal-dia');
    modal.classList.add('hidden');
    modal.classList.remove('flex', 'items-center', 'justify-center');
};

window.guardarDias = function () {
    const payloadDias = dias
        .filter(d => d.tipo && d.tipo !== 4)
        .map(d => ({
            empleado_id: empleadoId,
            fecha: d.fecha,
            tipo: d.tipo
        }));

    const payloadExtras = dias
        .filter(d => d.tipo === 4)
        .map(d => ({
            empleado_id: empleadoId,
            fecha: d.fecha,
            cantidad: d.horas
        }));

    fetch(window.diaStoreUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(payloadDias)
    });

    fetch(window.extrasStoreUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': window.csrfToken
        },
        body: JSON.stringify(payloadExtras)
    }).then(r => {
        if (r.ok) {
            alert('Días y Horas Extras guardados correctamente');
            cerrarModal();
            location.reload();
        }
    });
};

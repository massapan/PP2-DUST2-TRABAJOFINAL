// Borrar productos desde "Mis productos" con un modal de confirmación.
//
// Flujo:
//  1) Click en el tachito de una card (.btn-eliminar-producto): NO borra
//     nada, solo guarda qué producto es, escribe su nombre en el modal y
//     lo abre.
//  2) Click en "Sí, eliminar" (#btnConfirmarEliminar): recién ahí se manda
//     el POST a eliminar_producto.php.
//
// Todo va delegado en 'document' (como el resto de los JS del proyecto)
// porque mis_productos.php se carga por fetch()+innerHTML y los listeners
// puestos directo en los botones se perderían.

let productoAEliminar = null; // { id, columna }

document.addEventListener('click', function (evento) {

    // ---- Paso 1: abrir el modal ----
    const botonTacho = evento.target.closest('.btn-eliminar-producto');
    if (botonTacho) {
        const modalEl = document.getElementById('modalConfirmarEliminar');
        if (!modalEl) return;

        productoAEliminar = {
            id: botonTacho.dataset.id,
            columna: botonTacho.closest('.col-6'),
        };

        modalEl.querySelector('.modal-nombre-producto').textContent = botonTacho.dataset.nombre;
        modalEl.querySelector('.modal-error-eliminar').classList.add('d-none');

        bootstrap.Modal.getOrCreateInstance(modalEl).show();
        return;
    }

    // ---- Paso 2: confirmar ----
    const botonConfirmar = evento.target.closest('#btnConfirmarEliminar');
    if (!botonConfirmar || !productoAEliminar) return;

    const modalEl = document.getElementById('modalConfirmarEliminar');
    const errorEl = modalEl.querySelector('.modal-error-eliminar');

    botonConfirmar.disabled = true; // evita doble click mientras espera

    const datos = new FormData();
    datos.append('producto_id', productoAEliminar.id);

    fetch('eliminar_producto.php', { method: 'POST', body: datos })
        .then(function (respuesta) { return respuesta.json(); })
        .then(function (data) {
            if (!data.exito) {
                errorEl.textContent = data.mensaje;
                errorEl.classList.remove('d-none');
                return;
            }

            bootstrap.Modal.getOrCreateInstance(modalEl).hide();

            // Sacamos la card del DOM sin recargar la página
            const fila = productoAEliminar.columna.parentElement;
            productoAEliminar.columna.remove();
            productoAEliminar = null;

            // Si era el último producto, mostramos el mensaje de "vacío"
            if (fila.children.length === 0) {
                const aviso = document.createElement('p');
                aviso.className = 'text-muted';
                aviso.textContent = 'Todavía no subiste ningún producto.';
                fila.replaceWith(aviso);
            }
        })
        .catch(function () {
            errorEl.textContent = 'No se pudo contactar al servidor. Probá de nuevo.';
            errorEl.classList.remove('d-none');
        })
        .finally(function () {
            botonConfirmar.disabled = false;
        });
});
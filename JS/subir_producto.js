// Envío del formulario de "Subir/Editar Producto" por fetch(), para
// mostrar el resultado en un modal en vez de navegar a una página aparte.
//
// Esta página (productos.php) es una carga completa normal, no un
// fragmento de la SPA, así que un <script> al final del body se ejecuta
// sin problema (no es el caso de innerHTML de navegacion.js).

document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('form-subir-producto');
    if (!form) return;

    const modalEl = document.getElementById('modalResultadoProducto');
    const modal = new bootstrap.Modal(modalEl);
    const titulo = document.getElementById('modalResultadoTitulo');
    const mensaje = document.getElementById('modalResultadoMensaje');

    form.addEventListener('submit', function (evento) {
        evento.preventDefault();

        const datos = new FormData(form);

        fetch('guardar_producto.php', {
            method: 'POST',
            body: datos,
        })
            .then(function (respuesta) { return respuesta.json(); })
            .then(function (data) {
                titulo.classList.remove('text-success', 'text-danger');

                if (data.exito) {
                    titulo.textContent = form.dataset.modoEdicion === '1'
                        ? '¡Cambios guardados!'
                        : '¡Producto subido con éxito!';
                    titulo.classList.add('text-success');
                    if (form.dataset.modoEdicion !== '1') {
                        form.reset();
                    }
                } else {
                    titulo.textContent = 'No se pudo subir el producto';
                    titulo.classList.add('text-danger');
                }

                mensaje.textContent = data.mensaje;
                modal.show();
            })
            .catch(function () {
                titulo.classList.remove('text-success');
                titulo.classList.add('text-danger');
                titulo.textContent = 'Error de conexión';
                mensaje.textContent = 'No se pudo contactar al servidor. Probá de nuevo.';
                modal.show();
            });
    });
});
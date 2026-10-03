// Envío de formularios por fetch() + modal de resultado, en vez de
// navegar a una página aparte. Sirve para "Subir/Editar Producto" y
// "Registrar/Editar Local" (cualquier form con data-ajax-modal).
//
// Va delegado en 'document' con el evento 'submit' (que sí burbujea),
// NO en el form por su id dentro de un DOMContentLoaded — eso solo
// funcionaría la primera vez que la página carga entera, y se rompería
// apenas esta página se cargara por fetch() dentro de la SPA.
//
// El form necesita:
//   data-ajax-modal="idDelModal"      -> qué modal mostrar con el resultado
//   data-modo-edicion="0" o "1"       -> si es "1", no resetea el form al
//                                        terminar (vas a navegar afuera)
// El modal necesita, adentro, un elemento con class="modal-resultado-titulo"
// y otro con class="modal-resultado-mensaje".

document.addEventListener('submit', function (evento) {
    const form = evento.target;
    if (!form.matches('form[data-ajax-modal]')) return;

    evento.preventDefault();

    const modalEl = document.getElementById(form.dataset.ajaxModal);
    if (!modalEl) return;

    const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
    const titulo = modalEl.querySelector('.modal-resultado-titulo');
    const mensaje = modalEl.querySelector('.modal-resultado-mensaje');

    const datos = new FormData(form);

    fetch(form.getAttribute('action'), {
        method: 'POST',
        body: datos,
    })
        .then(function (respuesta) { return respuesta.json(); })
        .then(function (data) {
            if (titulo) {
                titulo.classList.remove('text-success', 'text-danger');
                if (data.exito) {
                    titulo.textContent = form.dataset.modoEdicion === '1'
                        ? (form.dataset.tituloExitoEdicion || '¡Cambios guardados!')
                        : (form.dataset.tituloExitoNuevo || '¡Listo!');
                    titulo.classList.add('text-success');
                    if (form.dataset.modoEdicion !== '1') {
                        form.reset();
                    }
                } else {
                    titulo.textContent = 'No se pudo guardar';
                    titulo.classList.add('text-danger');
                }
            }
            if (mensaje) mensaje.textContent = data.mensaje;
            modal.show();
        })
        .catch(function () {
            if (titulo) {
                titulo.classList.remove('text-success');
                titulo.classList.add('text-danger');
                titulo.textContent = 'Error de conexión';
            }
            if (mensaje) mensaje.textContent = 'No se pudo contactar al servidor. Probá de nuevo.';
            modal.show();
        });
});
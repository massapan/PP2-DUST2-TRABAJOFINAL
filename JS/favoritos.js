// Botón de favorito (corazón) en local.php y prenda.php.
//
// Igual que en filtros.js: el botón vive dentro de un fragmento que se
// reemplaza por fetch()+innerHTML, así que el listener va delegado en
// 'document' (que nunca se destruye), no en el botón directamente.

document.addEventListener('click', function (evento) {
    const boton = evento.target.closest('.btn-favorito');
    if (!boton) return;

    evento.preventDefault();

    const tipo = boton.dataset.tipo; // 'local' | 'producto'
    const id = boton.dataset.id;

    const url = tipo === 'local' ? 'toggle_favorito_local.php' : 'toggle_favorito_producto.php';
    const campo = tipo === 'local' ? 'local_id' : 'producto_id';

    const datos = new FormData();
    datos.append(campo, id);

    fetch(url, { method: 'POST', body: datos })
        .then(function (respuesta) { return respuesta.json(); })
        .then(function (data) {
            if (data.error) {
                console.error(data.error);
                return;
            }

            const icono = boton.querySelector('i');
            if (data.favorito) {
                icono.classList.remove('bi-heart');
                icono.classList.add('bi-heart-fill', 'text-danger');
            } else {
                icono.classList.remove('bi-heart-fill', 'text-danger');
                icono.classList.add('bi-heart');
            }
        })
        .catch(function (error) {
            console.error('Error al marcar favorito:', error);
        });
});

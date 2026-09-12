// Botón de favorito (corazón), usado en catalogo.php y favoritos.php.
//
// Igual que en filtros.js: el listener va en 'document' (delegado) y no
// en cada botón, porque navegacion.js reemplaza #contenido con fetch()
// y los navegadores no ejecutan listeners agregados directo a nodos que
// ya no existen después de un innerHTML nuevo.

document.addEventListener('click', function (evento) {
    const boton = evento.target.closest('.btn-favorito');
    if (!boton) return;

    evento.preventDefault();

    const tipo = boton.dataset.tipo; // 'producto' o 'local'
    const id = boton.dataset.id;

    fetch('toggle_favorito.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'tipo=' + encodeURIComponent(tipo) + '&id=' + encodeURIComponent(id)
    })
        .then(function (respuesta) {
            if (respuesta.status === 401) {
                // No hay sesión: mandamos a login.
                window.location.href = 'login.php';
                return null;
            }
            return respuesta.json();
        })
        .then(function (data) {
            if (!data || !data.ok) return;

            const icono = boton.querySelector('i');

            // Si el botón está en favoritos.php (data-contexto="favoritos")
            // y se acaba de desmarcar, sacamos la tarjeta entera de la vista.
            if (boton.dataset.contexto === 'favoritos' && !data.favorito) {
                const tarjeta = boton.closest('.tarjeta-producto, .tarjeta-local');
                if (tarjeta) {
                    tarjeta.remove();
                }
                mostrarEstadoVacioSiCorresponde();
                return;
            }

            // En cualquier otro contexto (ej: catalogo.php), solo
            // actualizamos el ícono del corazón.
            if (icono) {
                icono.classList.toggle('bi-heart-fill', data.favorito);
                icono.classList.toggle('bi-heart', !data.favorito);
                icono.classList.toggle('text-danger', data.favorito);
            }
        })
        .catch(function (error) {
            console.error('Error al actualizar favorito:', error);
        });
});

// Si sacamos la última tarjeta de una grilla de favoritos, mostramos
// de nuevo el mensaje de "no tenés favoritos".
function mostrarEstadoVacioSiCorresponde() {
    const grillaProductos = document.getElementById('grillaFavProductos');
    if (grillaProductos && !grillaProductos.querySelector('.tarjeta-producto') && !document.getElementById('sinFavProductos')) {
        const vacio = document.createElement('div');
        vacio.className = 'col-12 text-center py-4';
        vacio.id = 'sinFavProductos';
        vacio.innerHTML = '<p class="fs-6 text-muted">Todavía no tenés productos marcados como favoritos.</p>';
        grillaProductos.appendChild(vacio);
    }

    const grillaLocales = document.getElementById('grillaFavLocales');
    if (grillaLocales && !grillaLocales.querySelector('.tarjeta-local') && !document.getElementById('sinFavLocales')) {
        const vacio = document.createElement('div');
        vacio.className = 'col-12 text-center py-4';
        vacio.id = 'sinFavLocales';
        vacio.innerHTML = '<p class="fs-6 text-muted">Todavía no tenés locales marcados como favoritos.</p>';
        grillaLocales.appendChild(vacio);
    }
}

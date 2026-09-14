// Navegación de la SPA: fetch()+innerHTML para no recargar la página,
// + History API para que las flechitas de atrás/adelante funcionen
// DENTRO de la SPA en vez de sacarte de index.php.
//
// Cada "página" es una URL real y completa: index.php?pagina=X(&id=Y).
// Si el navegador alguna vez necesita cargar esa URL de verdad (F5,
// algún comportamiento raro del botón atrás, etc.), index.php sabe
// armar la página completa con header/footer -- nunca se ve un
// fragmento pelado ni se termina en una URL vieja sin sentido.
//
// Para diferenciar "esto es un fetch de la SPA" de "esto es una carga
// de verdad", mandamos el header X-Requested-With, que index.php revisa
// del lado del servidor.

function cargarContenido(url) {
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (respuesta) { return respuesta.text(); })
        .then(function (html) {
            document.getElementById('contenido').innerHTML = html;
            actualizarNavActivo(url);
            window.scrollTo(0, 0);
        })
        .catch(function (error) {
            console.error('Error al cargar la página:', error);
        });
}

// Saca el valor de ?pagina= de una URL (relativa o absoluta), resolviéndola
// contra la ubicación actual para que funcione tanto si el sitio vive en
// la raíz del dominio como si vive en una subcarpeta (típico de XAMPP).
function obtenerPagina(url) {
    try {
        return new URL(url, location.href).searchParams.get('pagina') || 'catalogo';
    } catch (e) {
        return 'catalogo';
    }
}

// Resalta en el menú de arriba el link cuya "pagina" coincida con la
// que se acaba de cargar. Se recalcula siempre: tanto al hacer click
// como al volver con la flechita de atrás.
function actualizarNavActivo(url) {
    const pagina = obtenerPagina(url);

    document.querySelectorAll('.nav-pills .nav-link').forEach(function (link) {
        const paginaLink = obtenerPagina(link.getAttribute('href') || '');
        const esActivo = paginaLink === pagina;

        link.classList.toggle('active', esActivo);
        if (esActivo) {
            link.setAttribute('aria-current', 'page');
        } else {
            link.removeAttribute('aria-current');
        }
    });
}

// Guardamos como "estado inicial" la URL con la que index.php ya cargó
// de entrada, sin cambiar nada en la barra de direcciones.
history.replaceState({ url: location.pathname + location.search }, '', location.href);

document.addEventListener('click', function (evento) {

    // Botón "Página anterior" (local.php / prenda.php): como ahora sí
    // existe historial de verdad, alcanza con volver un paso atrás.
    const botonVolver = evento.target.closest('.volver-atras');
    if (botonVolver) {
        evento.preventDefault();
        history.back();
        return;
    }

    // a.nav-link -> los links del menú de arriba (Mapa/Catálogo/Favoritos)
    // a.enlace-interno -> cualquier otro link que navegue "dentro" de la SPA
    //                     (ej: nombre de producto o de local en una card)
    const link = evento.target.closest('a.nav-link, a.enlace-interno');
    if (!link) return;

    // Los links marcados como "recarga completa" (ej: Mis Productos, Mi
    // Local, que son páginas HTML completas y no fragmentos) no deben
    // pasar por fetch()+innerHTML: dejamos que el navegador los abra
    // normalmente, como si esto ni existiera.
    if (link.hasAttribute('data-recarga-completa')) return;

    const url = link.getAttribute('href');
    if (!url || url === '#') return;

    evento.preventDefault();

    history.pushState({ url: url }, '', url);
    cargarContenido(url);
});

// Se dispara cuando el usuario toca atrás/adelante en el navegador.
window.addEventListener('popstate', function (evento) {
    const url = (evento.state && evento.state.url) ? evento.state.url : 'index.php?pagina=catalogo';
    cargarContenido(url);
});
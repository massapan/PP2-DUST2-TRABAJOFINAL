// Navegación de la SPA: fetch()+innerHTML para no recargar la página,
// + History API para que las flechitas de atrás/adelante del navegador
// funcionen DENTRO de la SPA en vez de sacarte de index.php.

// Carga un fragmento en #contenido y, si corresponde, resalta el link
// del menú superior que coincida con esa URL.
function cargarContenido(url) {
    fetch(url)
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

// El resaltado de "activo" en el menú de arriba se recalcula siempre
// (tanto al hacer click como al volver con la flechita de atrás),
// comparando el archivo (sin el ?id=...) contra los href del menú.
function actualizarNavActivo(url) {
    const archivo = url.split('?')[0];

    document.querySelectorAll('.nav-pills .nav-link').forEach(function (link) {
        const archivoLink = (link.getAttribute('href') || '').split('?')[0];
        const esActivo = archivoLink === archivo;

        link.classList.toggle('active', esActivo);
        if (esActivo) {
            link.setAttribute('aria-current', 'page');
        } else {
            link.removeAttribute('aria-current');
        }
    });
}

// Guardamos como "estado inicial" lo que index.php ya cargó de entrada
// (catalogo.php), sin cambiar la URL que se ve en la barra.
history.replaceState({ url: 'catalogo.php' }, '', location.href);

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
    const url = (evento.state && evento.state.url) ? evento.state.url : 'catalogo.php';
    cargarContenido(url);
});
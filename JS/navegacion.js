document.addEventListener('click', function(evento) {

    // a.nav-link -> los links del menú de arriba (Mapa/Catálogo/Favoritos)
    // a.enlace-interno -> cualquier otro link que navegue "dentro" de la SPA
    //                     (ej: nombre de producto o de local en una card)
    const link = evento.target.closest('a.nav-link, a.enlace-interno');
    if (!link) return;

    const url = link.getAttribute('href');
    if (!url || url === '#') return;

    evento.preventDefault();

    // El resaltado de "activo" solo tiene sentido para el menú superior
    if (link.classList.contains('nav-link')) {
        document.querySelectorAll('.nav-pills .nav-link').forEach(function(otroLink) {
            otroLink.classList.remove('active');
            otroLink.removeAttribute('aria-current');
        });
        link.classList.add('active');
        link.setAttribute('aria-current', 'page');
    }

    fetch(url)
        .then(function(respuesta) {
            return respuesta.text();
        })
        .then(function(html) {
            document.getElementById('contenido').innerHTML = html;
        })
        .catch(function(error) {
            console.error('Error al cargar la página:', error);
        });

});
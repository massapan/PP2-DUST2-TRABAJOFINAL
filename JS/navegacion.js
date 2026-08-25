document.addEventListener('click', function(evento) {

    const link = evento.target.closest('a.nav-link');
    if (!link) return;

    const url = link.getAttribute('href');
    if (!url || url === '#') return;

    evento.preventDefault();

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
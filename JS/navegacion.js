document.addEventListener('click', function(evento) {

    const link = evento.target.closest('a.nav-link');
    if (!link) return;

    const url = link.getAttribute('href');
    if (!url || url === '#') return;

    evento.preventDefault();

    // Sacamos "active" de todos los links del menú
    document.querySelectorAll('.nav-pills .nav-link').forEach(function(otroLink) {
        otroLink.classList.remove('active');
        otroLink.removeAttribute('aria-current');
    });

    // Se la ponemos solo al que se clickeó
    link.classList.add('active');
    link.setAttribute('aria-current', 'page');

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
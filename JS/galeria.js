// Galería de fotos estilo "miniaturas" (local.php y prenda.php).
//
// Delegado en document, como filtros.js y favoritos.js: las miniaturas
// viven dentro de un fragmento que navegacion.js reemplaza por
// fetch()+innerHTML, así que un listener puesto directo en cada <img>
// se perdería en la próxima navegación.

document.addEventListener('click', function (evento) {
    const miniatura = evento.target.closest('.miniatura-galeria');
    if (!miniatura) return;

    const fotoGrande = document.getElementById(miniatura.dataset.target);
    if (!fotoGrande) return;

    fotoGrande.src = miniatura.dataset.src;

    // Sacamos "activa" de las miniaturas DE ESTA MISMA galería
    // (buscamos el contenedor padre en vez de todo el documento, por si
    // el día de mañana hay dos galerías en la misma pantalla).
    const contenedor = miniatura.parentElement;
    contenedor.querySelectorAll('.miniatura-galeria').forEach(function (img) {
        img.classList.remove('activa');
    });
    miniatura.classList.add('activa');
});
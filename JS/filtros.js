// Filtro de la sidebar de catálogo/favoritos.
//
// IMPORTANTE: este listener está en 'document' (delegado), no en el botón
// directamente. Esto es necesario porque navegacion.js reemplaza el
// contenido de #contenido con fetch()+innerHTML cada vez que navegás, y
// los navegadores NO ejecutan <script> insertados por innerHTML. Si este
// código estuviera dentro de catalogo.php, funcionaría la primera carga
// y se rompería en cuanto volvieras a entrar a Catálogo por el menú.
// Delegando en 'document' (que nunca se destruye) el listener sigue vivo
// sin importar cuántas veces se reemplace el HTML de adentro.

document.addEventListener('click', function (evento) {
    const boton = evento.target.closest('#btnAplicarFiltros');
    if (!boton) return;

    aplicarFiltrosProductos();
});

function aplicarFiltrosProductos() {
    const grilla = document.getElementById('grillaProductos');
    if (!grilla) return; // estamos en una página sin catálogo (ej: mapa)

    // 1. Categorías tildadas
    const categoriasElegidas = Array.from(
        document.querySelectorAll('.filtro-categoria:checked')
    ).map(function (checkbox) { return checkbox.value; });

    // 2. Orden por precio (radio button)
    const radioOrden = document.querySelector('input[name="ordenPrecio"]:checked');
    const orden = radioOrden ? radioOrden.value : null;

    // 3. Rango de precio
    const inputMin = document.getElementById('precioMin');
    const inputMax = document.getElementById('precioMax');
    const precioMin = (inputMin && inputMin.value !== '') ? parseFloat(inputMin.value) : null;
    const precioMax = (inputMax && inputMax.value !== '') ? parseFloat(inputMax.value) : null;

    const tarjetas = Array.from(grilla.querySelectorAll('.tarjeta-producto'));
    let visibles = 0;

    tarjetas.forEach(function (tarjeta) {
        const categoria = tarjeta.dataset.categoria;
        const precio = parseFloat(tarjeta.dataset.precio);
        let mostrar = true;

        if (categoriasElegidas.length > 0 && !categoriasElegidas.includes(categoria)) {
            mostrar = false;
        }
        if (precioMin !== null && precio < precioMin) {
            mostrar = false;
        }
        if (precioMax !== null && precio > precioMax) {
            mostrar = false;
        }

        tarjeta.classList.toggle('d-none', !mostrar);
        if (mostrar) visibles++;
    });

    // Reordenamos solo las tarjetas visibles, moviéndolas dentro del mismo contenedor
    if (orden) {
        const visiblesOrdenadas = tarjetas
            .filter(function (t) { return !t.classList.contains('d-none'); })
            .sort(function (a, b) {
                const precioA = parseFloat(a.dataset.precio);
                const precioB = parseFloat(b.dataset.precio);
                return orden === 'asc' ? precioA - precioB : precioB - precioA;
            });

        visiblesOrdenadas.forEach(function (tarjeta) {
            grilla.appendChild(tarjeta);
        });
    }

    const mensajeVacio = document.getElementById('sinResultadosFiltro');
    if (mensajeVacio) {
        mensajeVacio.classList.toggle('d-none', visibles > 0);
    }
}
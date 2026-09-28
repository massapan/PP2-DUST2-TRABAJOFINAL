// Vista previa de imágenes ANTES de subir, en los formularios de
// "Registrar/Editar Local" y "Subir/Editar Producto".
//
// El evento "change" de un <input> sí burbujea (a diferencia de otros
// eventos de formulario), así que podemos delegarlo en document como
// venimos haciendo con el resto de los listeners del proyecto.

document.addEventListener('change', function (evento) {
    const input = evento.target;
    if (!input.matches('input[type="file"]')) return;
    if (!input.files || input.files.length === 0) return;

    // El <label for="..."> que abre el selector de archivos es hermano
    // del <input> dentro de la misma caja (#imagen-local-prenda o
    // #mas-imagenes-local) — lo insertamos DENTRO del label para que
    // la imagen elegida se pueda seguir clickeando para cambiarla.
    const caja = input.closest('div');
    if (!caja) return;

    const label = caja.querySelector('label');
    if (!label) return;

    const icono = label.querySelector('i');
    const texto = label.querySelector('p');

    if (input.multiple) {
        // "Más fotos": mostramos una miniatura por cada archivo elegido.
        let fila = label.querySelector('.preview-fila-miniaturas');
        if (!fila) {
            fila = document.createElement('div');
            fila.className = 'preview-fila-miniaturas d-flex flex-wrap justify-content-center';
            label.appendChild(fila);
        }
        fila.innerHTML = '';

        Array.from(input.files).forEach(function (archivo) {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(archivo);
            img.className = 'preview-miniatura';
            fila.appendChild(img);
        });

        if (texto) {
            const cantidad = input.files.length;
            texto.textContent = cantidad === 1 ? '1 foto elegida' : cantidad + ' fotos elegidas';
        }
    } else {
        // Foto principal / portada: una sola vista previa grande.
        let preview = label.querySelector('.preview-principal');
        if (!preview) {
            preview = document.createElement('img');
            preview.className = 'preview-principal';
            label.insertBefore(preview, label.firstChild);
        }
        preview.src = URL.createObjectURL(input.files[0]);

        if (icono) icono.style.display = 'none';
        if (texto) texto.textContent = 'Cambiar foto';
    }
});
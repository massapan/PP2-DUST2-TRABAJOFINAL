/* ============================================================
   Boton "ver contrasena" para los campos de password
   ============================================================
   Se aplica solo a todos los <input type="password"> de la pagina,
   asi no hay que tocar el HTML de cada formulario ni acordarse de
   agregarlo cuando se sumen campos nuevos.

   Los estilos (.campo-password y .ver-password) estan en style.css.
   ============================================================ */

/* Iconos en SVG inline en vez de una libreria de iconos: estas paginas
   no cargan nada externo, asi siguen andando sin internet y sin pedir
   un archivo mas. currentColor hace que tomen el color del boton. */

// Ojo abierto: se muestra cuando la contrasena esta OCULTA,
// porque tocarlo es la accion de "mostrar".
var ICONO_OJO_ABIERTO =
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"' +
    ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
    '<path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"/>' +
    '<circle cx="12" cy="12" r="3"/>' +
    '</svg>';

// Ojo cerrado con pestanas: se muestra cuando la contrasena esta VISIBLE,
// porque tocarlo es la accion de "ocultar".
var ICONO_OJO_CERRADO =
    '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"' +
    ' stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' +
    '<path d="M2 9c2.5 3.5 6 5.5 10 5.5S19.5 12.5 22 9"/>' +
    '<path d="M4 13.5 2.5 16"/>' +
    '<path d="M8 15.5 7 18"/>' +
    '<path d="M12 16v2.5"/>' +
    '<path d="M16 15.5 17 18"/>' +
    '<path d="M20 13.5 21.5 16"/>' +
    '</svg>';


document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('input[type="password"]').forEach(function (input) {

        // Envoltorio con position:relative para poder ubicar el boton
        // adentro del campo, sobre el borde derecho.
        var envoltorio = document.createElement('div');
        envoltorio.className = 'campo-password';
        input.parentNode.insertBefore(envoltorio, input);
        envoltorio.appendChild(input);

        var boton = document.createElement('button');
        // type="button" es obligatorio: sin esto, al estar dentro del
        // <form>, el navegador lo toma como submit y envia el registro.
        boton.type = 'button';
        boton.className = 'ver-password';
        // No tiene que entrar en el recorrido con Tab entre los campos:
        // se llega igual, pero despues del boton de enviar.
        boton.tabIndex = -1;
        envoltorio.appendChild(boton);

        // Deja el boton acorde al estado actual del campo. Como el icono
        // no tiene texto, el aria-label es la unica pista para quien use
        // un lector de pantalla: no es opcional.
        function pintarBoton() {
            var visible = input.type === 'text';
            boton.innerHTML = visible ? ICONO_OJO_CERRADO : ICONO_OJO_ABIERTO;
            boton.setAttribute(
                'aria-label',
                visible ? 'Ocultar contrasena' : 'Mostrar contrasena'
            );
            boton.setAttribute('title', visible ? 'Ocultar contrasena' : 'Mostrar contrasena');
            boton.setAttribute('aria-pressed', visible ? 'true' : 'false');
        }

        pintarBoton();

        boton.addEventListener('click', function () {
            input.type = input.type === 'text' ? 'password' : 'text';
            pintarBoton();

            // Devuelve el foco al campo para poder seguir escribiendo
            // sin tener que volver a hacer clic.
            input.focus();
        });
    });
});

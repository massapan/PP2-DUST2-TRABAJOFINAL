/* ============================================================
   Boton "Mostrar / Ocultar" para los campos de contrasena
   ============================================================
   Se aplica solo a todos los <input type="password"> de la pagina,
   asi no hay que tocar el HTML de cada formulario ni acordarse de
   agregarlo cuando se sumen campos nuevos.

   Los estilos (.campo-password y .ver-password) estan en style.css.
   ============================================================ */

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
        boton.textContent = 'Mostrar';
        boton.setAttribute('aria-label', 'Mostrar contrasena');
        boton.setAttribute('aria-pressed', 'false');
        // No tiene que entrar en el recorrido con Tab entre los campos:
        // se llega igual, pero despues del boton de enviar.
        boton.tabIndex = -1;
        envoltorio.appendChild(boton);

        boton.addEventListener('click', function () {
            var estabaVisible = input.type === 'text';

            input.type = estabaVisible ? 'password' : 'text';
            boton.textContent = estabaVisible ? 'Mostrar' : 'Ocultar';
            boton.setAttribute(
                'aria-label',
                estabaVisible ? 'Mostrar contrasena' : 'Ocultar contrasena'
            );
            boton.setAttribute('aria-pressed', estabaVisible ? 'false' : 'true');

            // Devuelve el foco al campo para poder seguir escribiendo
            // sin tener que volver a hacer clic.
            input.focus();
        });
    });
});

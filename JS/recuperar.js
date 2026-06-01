const recuperarForm = document.getElementById("recuperarForm");

recuperarForm.addEventListener("submit", function(e){

    e.preventDefault();

    const mensaje = document.getElementById("mensaje");

    mensaje.textContent =
    "Se envió un enlace de recuperación al correo.";

    mensaje.style.color = "green";

});
const loginForm = document.getElementById("loginForm");

loginForm.addEventListener("submit", function(e){

    e.preventDefault();

    const email = document.getElementById("email").value;
    const password = document.getElementById("password").value;

    const mensaje = document.getElementById("mensaje");

    if(email === "" || password === ""){
        mensaje.textContent = "Complete todos los campos";
        mensaje.style.color = "red";
        return;
    }

    mensaje.textContent = "Inicio de sesión exitoso";
    mensaje.style.color = "green";

    // Más adelante redirigirá al panel principal
    // window.location.href = "dashboard.html";

});
const localForm = document.getElementById("localForm");

localForm.addEventListener("submit", function(e){

    e.preventDefault();

    const nombre = document.getElementById("nombreLocal").value;
    const direccion = document.getElementById("direccion").value;
    const descripcion = document.getElementById("descripcion").value;

    const mensaje = document.getElementById("mensaje");

    if(
        nombre === "" ||
        direccion === "" ||
        descripcion === ""
    ){
        mensaje.textContent = "Complete todos los campos";
        mensaje.style.color = "red";
        return;
    }

    mensaje.textContent = "Local registrado correctamente";
    mensaje.style.color = "green";

});
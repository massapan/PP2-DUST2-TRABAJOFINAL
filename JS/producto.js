const productoForm = document.getElementById("productoForm");

productoForm.addEventListener("submit", function(e){

    e.preventDefault();

    const nombre = document.getElementById("nombreProducto").value;
    const precio = document.getElementById("precio").value;
    const categoria = document.getElementById("categoria").value;

    const mensaje = document.getElementById("mensaje");

    if(
        nombre === "" ||
        precio === "" ||
        categoria === ""
    ){
        mensaje.textContent = "Complete todos los campos";
        mensaje.style.color = "red";
        return;
    }

    mensaje.textContent = "Producto publicado correctamente";
    mensaje.style.color = "green";

});
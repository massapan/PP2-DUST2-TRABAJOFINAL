<?php
ob_start();
session_start();

// Validación de seguridad: Solo vendedores pueden cargar productos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cargar Producto - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <h2>Subir Nuevo Producto</h2>
        <p>Completá los detalles y subí una buena foto de tu artículo.</p>
<br>
        <form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
            
            <b><label for="nombre_producto">Nombre del Producto:</label></b>
            <input type="text" id="nombre_producto" name="nombre_producto" required>

            <b><label for="precio">Precio ($):</label></b>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <b><label for="imagen">Imagen del Producto:</label></b >
            <input type="file" id="imagen" name="imagen" accept="image/*" required>

            <button type="submit">Subir Producto</button>

        </form>

        <br>
        

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
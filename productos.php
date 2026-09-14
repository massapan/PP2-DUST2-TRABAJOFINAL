<?php
ob_start();
session_start();

// Validación de seguridad: Solo vendedores pueden cargar productos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$categorias = $conexion->query("SELECT id, nombre FROM categorias_producto ORDER BY nombre ASC");
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cargar Producto - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-centrada">
    <div class="fondo"></div>
    <div class="container">
    <div class="caja-auth">
        
        <h2>Subir Nuevo Producto</h2>
        <p>Completá los detalles y subí una buena foto de tu artículo.</p>
<br>
        <?php if (isset($_GET['error']) && $_GET['error'] == 'sin_local'): ?>
            <p style="color:red; font-weight:bold;">No tenés un local registrado. Registrá uno antes de subir productos.</p>
        <?php endif; ?>

        <form action="guardar_producto.php" method="POST" enctype="multipart/form-data">
            
            <b><label for="nombre_producto">Nombre del Producto:</label></b>
            <input type="text" id="nombre_producto" name="nombre_producto" required>

            <b><label for="descripcion">Descripción:</label></b>
            <textarea id="descripcion" name="descripcion" rows="4" placeholder="Tela, talles disponibles, estado, etc."></textarea>

            <b><label for="categoria_id">Categoría:</label></b>
            <select id="categoria_id" name="categoria_id" required>
                <option value="" disabled selected>Seleccioná una categoría</option>
                <?php while ($cat = $categorias->fetch_assoc()): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                <?php endwhile; ?>
            </select>

            <b><label for="precio">Precio ($):</label></b>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <b><label for="imagen">Imagen principal:</label></b>
            <input type="file" id="imagen" name="imagen" accept="image/*" required>

            <b><label for="imagenes_adicionales">Más imágenes (opcional):</label></b>
            <input type="file" id="imagenes_adicionales" name="imagenes_adicionales[]" accept="image/*" multiple>
            <p style="font-size:12px; color:#666; margin-top:-10px;">
                Podés seleccionar varias a la vez. El orden en que las elijas es el orden en que se van a mostrar
                (después de la imagen principal).
            </p>

            <button type="submit">Subir Producto</button>

        </form>

        <br>
        

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
<?php
session_start();
include 'conexion.php';

// Traemos los productos de la base de datos junto con el nombre de su respectivo local
$sql = "SELECT p.nombre_producto, p.precio, p.imagen_ruta, l.nombre_local 
        FROM productos p 
        INNER JOIN locales l ON p.local_id = l.id
        ORDER BY p.creado_en DESC";

$resultado = $conexion->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catálogo de Productos</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header class="d-flex flex-wrap justify-content-center py-3 mb-4 border-bottom Header">
        <a href="/" class="d-flex align-items-center mb-3 mb-md-0 me-md-auto link-body-emphasis text-decoration-none">
            <span class="fs-4">Logo y nombre</span>
        </a>
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="#" class="nav-link active" aria-current="page">Mapa</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Catálogo</a></li>
            <li class="nav-item"><a href="#" class="nav-link">Favoritos</a></li>
            <li class="nav-item"><a href="#" class="nav-link">???</a></li>
            <li class="nav-item"><a href="#" class="nav-link">???</a></li>
        </ul>
    </header>

    <div class="fondo"></div>
    <div class="contenedor-catalogo">
        
        <h2>Catálogo de Productos</h2>
        <br>
        <p>Mirá los productos disponibles en los comercios de tu zona.</p>
        <br>

        <div class="productos-grid" style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: center;">
            
            <?php 
            if ($resultado && $resultado->num_rows > 0): 
                while($producto = $resultado->fetch_assoc()): 
            ?>
                
                <div class="tarjeta-producto" style="border: 1px solid #126954; padding: 15px; border-radius: 8px; width: 250px; background: #e0e0e0; box-shadow: 0 5px 5px rgba(0, 0, 0, 0.66); text-align: center;">
                    
                    <img src="<?php echo $producto['imagen_ruta']; ?>" alt="<?php echo $producto['nombre_producto']; ?>" style="width: 100%; height: 200px; object-fit: cover; border-radius: 4px; margin-bottom: 10px;">
                    
                    <h3 style="margin: 10px 0 5px 0; font-size: 1.2em;"><?php echo $producto['nombre_producto']; ?></h3>
                    
                    <p class="precio" style="font-weight: bold; color: #28a745; margin: 5px 0; font-size: 1.1em;">
                        $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                    </p>
                    
                    <p class="local-origen" style="font-size: 0.9em; color: #666; font-style: italic; margin-top: 5px;">
                        Local: <?php echo $producto['nombre_local']; ?>
                    </p>
                    
                </div>

            <?php 
                endwhile; 
            else: 
            ?>
                <div style="width: 100%; text-align: center; padding: 40px 0;">
                    <p style="font-size: 1.2em; color: #777;">Todavía no hay productos publicados. ¡Sé el primero en subir uno!</p>
                </div>
            <?php 
            endif; 
            $conexion->close();
            ?>

        </div>

    </div>

</body>
</html>
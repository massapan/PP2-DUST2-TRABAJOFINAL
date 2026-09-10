<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';
 
// Traemos los productos junto con su local y su categoría (para poder filtrarlos en el JS).
$sql = "SELECT p.id, p.nombre_producto, p.precio, l.nombre_local, c.nombre AS categoria_nombre,
               (SELECT ip.ruta FROM imagenes_producto ip
                WHERE ip.producto_id = p.id
                ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
        FROM productos p 
        INNER JOIN locales l ON p.local_id = l.id
        LEFT JOIN categorias_producto c ON p.categoria_id = c.id
        ORDER BY p.creado_en DESC";
 
$resultado = $conexion->query($sql);
?>

 
<div class="fondo"></div>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 border-end bg-white">
            <?php include 'sidebar_prendas_locales.php'; ?>
        </aside>
        <div class="col-md-9 my-3">
            <div class="contenedor-catalogo">
                <h2>Catálogo de Productos</h2>
                <p>Mirá los productos disponibles en los comercios de tu zona.</p>

                <div id="grillaProductos" class="row g-3">
                    <?php
                    if ($resultado && $resultado->num_rows > 0):
                        while ($producto = $resultado->fetch_assoc()):
                            $categoriaSlug = $producto['categoria_nombre'] ? strtolower($producto['categoria_nombre']) : '';
                    ?>
                        <div class="col-6 col-lg-4 tarjeta-producto"
                             data-categoria="<?php echo htmlspecialchars($categoriaSlug); ?>"
                             data-precio="<?php echo $producto['precio']; ?>">
                            <div class="card h-100">
                                <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                     class="card-img-top" style="height:200px; object-fit:cover;"
                                     alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                <div class="card-body text-center">
                                    <h3 class="h6"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                    <p class="fw-bold text-success mb-1">
                                        $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                    </p>
                                    <p class="text-muted small fst-italic mb-0">
                                        Local: <?php echo htmlspecialchars($producto['nombre_local']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <div class="col-12 text-center py-5">
                            <p class="fs-5 text-muted">Todavía no hay productos publicados. ¡Sé el primero en subir uno!</p>
                        </div>
                    <?php
                    endif;
                    $conexion->close();
                    ?>
                </div>
                <p id="sinResultadosFiltro" class="text-center text-muted py-4 d-none">
                    Ningún producto coincide con los filtros seleccionados.
                </p>
            </div>
        </div>
    </div>
</div>
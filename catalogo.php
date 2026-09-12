<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';
 
// Traemos los productos junto con su local y su categoría (para poder filtrarlos en el JS).
$sql = "SELECT p.id, p.nombre_producto, p.precio, l.id AS local_id, l.nombre_local, c.nombre AS categoria_nombre,
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
        <!-- Boton de filtros: solo en mobile. En desktop el panel va siempre
             visible, asi que este boton se oculta con d-md-none. -->
        <div class="col-12 d-md-none pt-3">
            <button class="btn btn-success w-100" type="button"
                    data-bs-toggle="collapse" data-bs-target="#panelFiltros"
                    aria-expanded="false" aria-controls="panelFiltros">
                <i class="bi bi-funnel"></i> Filtros
            </button>
        </div>
        <!-- collapse lo oculta en mobile hasta que se toca el boton;
             d-md-block lo fuerza visible de 768px para arriba. -->
        <aside class="col-md-2 border-end bg-white collapse d-md-block" id="panelFiltros">
            <?php include 'sidebar_prendas_locales.php'; ?>
        </aside>
        <div class="col-md-10 my-3">
            <div class="contenedor-catalogo">
                <h2>Catálogo de Productos</h2>

                <div id="grillaProductos" class="row g-3">
                    <?php
                    if ($resultado && $resultado->num_rows > 0):
                        while ($producto = $resultado->fetch_assoc()):
                            $categoriaSlug = $producto['categoria_nombre'] ? strtolower($producto['categoria_nombre']) : '';
                    ?>
                        <div class="col-6 col-lg-3 tarjeta-producto"
                             data-categoria="<?php echo htmlspecialchars($categoriaSlug); ?>"
                             data-precio="<?php echo $producto['precio']; ?>">
                            <div class="card h-100">
                                <a href="prenda.php?id=<?php echo $producto['id']; ?>"
                                   class="enlace-interno enlace-producto d-block text-decoration-none text-dark">
                                    <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                         class="card-img-top" style="height:200px; object-fit:cover;"
                                         alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                    <div class="card-body text-center pb-0">
                                        <h3 class="h6 text-start" ><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                        <p class="fw-bold text-success mb-1 text-start ps-0">
                                            $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                        </p>
                                    </div>
                                </a>
                                <div class="card-body pt-0 text-center">
                                    <a href="local.php?id=<?php echo $producto['local_id']; ?>"
                                       class="enlace-interno enlace-local text-muted small fst-italic text-start d-block text-decoration-none">
                                        Local: <?php echo htmlspecialchars($producto['nombre_local']); ?>
                                    </a>
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
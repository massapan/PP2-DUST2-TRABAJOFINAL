<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$logueado = isset($_SESSION['usuario_id']);
$favoritos_producto_ids = [];

// Si hay sesión, traemos los ids de productos que el usuario ya
// marcó como favoritos, para pintar el corazón lleno desde el arranque.
if ($logueado) {
    $usuario_id = $_SESSION['usuario_id'];
    $sql_fav = "SELECT producto_id FROM favoritos_productos WHERE usuario_id = ?";
    $stmt_fav = $conexion->prepare($sql_fav);
    $stmt_fav->bind_param("i", $usuario_id);
    $stmt_fav->execute();
    $resultado_fav = $stmt_fav->get_result();
    while ($fila_fav = $resultado_fav->fetch_assoc()) {
        $favoritos_producto_ids[] = (int) $fila_fav['producto_id'];
    }
    $stmt_fav->close();
}

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
        
        <aside class="col-md-2 border-end pa bg-white position-sticky align-self-start "style="top: 0; padding-top: 1rem;" id="panelFiltros">
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
                            $esFavorito = in_array((int) $producto['id'], $favoritos_producto_ids, true);
                    ?>
                        <div class="col-6 col-lg-3 tarjeta-producto"
                             data-categoria="<?php echo htmlspecialchars($categoriaSlug); ?>"
                             data-precio="<?php echo $producto['precio']; ?>">
                            <div class="card h-100 position-relative">
                                <a href="index.php?pagina=prenda&id=<?php echo $producto['id']; ?>"
                                   class="enlace-interno text-decoration-none text-dark stretched-link">
                                    <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                         class="card-img-top" style="height:200px; object-fit:cover;"
                                         alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                    <div class="card-body text-center pb-0">
                                        <h3 class="h6 text-start" ><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                    </div>
                                </a>
                                <div class="card-body pt-0">
                                    <a href="index.php?pagina=local&id=<?php echo $producto['local_id']; ?>"
                                       class="enlace-interno position-relative text-muted small fst-italic text-start d-block text-decoration-none"
                                       style="z-index: 2;">
                                        Local: <?php echo htmlspecialchars($producto['nombre_local']); ?>
                                    </a>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <p class="fw-bold text-success mb-0 fs-5">
                                            $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                        </p>
                                        <button type="button"
                                                class="btn-favorito-inferior position-relative <?php echo $esFavorito ? 'activo' : ''; ?>"
                                                style="z-index: 2;"
                                                data-tipo="producto"
                                                data-id="<?php echo $producto['id']; ?>"
                                                aria-label="Marcar como favorito">
                                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                                <line class="linea-tacha" x1="2" y1="2" x2="22" y2="22"></line>
                                            </svg>
                                        </button>
                                    </div>
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
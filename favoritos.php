<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logueado = isset($_SESSION['usuario_id']);

$locales_favoritos = [];
$productos_favoritos = [];

if ($logueado) {
    include 'conexion.php';
    $usuario_id = $_SESSION['usuario_id'];

    // ---- Locales favoritos del usuario logueado ----
    $sql_locales = "SELECT l.id, l.nombre_local, l.direccion, l.descripcion,
                            cl.nombre AS categoria_nombre
                     FROM favoritos_locales fl
                     INNER JOIN locales l ON fl.local_id = l.id
                     LEFT JOIN categorias_local cl ON l.categoria_id = cl.id
                     WHERE fl.usuario_id = ?
                     ORDER BY fl.creado_en DESC";
    $stmt_locales = $conexion->prepare($sql_locales);
    $stmt_locales->bind_param("i", $usuario_id);
    $stmt_locales->execute();
    $resultado_locales = $stmt_locales->get_result();
    $locales_favoritos = $resultado_locales->fetch_all(MYSQLI_ASSOC);
    $stmt_locales->close();

    // ---- Productos favoritos del usuario logueado ----
    $sql_productos = "SELECT p.id, p.nombre_producto, p.precio, l.nombre_local,
                              c.nombre AS categoria_nombre,
                              (SELECT ip.ruta FROM imagenes_producto ip
                               WHERE ip.producto_id = p.id
                               ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
                       FROM favoritos_productos fp
                       INNER JOIN productos p ON fp.producto_id = p.id
                       INNER JOIN locales l ON p.local_id = l.id
                       LEFT JOIN categorias_producto c ON p.categoria_id = c.id
                       WHERE fp.usuario_id = ?
                       ORDER BY fp.creado_en DESC";
    $stmt_productos = $conexion->prepare($sql_productos);
    $stmt_productos->bind_param("i", $usuario_id);
    $stmt_productos->execute();
    $resultado_productos = $stmt_productos->get_result();
    $productos_favoritos = $resultado_productos->fetch_all(MYSQLI_ASSOC);
    $stmt_productos->close();

    $conexion->close();
}
?>

<div class="fondo"></div>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-2 border-end bg-white">
            <?php include 'sidebar_prendas_locales.php'; ?>
        </aside>
        <div class="col-md-10 my-3">
            <div class="contenedor-catalogo">
                <h2>Favoritos</h2>

                <?php if (!$logueado): ?>

                    <p>Para ver tus favoritos necesitás iniciar sesión.</p>
                    <a href="iniciar.html" class="Boton-secundario" style="width: 20%;">Iniciar sesión</a>

                <?php else: ?>

                    <!-- ===================== LOCALES FAVORITOS ===================== -->
                    <h3 class="mt-2">Locales</h3>
                    <div id="grillaFavLocales" class="row g-3 mb-4">
                        <?php if (count($locales_favoritos) > 0): ?>
                            <?php foreach ($locales_favoritos as $local): ?>
                                <div class="col-12 col-md-6 col-lg-4 tarjeta-local">
                                    <div class="card h-100 position-relative">
                                        <button type="button"
                                                class="btn-favorito position-absolute top-0 end-0 m-2 bg-white rounded-circle"
                                                data-tipo="local"
                                                data-id="<?php echo $local['id']; ?>"
                                                data-contexto="favoritos"
                                                aria-label="Sacar de favoritos">
                                            <i class="bi bi-heart-fill text-danger"></i>
                                        </button>
                                        <div class="info">
                                            <h3 class="h6 text-start">
                                                <?php echo htmlspecialchars($local['nombre_local']); ?>
                                            </h3>
                                            <p class="text-muted small fst-italic mb-1 text-start">
                                                <?php echo htmlspecialchars($local['direccion']); ?>
                                            </p>
                                            <?php if (!empty($local['descripcion'])): ?>
                                                <p class="small mb-1 text-start">
                                                    <?php echo htmlspecialchars($local['descripcion']); ?>
                                                </p>
                                            <?php endif; ?>
                                            <?php if (!empty($local['categoria_nombre'])): ?>
                                                <span><?php echo htmlspecialchars($local['categoria_nombre']); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-4" id="sinFavLocales">
                                <p class="fs-6 text-muted">Todavía no tenés locales marcados como favoritos.</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <hr class="my-3">

                    <!-- ===================== PRODUCTOS FAVORITOS ===================== -->
                    <h3>Productos</h3>
                    <div id="grillaFavProductos" class="row g-3">
                        <?php if (count($productos_favoritos) > 0): ?>
                            <?php foreach ($productos_favoritos as $producto): ?>
                                <?php $categoriaSlug = $producto['categoria_nombre'] ? strtolower($producto['categoria_nombre']) : ''; ?>
                                <div class="col-6 col-lg-3 tarjeta-producto"
                                     data-categoria="<?php echo htmlspecialchars($categoriaSlug); ?>"
                                     data-precio="<?php echo $producto['precio']; ?>">
                                    <div class="card h-100 position-relative">
                                        <button type="button"
                                                class="btn-favorito position-absolute top-0 end-0 m-2 bg-white rounded-circle"
                                                style="z-index: 3;"
                                                data-tipo="producto"
                                                data-id="<?php echo $producto['id']; ?>"
                                                data-contexto="favoritos"
                                                aria-label="Sacar de favoritos">
                                            <i class="bi bi-heart-fill text-danger"></i>
                                        </button>
                                        <a href="prenda.php?id=<?php echo $producto['id']; ?>"
                                           class="nav-link text-decoration-none text-dark stretched-link">
                                            <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                                 class="card-img-top" style="height:200px; object-fit:cover;"
                                                 alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                            <div class="card-body text-center">
                                                <h3 class="h6 text-start"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                                <p class="text-muted small fst-italic mb-0 text-start">
                                                    Local: <?php echo htmlspecialchars($producto['nombre_local']); ?>
                                                </p>
                                                <p class="fw-bold text-success mb-1 text-start ps-0">
                                                    $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                                </p>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-12 text-center py-4" id="sinFavProductos">
                                <p class="fs-6 text-muted">Todavía no tenés productos marcados como favoritos.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <p id="sinResultadosFiltro" class="text-center text-muted py-4 d-none">
                        Ningún favorito coincide con los filtros seleccionados.
                    </p>

                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
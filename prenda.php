<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$producto_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT p.id, p.nombre_producto, p.descripcion, p.talle, p.precio, p.local_id, p.categoria_id,
               l.nombre_local, c.nombre AS categoria_nombre
        FROM productos p
        INNER JOIN locales l ON p.local_id = l.id
        LEFT JOIN categorias_producto c ON p.categoria_id = c.id
        WHERE p.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

$imagenesProducto = [];
$similares = [];
// Antes esto exigía rol==='comprador'. Se saca esa restricción para que
// se comporte igual que en catalogo.php: cualquier usuario logueado
// (comprador o vendedor) puede marcar favoritos — el backend
// (toggle_favorito.php) tampoco distingue por rol.
$esComprador = isset($_SESSION['usuario_id']);
$esFavorito = false;
$favoritos_producto_ids = [];

if ($esComprador) {
    $sql_fav = "SELECT producto_id FROM favoritos_productos WHERE usuario_id = ?";
    $stmt_fav = $conexion->prepare($sql_fav);
    $stmt_fav->bind_param("i", $_SESSION['usuario_id']);
    $stmt_fav->execute();
    $resultado_fav = $stmt_fav->get_result();
    while ($fila_fav = $resultado_fav->fetch_assoc()) {
        $favoritos_producto_ids[] = (int) $fila_fav['producto_id'];
    }
    $stmt_fav->close();
}

if ($producto) {
    // Todas las fotos, en orden. La de orden 0 (la primera que el vendedor
    // cargó al publicar) es la que se usa como principal.
    $sqlImg = "SELECT ruta FROM imagenes_producto WHERE producto_id = ? ORDER BY orden ASC";
    $stmtImg = $conexion->prepare($sqlImg);
    $stmtImg->bind_param("i", $producto_id);
    $stmtImg->execute();
    $resImg = $stmtImg->get_result();
    while ($fila = $resImg->fetch_assoc()) {
        $imagenesProducto[] = $fila['ruta'];
    }
    $stmtImg->close();

    // Prendas similares = misma categoría que esta prenda, sin incluirla a ella misma.
    if ($producto['categoria_id']) {
        $sqlSim = "SELECT p2.id, p2.nombre_producto, p2.precio,
                          (SELECT ip.ruta FROM imagenes_producto ip
                           WHERE ip.producto_id = p2.id
                           ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
                   FROM productos p2
                   WHERE p2.categoria_id = ? AND p2.id != ?
                   ORDER BY p2.creado_en DESC
                   LIMIT 8";
        $stmtSim = $conexion->prepare($sqlSim);
        $stmtSim->bind_param("ii", $producto['categoria_id'], $producto_id);
        $stmtSim->execute();
        $resSim = $stmtSim->get_result();
        while ($fila = $resSim->fetch_assoc()) {
            $similares[] = $fila;
        }
        $stmtSim->close();
    }

    // ¿El comprador logueado ya tiene esta prenda en favoritos?
    if ($esComprador) {
        $sqlFav = "SELECT 1 FROM favoritos_productos WHERE usuario_id = ? AND producto_id = ?";
        $stmtFav = $conexion->prepare($sqlFav);
        $stmtFav->bind_param("ii", $_SESSION['usuario_id'], $producto_id);
        $stmtFav->execute();
        $esFavorito = $stmtFav->get_result()->num_rows > 0;
        $stmtFav->close();
    }
}

$conexion->close();
?>
<div class="fondo"></div>

<div class="caja-local-prenda">
    <a href="#" class="volver-atras d-inline-block mb-3 text-decoration-none">&larr; Página anterior</a>

    <?php if (!$producto): ?>

        <p class="text-muted">No encontramos esa prenda.</p>

    <?php else: ?>

        <div class="superior">

            <div class="caja1">
                <div id="nombre-prenda" class="d-flex align-items-center gap-3">
                    <h3 class="mb-0"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                    <?php if ($esComprador): ?>
                        <button type="button" class="btn-favorito-inferior <?php echo $esFavorito ? 'activo' : ''; ?>"
                                data-tipo="producto" data-id="<?php echo $producto['id']; ?>"
                                title="Guardar en favoritos">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                <line class="linea-tacha" x1="2" y1="2" x2="22" y2="22"></line>
                            </svg>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($producto['categoria_nombre']): ?>
                    <span class="badge bg-success mb-2"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></span>
                <?php endif; ?>

                <p class="fs-4 fw-bold text-success">
                    $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                </p>

                <?php if (!empty($producto['talle'])): ?>
                    <p class="mb-2">Talle: <strong><?php echo htmlspecialchars($producto['talle']); ?></strong></p>
                <?php endif; ?>

                <p>
                    Vendido por:
                    <a href="index.php?pagina=local&id=<?php echo $producto['local_id']; ?>" class="enlace-interno fw-bold">
                        <?php echo htmlspecialchars($producto['nombre_local']); ?>
                    </a>
                </p>

                <div id="descripcion-prenda">
                    <h6>Descripcion de la prenda:</h6>
                    <p>
                        <?php echo $producto['descripcion']
                            ? nl2br(htmlspecialchars($producto['descripcion']))
                            : 'El vendedor todavía no cargó una descripción.'; ?>
                    </p>
                </div>
            </div>

            <div class="caja2">
                <div id="imagen-local-prenda">
                    <?php if (count($imagenesProducto) > 0): ?>
                        <img id="imagen-prenda-grande" src="<?php echo htmlspecialchars($imagenesProducto[0]); ?>"
                             alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                             style="width:100%; height:100%; object-fit:cover; border-radius: 8px;">
                    <?php else: ?>
                        <p>imagen de la prenda</p>
                    <?php endif; ?>
                </div>
                <?php if (count($imagenesProducto) > 1): ?>
                    <div id="mas-imagenes-prenda">
                        <?php foreach ($imagenesProducto as $i => $ruta): ?>
                            <img src="<?php echo htmlspecialchars($ruta); ?>"
                                 class="miniatura-galeria <?php echo $i === 0 ? 'activa' : ''; ?>"
                                 data-target="imagen-prenda-grande"
                                 data-src="<?php echo htmlspecialchars($ruta); ?>">
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <div class="inferior">
            <div>
                <h2>Prendas similares</h2>
            </div>

            <div class="row g-3 mt-1">
                <?php if (count($similares) > 0): ?>
                    <?php foreach ($similares as $similar): ?>
                        <?php $esFavSimilar = in_array((int) $similar['id'], $favoritos_producto_ids, true); ?>
                        <div class="col-6 col-lg-3">
                            <div class="card h-100 position-relative">
                                <a href="index.php?pagina=prenda&id=<?php echo $similar['id']; ?>"
                                   class="enlace-interno text-decoration-none text-dark stretched-link">
                                    <img src="<?php echo htmlspecialchars($similar['imagen_ruta']); ?>"
                                         class="card-img-top" style="height:180px; object-fit:cover;"
                                         alt="<?php echo htmlspecialchars($similar['nombre_producto']); ?>">
                                    <div class="card-body text-center pb-0">
                                        <h3 class="h6 text-start"><?php echo htmlspecialchars($similar['nombre_producto']); ?></h3>
                                    </div>
                                </a>
                                <div class="card-body pt-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <p class="fw-bold text-success mb-0">
                                            $<?php echo number_format($similar['precio'], 2, ',', '.'); ?>
                                        </p>
                                        <?php if ($esComprador): ?>
                                            <button type="button"
                                                    class="btn-favorito-inferior position-relative <?php echo $esFavSimilar ? 'activo' : ''; ?>"
                                                    style="z-index: 2;"
                                                    data-tipo="producto"
                                                    data-id="<?php echo $similar['id']; ?>"
                                                    aria-label="Marcar como favorito">
                                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"></path>
                                                    <line class="linea-tacha" x1="2" y1="2" x2="22" y2="22"></line>
                                                </svg>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Todavía no hay otras prendas en esta categoría.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
</div>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$producto_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT p.id, p.nombre_producto, p.descripcion, p.precio, p.local_id, p.categoria_id,
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

$imagenPrincipal = null;
$similares = [];
$esComprador = isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'comprador';
$esFavorito = false;

if ($producto) {
    // La imagen principal es la de orden 0: la primera que el vendedor cargó al publicar.
    $sqlImg = "SELECT ruta FROM imagenes_producto WHERE producto_id = ? ORDER BY orden ASC LIMIT 1";
    $stmtImg = $conexion->prepare($sqlImg);
    $stmtImg->bind_param("i", $producto_id);
    $stmtImg->execute();
    $filaImg = $stmtImg->get_result()->fetch_assoc();
    $imagenPrincipal = $filaImg ? $filaImg['ruta'] : null;
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
                        <button type="button" class="btn-favorito btn btn-link fs-4 p-0"
                                data-tipo="producto" data-id="<?php echo $producto['id']; ?>"
                                title="Guardar en favoritos">
                            <i class="bi <?php echo $esFavorito ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
                        </button>
                    <?php endif; ?>
                </div>

                <?php if ($producto['categoria_nombre']): ?>
                    <span class="badge bg-success mb-2"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></span>
                <?php endif; ?>

                <p class="fs-4 fw-bold text-success">
                    $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                </p>

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
                <div id="imagen-prenda">
                    <?php if ($imagenPrincipal): ?>
                        <img src="<?php echo htmlspecialchars($imagenPrincipal); ?>"
                             alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>"
                             style="width:100%; height:100%; object-fit:cover; border-radius: 8px;">
                    <?php else: ?>
                        <p>imagen de la prenda</p>
                    <?php endif; ?>
                </div>
                <div id="mas-imagenes-prenda">
                    <p>mas imagenes de la prenda</p>
                </div>
            </div>

        </div>

        <div class="inferior">
            <div>
                <h2>Prendas similares</h2>
            </div>

            <div class="row g-3 mt-1">
                <?php if (count($similares) > 0): ?>
                    <?php foreach ($similares as $similar): ?>
                        <div class="col-6 col-lg-3">
                            <a href="index.php?pagina=prenda&id=<?php echo $similar['id']; ?>"
                               class="enlace-interno d-block text-decoration-none text-dark">
                                <div class="card h-100">
                                    <img src="<?php echo htmlspecialchars($similar['imagen_ruta']); ?>"
                                         class="card-img-top" style="height:180px; object-fit:cover;"
                                         alt="<?php echo htmlspecialchars($similar['nombre_producto']); ?>">
                                    <div class="card-body text-center">
                                        <h3 class="h6 text-start"><?php echo htmlspecialchars($similar['nombre_producto']); ?></h3>
                                        <p class="fw-bold text-success mb-0 text-start">
                                            $<?php echo number_format($similar['precio'], 2, ',', '.'); ?>
                                        </p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">Todavía no hay otras prendas en esta categoría.</p>
                <?php endif; ?>
            </div>
        </div>

    <?php endif; ?>
</div>
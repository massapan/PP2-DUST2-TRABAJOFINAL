<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$producto_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT p.id, p.nombre_producto, p.precio, p.local_id,
               l.nombre_local, l.direccion,
               c.nombre AS categoria_nombre
        FROM productos p
        INNER JOIN locales l ON p.local_id = l.id
        LEFT JOIN categorias_producto c ON p.categoria_id = c.id
        WHERE p.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Traemos todas las imágenes de la galería del producto (orden ascendente).
$imagenes = [];
if ($producto) {
    $sqlImg = "SELECT ruta FROM imagenes_producto WHERE producto_id = ? ORDER BY orden ASC";
    $stmtImg = $conexion->prepare($sqlImg);
    $stmtImg->bind_param("i", $producto_id);
    $stmtImg->execute();
    $resImg = $stmtImg->get_result();
    while ($fila = $resImg->fetch_assoc()) {
        $imagenes[] = $fila['ruta'];
    }
    $stmtImg->close();
}

$conexion->close();
?>

<div class="fondo"></div>
<div class="container-fluid my-3">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="contenedor-catalogo">

                <a href="catalogo.php" class="enlace-interno d-inline-block mb-3">&larr; Volver al catálogo</a>

                <?php if (!$producto): ?>

                    <p class="fs-5 text-muted">No encontramos ese producto.</p>

                <?php else: ?>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <?php if (count($imagenes) > 0): ?>
                                <img src="<?php echo htmlspecialchars($imagenes[0]); ?>"
                                     class="img-fluid rounded" style="width:100%; max-height:420px; object-fit:cover;"
                                     alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                                <?php if (count($imagenes) > 1): ?>
                                    <div class="d-flex gap-2 mt-2 flex-wrap">
                                        <?php foreach (array_slice($imagenes, 1) as $ruta): ?>
                                            <img src="<?php echo htmlspecialchars($ruta); ?>"
                                                 class="rounded" style="width:70px; height:70px; object-fit:cover;">
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            <?php else: ?>
                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="height:300px;">
                                    <span class="text-muted">Sin imagen</span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <?php if ($producto['categoria_nombre']): ?>
                                <span class="badge bg-success mb-2"><?php echo htmlspecialchars($producto['categoria_nombre']); ?></span>
                            <?php endif; ?>

                            <h2><?php echo htmlspecialchars($producto['nombre_producto']); ?></h2>

                            <p class="fs-3 fw-bold text-success">
                                $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                            </p>

                            <p class="mb-1">
                                Vendido por:
                                <a href="local.php?id=<?php echo $producto['local_id']; ?>" class="enlace-interno fw-bold">
                                    <?php echo htmlspecialchars($producto['nombre_local']); ?>
                                </a>
                            </p>

                            <?php if ($producto['direccion']): ?>
                                <p class="text-muted small">
                                    <i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($producto['direccion']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
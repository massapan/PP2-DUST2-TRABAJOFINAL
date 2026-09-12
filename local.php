<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$local_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT l.id, l.nombre_local, l.direccion, l.descripcion, l.horario_texto,
               cl.nombre AS categoria_nombre
        FROM locales l
        LEFT JOIN categorias_local cl ON l.categoria_id = cl.id
        WHERE l.id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $local_id);
$stmt->execute();
$local = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Productos de este local, con su primera imagen (misma lógica que catalogo.php).
$productos = [];
if ($local) {
    $sqlProd = "SELECT p.id, p.nombre_producto, p.precio,
                       (SELECT ip.ruta FROM imagenes_producto ip
                        WHERE ip.producto_id = p.id
                        ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
                FROM productos p
                WHERE p.local_id = ?
                ORDER BY p.creado_en DESC";
    $stmtProd = $conexion->prepare($sqlProd);
    $stmtProd->bind_param("i", $local_id);
    $stmtProd->execute();
    $resProd = $stmtProd->get_result();
    while ($fila = $resProd->fetch_assoc()) {
        $productos[] = $fila;
    }
    $stmtProd->close();
}

$conexion->close();
?>

<div class="fondo"></div>
<div class="container-fluid my-3">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="contenedor-catalogo">

                <a href="catalogo.php" class="enlace-interno d-inline-block mb-3">&larr; Volver al catálogo</a>

                <?php if (!$local): ?>

                    <p class="fs-5 text-muted">No encontramos ese local.</p>

                <?php else: ?>

                    <?php if ($local['categoria_nombre']): ?>
                        <span class="badge bg-success mb-2"><?php echo htmlspecialchars($local['categoria_nombre']); ?></span>
                    <?php endif; ?>

                    <h2><?php echo htmlspecialchars($local['nombre_local']); ?></h2>

                    <?php if ($local['direccion']): ?>
                        <p class="text-muted mb-1"><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($local['direccion']); ?></p>
                    <?php endif; ?>

                    <?php if ($local['horario_texto']): ?>
                        <p class="text-muted mb-1"><i class="bi bi-clock"></i> <?php echo htmlspecialchars($local['horario_texto']); ?></p>
                    <?php endif; ?>

                    <?php if ($local['descripcion']): ?>
                        <p class="mt-3"><?php echo nl2br(htmlspecialchars($local['descripcion'])); ?></p>
                    <?php endif; ?>

                    <hr class="my-4">

                    <h4>Productos de este local</h4>

                    <div class="row g-3 mt-1">
                        <?php if (count($productos) > 0): ?>
                            <?php foreach ($productos as $producto): ?>
                                <div class="col-6 col-lg-3">
                                    <a href="prenda.php?id=<?php echo $producto['id']; ?>"
                                       class="enlace-interno d-block text-decoration-none text-dark">
                                        <div class="card h-100">
                                            <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                                 class="card-img-top" style="height:180px; object-fit:cover;"
                                                 alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                            <div class="card-body text-center">
                                                <h3 class="h6 text-start"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                                <p class="fw-bold text-success mb-0 text-start">
                                                    $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-muted">Este local todavía no publicó productos.</p>
                        <?php endif; ?>
                    </div>

                <?php endif; ?>

            </div>
        </div>
    </div>
</div>
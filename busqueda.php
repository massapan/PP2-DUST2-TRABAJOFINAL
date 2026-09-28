<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$q = trim($_GET['q'] ?? '');
$locales = [];
$productos = [];

if ($q !== '') {
    // Escapamos % y _ para que el usuario no pueda usarlos como comodines
    $like = '%' . addcslashes($q, '%_\\') . '%';

    // Locales: solo por nombre
    $sql_locales = "SELECT id, nombre_local, direccion
                    FROM locales
                    WHERE nombre_local LIKE ?
                    ORDER BY nombre_local ASC";
    $stmt = $conexion->prepare($sql_locales);
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $locales = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();

    // Prendas: solo por nombre (sin tocar categorías)
    $sql_productos = "SELECT p.id, p.nombre_producto, p.precio,
                             l.id AS local_id, l.nombre_local,
                             (SELECT ip.ruta FROM imagenes_producto ip
                              WHERE ip.producto_id = p.id
                              ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
                      FROM productos p
                      INNER JOIN locales l ON p.local_id = l.id
                      WHERE p.nombre_producto LIKE ?
                      ORDER BY p.creado_en DESC";
    $stmt = $conexion->prepare($sql_productos);
    $stmt->bind_param("s", $like);
    $stmt->execute();
    $productos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conexion->close();
?>

<div class="fondo"></div>
<div class="container-fluid">
    <div class="contenedor-catalogo my-3">
        <h2>Resultados para "<?php echo htmlspecialchars($q); ?>"</h2>

        <?php if ($q === ''): ?>
            <p class="text-muted">Escribí algo en el buscador para encontrar locales o prendas.</p>
        <?php else: ?>

            <h3 class="mt-3">Locales</h3>
            <div class="row g-3 mb-4">
                <?php if (count($locales) > 0): ?>
                    <?php foreach ($locales as $local): ?>
                        <div class="col-12 col-md-6 col-lg-4">
                            <div class="card h-100 position-relative">
                                <div class="card-body">
                                    <h3 class="h6 mb-1">
                                        <a href="index.php?pagina=local&id=<?php echo $local['id']; ?>"
                                           class="enlace-interno stretched-link text-decoration-none text-dark">
                                            <?php echo htmlspecialchars($local['nombre_local']); ?>
                                        </a>
                                    </h3>
                                    <p class="text-muted small fst-italic mb-0">
                                        <?php echo htmlspecialchars($local['direccion']); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No encontramos locales con ese nombre.</p>
                <?php endif; ?>
            </div>

            <hr>

            <h3>Prendas</h3>
            <div class="row g-3">
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-6 col-lg-3">
                            <div class="card h-100 position-relative">
                                <a href="index.php?pagina=prenda&id=<?php echo $producto['id']; ?>"
                                   class="enlace-interno text-decoration-none text-dark stretched-link">
                                    <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                         class="card-img-top" style="height:200px; object-fit:cover;"
                                         alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                    <div class="card-body pb-0">
                                        <h3 class="h6 text-start"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                    </div>
                                </a>
                                <div class="card-body pt-0">
                                    <a href="index.php?pagina=local&id=<?php echo $producto['local_id']; ?>"
                                       class="enlace-interno position-relative text-muted small fst-italic d-block text-decoration-none"
                                       style="z-index: 2;">
                                        Local: <?php echo htmlspecialchars($producto['nombre_local']); ?>
                                    </a>
                                    <p class="fw-bold text-success mb-0 fs-5 mt-2">
                                        $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No encontramos prendas con ese nombre.</p>
                <?php endif; ?>
            </div>

        <?php endif; ?>
    </div>
</div>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$local_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT id, usuario_id, nombre_local, direccion, entre_calles, descripcion, imagen_portada, instagram, whatsapp, facebook, tiktok
        FROM locales WHERE id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $local_id);
$stmt->execute();
$local = $stmt->get_result()->fetch_assoc();
$stmt->close();

$productos = [];
$esComprador = isset($_SESSION['usuario_id']) && $_SESSION['rol'] === 'comprador';
$esFavorito = false;

if ($local) {
    // Todos los productos de ESTE local (mismo local_id), igual que en catalogo.php.
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

    // ¿El comprador logueado ya tiene este local en favoritos?
    if ($esComprador) {
        $sqlFav = "SELECT 1 FROM favoritos_locales WHERE usuario_id = ? AND local_id = ?";
        $stmtFav = $conexion->prepare($sqlFav);
        $stmtFav->bind_param("ii", $_SESSION['usuario_id'], $local_id);
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

    <?php if (!$local): ?>

        <p class="text-muted">No encontramos ese local.</p>

    <?php else: ?>

        <div class="superior">  

            <div class="caja1">
                <div id="nombre-local" class="d-flex align-items-center gap-3">
    <h1 class="mb-0"><?php echo htmlspecialchars($local['nombre_local']); ?></h1>

    <?php if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] == $local['usuario_id']): ?>
        <a href="SubidaLocal.php" title="Editar mi local">
            <i class="bi bi-pencil-square" style="font-size: 22px;"></i>
        </a>
    <?php endif; ?>

    <?php if ($esComprador): ?>
        <button type="button" class="btn-favorito btn btn-link fs-4 p-0"
                data-tipo="local" data-id="<?php echo $local['id']; ?>"
                title="Guardar en favoritos">
            <i class="bi <?php echo $esFavorito ? 'bi-heart-fill text-danger' : 'bi-heart'; ?>"></i>
        </button>
    <?php endif; ?>
</div>

                <div class="direccion-local">
                    <h5><i class="bi bi-geo-alt"></i> <?php echo htmlspecialchars($local['direccion']); ?></h5>
                </div>

                <?php if (!empty($local['entre_calles'])): ?>
                    <div id="entre-calles">
                        <h6>Entre calles: <?php echo htmlspecialchars($local['entre_calles']); ?></h6>
                    </div>
                <?php endif; ?>

                <div id="descripcion-local">
                    <h6>Descripcion del local:</h6>
                    <p>
                        <?php echo $local['descripcion']
                            ? nl2br(htmlspecialchars($local['descripcion']))
                            : 'Este local todavía no cargó una descripción.'; ?>
                    </p>
                </div>

<div id="redes-sociales-local" class="d-flex gap-3 mt-2">

    <?php if (!empty($local['instagram'])): ?>
        <a href="https://instagram.com/<?php echo htmlspecialchars($local['instagram']); ?>" target="_blank">
            <i class="bi bi-instagram" style="font-size: 24px; color: #E1306C;"></i>
        </a>
    <?php endif; ?>

    <?php if (!empty($local['whatsapp'])): ?>
        <a href="https://wa.me/<?php echo htmlspecialchars($local['whatsapp']); ?>" target="_blank">
            <i class="bi bi-whatsapp" style="font-size: 24px; color: #25D366;"></i>
        </a>
    <?php endif; ?>

    <?php if (!empty($local['facebook'])): ?>
        <a href="https://facebook.com/<?php echo htmlspecialchars($local['facebook']); ?>" target="_blank">
            <i class="bi bi-facebook" style="font-size: 24px; color: #1877F2;"></i>
        </a>
    <?php endif; ?>

    <?php if (!empty($local['tiktok'])): ?>
        <a href="https://tiktok.com/@<?php echo htmlspecialchars($local['tiktok']); ?>" target="_blank">
            <i class="bi bi-tiktok" style="font-size: 24px; color: #000000;"></i>
        </a>
    <?php endif; ?>

</div>

                <a href="index.php?pagina=mapa" id="ver-en-mapa" class="enlace-interno d-inline-block text-center text-decoration-none">
                    ver en el mapa
                </a>
            </div>

            <div class="caja2">
                <div id="imagen-local">
                    <?php if (!empty($local['imagen_portada'])): ?>
                        <img src="<?php echo htmlspecialchars($local['imagen_portada']); ?>"
                             alt="<?php echo htmlspecialchars($local['nombre_local']); ?>"
                             style="width:100%; height:100%; object-fit:cover; border-radius: 8px;">
                    <?php else: ?>
                        <p>imagen del local</p>
                    <?php endif; ?>
                </div>
                <div id="mas-imagenes-local">
                    <p>mas imagenes del local</p>
                </div>
            </div>

        </div>

        <div class="inferior">
            <div>
                <h2>Catalogo de prendas</h2>
            </div>

            <div class="row g-3 mt-1">
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="col-6 col-lg-3">
                            <a href="index.php?pagina=prenda&id=<?php echo $producto['id']; ?>"
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
        </div>

    <?php endif; ?>
</div>
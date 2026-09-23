<?php
ob_start();
session_start();

// Validación de seguridad: Solo vendedores logueados
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

// Buscamos el local de este vendedor
$sql_local = "SELECT id FROM locales WHERE usuario_id = ?";
$stmt_local = $conexion->prepare($sql_local);
$stmt_local->bind_param("i", $usuario_id);
$stmt_local->execute();
$local = $stmt_local->get_result()->fetch_assoc();
$stmt_local->close();

// Todos los productos de ESE local, con su primera imagen (misma lógica
// que ya usás en catalogo.php y local.php)
$productos = [];
if ($local) {
    $sql = "SELECT p.id, p.nombre_producto, p.precio,
                   (SELECT ip.ruta FROM imagenes_producto ip
                    WHERE ip.producto_id = p.id
                    ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
            FROM productos p
            WHERE p.local_id = ?
            ORDER BY p.creado_en DESC";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $local['id']);
    $stmt->execute();
    $resultado = $stmt->get_result();
    while ($fila = $resultado->fetch_assoc()) {
        $productos[] = $fila;
    }
    $stmt->close();
}

// El vendedor también puede favoritear como cualquier usuario (si algún día
// entra como comprador de otro local), así que pintamos el corazón igual
// que en catalogo.php.
$favoritos_producto_ids = [];
$sql_fav = "SELECT producto_id FROM favoritos_productos WHERE usuario_id = ?";
$stmt_fav = $conexion->prepare($sql_fav);
$stmt_fav->bind_param("i", $usuario_id);
$stmt_fav->execute();
$resultado_fav = $stmt_fav->get_result();
while ($fila_fav = $resultado_fav->fetch_assoc()) {
    $favoritos_producto_ids[] = (int) $fila_fav['producto_id'];
}
$stmt_fav->close();

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Productos - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>
<body>

    <?php include 'header.php'; ?>

    <div id="contenido">

        <div class="fondo"></div>

        <div class="container-fluid my-3">
            <div class="contenedor-catalogo">

                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h2 class="mb-0">Mis Productos</h2>
                    <a href="productos.php" class="btn boton text-decoration-none">
                        <i class="bi bi-plus-lg"></i> Nuevo producto
                    </a>
                </div>

                <?php if (!$local): ?>

                    <p class="text-muted">
                        Todavía no tenés un local registrado.
                        <a href="SubidaLocal.php">Registrá uno</a> para poder subir productos.
                    </p>

                <?php elseif (count($productos) === 0): ?>

                    <p class="text-muted">Todavía no subiste ningún producto.</p>

                <?php else: ?>

                    <div class="row g-3">
                        <?php foreach ($productos as $producto): ?>
                            <?php $esFavorito = in_array((int) $producto['id'], $favoritos_producto_ids, true); ?>
                            <div class="col-6 col-lg-3">
                                <div class="card h-100 position-relative">

                                    <a href="productos.php?id=<?php echo $producto['id']; ?>"
                                       class="btn-editar-producto position-absolute top-0 end-0 m-2 bg-white rounded-circle"
                                       style="z-index: 3;"
                                       title="Editar producto">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <a href="index.php?pagina=prenda&id=<?php echo $producto['id']; ?>"
                                       class="enlace-interno text-decoration-none text-dark stretched-link">
                                        <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                             class="card-img-top" style="height:200px; object-fit:cover;"
                                             alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                                        <div class="card-body text-center pb-0">
                                            <h3 class="h6 text-start"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                        </div>
                                    </a>

                                    <div class="card-body pt-0">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="fw-bold text-success mb-0">
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
                        <?php endforeach; ?>
                    </div>

                <?php endif; ?>

            </div>
        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/favoritos.js"></script>

</body>
</html>
<?php ob_end_flush(); ?>
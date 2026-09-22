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
                            <div class="col-6 col-lg-3">
                                <div class="card h-100 position-relative">

                                    <a href="productos.php?id=<?php echo $producto['id']; ?>"
                                       class="btn-editar-producto position-absolute top-0 end-0 m-2 bg-white rounded-circle"
                                       style="z-index: 3;"
                                       title="Editar producto">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>

                                    <img src="<?php echo htmlspecialchars($producto['imagen_ruta']); ?>"
                                         class="card-img-top" style="height:200px; object-fit:cover;"
                                         alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">

                                    <div class="card-body text-center">
                                        <h3 class="h6 text-start"><?php echo htmlspecialchars($producto['nombre_producto']); ?></h3>
                                        <p class="fw-bold text-success mb-0 text-start">
                                            $<?php echo number_format($producto['precio'], 2, ',', '.'); ?>
                                        </p>
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

</body>
</html>
<?php ob_end_flush(); ?>
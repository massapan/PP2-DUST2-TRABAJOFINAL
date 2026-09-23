<?php
ob_start();
session_start();

// Validación de seguridad: Solo vendedores pueden cargar productos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$categorias_res = $conexion->query("SELECT id, nombre FROM categorias_producto ORDER BY nombre ASC");
$categorias = [];
while ($cat = $categorias_res->fetch_assoc()) {
    $categorias[] = $cat;
}

// ¿Vino un ?id= en la URL? Entonces esto es una edición, no una carga nueva.
// Verificamos que el producto exista Y sea de un local de ESTE vendedor —
// si no, cualquiera podría editar productos ajenos cambiando el id a mano.
$modoEdicion = false;
$producto = null;
$imagenesExistentes = [];

if (isset($_GET['id'])) {
    $producto_id_editar = (int) $_GET['id'];

    $sqlProd = "SELECT p.* FROM productos p
                INNER JOIN locales l ON p.local_id = l.id
                WHERE p.id = ? AND l.usuario_id = ?";
    $stmtProd = $conexion->prepare($sqlProd);
    $stmtProd->bind_param("ii", $producto_id_editar, $_SESSION['usuario_id']);
    $stmtProd->execute();
    $producto = $stmtProd->get_result()->fetch_assoc();
    $stmtProd->close();

    if (!$producto) {
        // No existe, o no es de este vendedor: afuera, directo al catálogo.
        $conexion->close();
        header("Location: index.php");
        exit();
    }

    $modoEdicion = true;

    $sqlImg = "SELECT ruta FROM imagenes_producto WHERE producto_id = ? ORDER BY orden ASC";
    $stmtImg = $conexion->prepare($sqlImg);
    $stmtImg->bind_param("i", $producto['id']);
    $stmtImg->execute();
    $resImg = $stmtImg->get_result();
    while ($fila = $resImg->fetch_assoc()) {
        $imagenesExistentes[] = $fila['ruta'];
    }
    $stmtImg->close();
}

$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $modoEdicion ? 'Editar Producto' : 'Subir Producto'; ?> - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>
<body>

    <?php include 'header.php'; ?>

    <div id="contenido">

        <div class="fondo"></div>

        <div class="caja-local-prenda">

            <?php if ($modoEdicion): ?>
                <p class="text-muted mb-2" style="font-size: 14px;">
                    <i class="bi bi-pencil-square"></i> Editando producto
                </p>
            <?php endif; ?>

            <form id="form-subir-producto" action="guardar_producto.php" method="POST"
                  enctype="multipart/form-data" data-modo-edicion="<?php echo $modoEdicion ? '1' : '0'; ?>">

                <?php if ($modoEdicion): ?>
                    <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                <?php endif; ?>

                <div class="superior">

                    <div class="caja1">

                        <div id="nombre-local" class="d-flex align-items-center gap-3">
                            <input type="text" id="nombre_producto" name="nombre_producto"
                                   class="form-control" style="font-size: 32px; border: 2px solid #ccc;"
                                   placeholder="Nombre del producto"
                                   value="<?php echo $producto ? htmlspecialchars($producto['nombre_producto']) : ''; ?>"
                                   required>
                        </div>

                        <div id="direccion-local" style="margin-bottom: 10px;">
                            <select id="categoria_id" name="categoria_id"
                                    class="form-control" style="border: 1px solid #ccc; color: gray;" required>
                                <option value="" disabled <?php echo !$producto ? 'selected' : ''; ?>>Seleccioná una categoría</option>
                                <?php foreach ($categorias as $cat): ?>
                                    <option value="<?php echo $cat['id']; ?>"
                                        <?php echo ($producto && (int) $producto['categoria_id'] === (int) $cat['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cat['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div id="entre-calles">
                            <input type="text" id="talle" name="talle"
                                   class="form-control" style="border: 1px solid #ccc; color: gray;"
                                   placeholder="Talle (opcional, ej: M, 42, único)"
                                   value="<?php echo $producto ? htmlspecialchars($producto['talle'] ?? '') : ''; ?>">
                        </div>

                        <div id="descripcion-local">
                            <textarea id="descripcion" name="descripcion" rows="5"
                                      class="form-control" style="border: 1px solid #ccc; resize: none;"
                                      placeholder="Descripción (tela, estado, medidas, etc.)"><?php echo $producto ? htmlspecialchars($producto['descripcion'] ?? '') : ''; ?></textarea>
                        </div>

                        <div id="redes-sociales" class="mt-3">
                            <p class="mb-2 text-muted" style="font-size: 14px;">Precio</p>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-currency-dollar" style="font-size: 20px; color: #00953c;"></i>
                                <input type="number" id="precio" name="precio" step="0.01"
                                       class="form-control" style="border: 1px solid #ccc;"
                                       placeholder="Precio ($)"
                                       value="<?php echo $producto ? htmlspecialchars($producto['precio']) : ''; ?>"
                                       required>
                            </div>
                        </div>

                    </div>

                    <div class="caja2">

                        <div id="imagen-local-prenda" class="d-flex align-items-center justify-content-center">
                            <label for="imagen" style="cursor: pointer; text-align: center; padding: 20px;">
                                <i class="bi bi-camera" style="font-size: 40px;"></i>
                                <p class="mb-0"><?php echo $modoEdicion ? 'Cambiar foto principal (opcional)' : 'Foto principal del producto'; ?></p>
                            </label>
                            <input type="file" id="imagen" name="imagen"
       accept="image/*" <?php echo $modoEdicion ? '' : 'required'; ?>
       style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                        </div>

                        <div id="mas-imagenes-local" class="d-flex align-items-center justify-content-center">
                            <label for="imagenes_adicionales" style="cursor: pointer; text-align: center; padding: 20px;">
                                <i class="bi bi-images" style="font-size: 40px;"></i>
                                <p class="mb-0">Más fotos del producto (opcional)</p>
                            </label>
                            <input type="file" id="imagenes_adicionales" name="imagenes_adicionales[]"
       accept="image/*" multiple
       style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                        </div>

                        <?php if ($modoEdicion && count($imagenesExistentes) > 0): ?>
                            <div class="w-100 px-3">
                                <p class="text-muted mb-1" style="font-size: 12px;">Fotos actuales:</p>
                                <div class="d-flex gap-2 flex-wrap mb-2">
                                    <?php foreach ($imagenesExistentes as $ruta): ?>
                                        <img src="<?php echo htmlspecialchars($ruta); ?>"
                                             style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px;">
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <p class="text-muted" style="font-size: 12px; padding: 0 20px; text-align: center;">
                            <?php if ($modoEdicion): ?>
                                Si no elegís una foto nueva, se mantiene la que ya tenías. Las que agregues en
                                "Más fotos" se suman a la galería — no reemplazan a las que ya había.
                            <?php else: ?>
                                La foto principal es la que se muestra en el catálogo — subila
                                <strong>horizontal (apaisada)</strong>, no vertical, para que no se recorte mal.
                                En "Más fotos", <strong>el orden en que las elijas es el orden en que se van a
                                mostrar</strong> en la galería del producto.
                            <?php endif; ?>
                        </p>

                    </div>

                </div>

                <div style="text-align: right; margin-top: 15px;">
                    <a href="<?php echo $modoEdicion ? 'mis_productos.php' : 'index.php'; ?>"
                       class="btn boton text-decoration-none" style="margin-right: 15px;">
                        <?php echo $modoEdicion ? 'Cancelar' : 'Volver al catálogo'; ?>
                    </a>
                    <button type="submit" class="btn d-inline-block text-center boton">
                        <?php echo $modoEdicion ? 'Guardar Cambios' : 'Subir Producto'; ?>
                    </button>
                </div>

            </form>

        </div>

    </div>

    <?php include 'footer.php'; ?>

    <!-- Modal de resultado (se completa con JS según la respuesta del servidor) -->
    <div class="modal fade" id="modalResultadoProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <h4 id="modalResultadoTitulo" class="mb-3"></h4>
                    <p id="modalResultadoMensaje" class="text-muted mb-0"></p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <?php if ($modoEdicion): ?>
                        <a href="mis_productos.php" class="btn boton text-decoration-none">
                            Volver a Mis Productos
                        </a>
                    <?php else: ?>
                        <button type="button" id="btnSubirOtro" class="btn boton" data-bs-dismiss="modal">
                            Subir otro producto
                        </button>
                    <?php endif; ?>
                    <a href="index.php" class="btn btn-outline-secondary">
                        Ir a catálogo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/subir_producto.js"></script>

</body>
</html>
<?php ob_end_flush(); ?>
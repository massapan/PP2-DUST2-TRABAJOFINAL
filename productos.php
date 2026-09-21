<?php
ob_start();
session_start();

// Validación de seguridad: Solo vendedores pueden cargar productos
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';
$categorias = $conexion->query("SELECT id, nombre FROM categorias_producto ORDER BY nombre ASC");
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Producto - Ituzaingó a un toque</title>
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

            <?php if (isset($_GET['error']) && $_GET['error'] == 'sin_local'): ?>
                <div class="alert alert-danger" role="alert" style="margin-bottom: 15px;">
                    No tenés un local registrado. <a href="SubidaLocal.php">Registrá uno</a> antes de subir productos.
                </div>
            <?php endif; ?>

            <form id="form-subir-producto" action="guardar_producto.php" method="POST" enctype="multipart/form-data">

                <div class="superior">

                    <div class="caja1">

                        <div id="nombre-local" class="d-flex align-items-center gap-3">
                            <input type="text" id="nombre_producto" name="nombre_producto"
                                   class="form-control" style="font-size: 32px; border: 2px solid #ccc;"
                                   placeholder="Nombre del producto" required>
                        </div>

                        <div id="direccion-local" style="margin-bottom: 10px;">
                            <select id="categoria_id" name="categoria_id"
                                    class="form-control" style="border: 1px solid #ccc; color: gray;" required>
                                <option value="" disabled selected>Seleccioná una categoría</option>
                                <?php while ($cat = $categorias->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['nombre']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div id="entre-calles">
                            <input type="text" id="talle" name="talle"
                                   class="form-control" style="border: 1px solid #ccc; color: gray;"
                                   placeholder="Talle (opcional, ej: M, 42, único)">
                        </div>

                        <div id="descripcion-local">
                            <textarea id="descripcion" name="descripcion" rows="5"
                                      class="form-control" style="border: 1px solid #ccc; resize: none;"
                                      placeholder="Descripción (tela, estado, medidas, etc.)"></textarea>
                        </div>

                        <div id="redes-sociales" class="mt-3">
                            <p class="mb-2 text-muted" style="font-size: 14px;">Precio</p>
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-currency-dollar" style="font-size: 20px; color: #00953c;"></i>
                                <input type="number" id="precio" name="precio" step="0.01"
                                       class="form-control" style="border: 1px solid #ccc;"
                                       placeholder="Precio ($)" required>
                            </div>
                        </div>

                    </div>

                    <div class="caja2">

                        <div id="imagen-local-prenda" class="d-flex align-items-center justify-content-center">
                            <label for="imagen" style="cursor: pointer; text-align: center; padding: 20px;">
                                <i class="bi bi-camera" style="font-size: 40px;"></i>
                                <p class="mb-0">Foto principal del producto</p>
                            </label>
                            <input type="file" id="imagen" name="imagen"
       accept="image/*" required
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

                        <p class="text-muted" style="font-size: 12px; padding: 0 20px; text-align: center;">
                            La foto principal es la que se muestra en el catálogo — subila
                            <strong>horizontal (apaisada)</strong>, no vertical, para que no se recorte mal.
                            En "Más fotos", <strong>el orden en que las elijas es el orden en que se van a
                            mostrar</strong> en la galería del producto.
                        </p>

                    </div>

                </div>

                <div style="text-align: right; margin-top: 15px;">
                    <a href="index.php" class="btn boton text-decoration-none" style="margin-right: 15px;">
                        Volver al catálogo
                    </a>
                    <button type="submit" class="btn d-inline-block text-center boton">
                        Subir Producto
                    </button>
                </div>

            </form>

        </div>

    </div>

    <?php include 'footer.php'; ?>

    <!-- Modal de resultado al subir un producto (se completa con JS según la respuesta) -->
    <div class="modal fade" id="modalResultadoProducto" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center p-4">
                    <h4 id="modalResultadoTitulo" class="mb-3"></h4>
                    <p id="modalResultadoMensaje" class="text-muted mb-0"></p>
                </div>
                <div class="modal-footer justify-content-center border-0 pb-4">
                    <button type="button" id="btnSubirOtro" class="btn boton" data-bs-dismiss="modal">
                        Subir otro producto
                    </button>
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
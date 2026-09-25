<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conexion.php';

$sql = "SELECT nombre_local, direccion, entre_calles, descripcion, imagen_portada, instagram, whatsapp, facebook, tiktok
        FROM locales WHERE usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$local = $stmt->get_result()->fetch_assoc(); // null si todavía no tiene local
$stmt->close();
$conexion->close();
// Validamos seguridad: Si no hay sesión o no es vendedor, lo mandamos al login
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Local - Ituzaingó a un toque</title>
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

            <form action="guardar_local.php" method="POST" enctype="multipart/form-data">

                <div class="superior">

                    <div class="caja1">

                        <div id="nombre-local" class="d-flex align-items-center gap-3">
                            <input type="text" id="nombre_local" name="nombre_local"
       class="form-control" style="font-size: 32px; border: 2px solid #ccc;"
       placeholder="Nombre de tu local"
       value="<?php echo htmlspecialchars($local['nombre_local'] ?? ''); ?>" required>
                        </div>

                        <div id="direccion-local" style="margin-bottom: 10px;">
                            <input type="text" id="direccion" name="direccion"
       class="form-control" style="border: 1px solid #ccc; color: gray;"
       placeholder="Dirección (opcional)"
       value="<?php echo htmlspecialchars($local['direccion'] ?? ''); ?>">
                        </div>

                        <div id="entre-calles">
                            <input type="text" id="entre_calles" name="entre_calles"
       class="form-control" style="border: 1px solid #ccc; color: gray;"
       placeholder="Entre calles (opcional)"
       value="<?php echo htmlspecialchars($local['entre_calles'] ?? ''); ?>">
                        </div>

                        <div id="descripcion-local">
                            <textarea id="descripcion" name="descripcion" rows="5"
          class="form-control" style="border: 1px solid #ccc; resize: none;"
          placeholder="Descripción del local" required><?php echo htmlspecialchars($local['descripcion'] ?? ''); ?></textarea>
                        </div>

                       <div id="redes-sociales" class="mt-3">
    <p class="mb-2 text-muted" style="font-size: 14px;">Redes sociales (opcional)</p>

    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-instagram" style="font-size: 20px; color: #E1306C;"></i>
        <input type="text" name="instagram" class="form-control"
       style="border: 1px solid #ccc;" placeholder="usuario de Instagram (sin @)"
       value="<?php echo htmlspecialchars($local['instagram'] ?? ''); ?>">
    </div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-whatsapp" style="font-size: 20px; color: #25D366;"></i>
        <input type="text" name="whatsapp" class="form-control"
               style="border: 1px solid #ccc;" placeholder="WhatsApp (ej: 5491122334455)"
               value="<?php echo htmlspecialchars($local['whatsapp'] ?? ''); ?>">
    </div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-facebook" style="font-size: 20px; color: #1877F2;"></i>
        <input type="text" name="facebook" class="form-control"
               style="border: 1px solid #ccc;" placeholder="usuario o link de Facebook"
               value="<?php echo htmlspecialchars($local['facebook'] ?? ''); ?>">
    </div>

    <div class="d-flex align-items-center gap-2 mb-2">
        <i class="bi bi-tiktok" style="font-size: 20px; color: #000000;"></i>
        <input type="text" name="tiktok" class="form-control"
               style="border: 1px solid #ccc;" placeholder="usuario de TikTok (sin @)"
               value="<?php echo htmlspecialchars($local['tiktok'] ?? ''); ?>">
    </div>
</div>

                    </div>

                    <div class="caja2">

                        <div id="imagen-local-prenda" class="d-flex align-items-center justify-content-center">
                            <label for="imagen_portada" style="cursor: pointer; text-align: center; padding: 20px;">
                                <i class="bi bi-camera" style="font-size: 40px;"></i>
                                <p class="mb-0">Foto de portada de tu local</p>
                                  <p class="text-muted" style="font-size: 12px; padding: 0 20px; text-align: center;">
                            La foto de portada es la que se muestra como principal en todo el sitio —
                            subila <strong>horizontal (apaisada)</strong>, no vertical, para que no se
                            recorte mal.
                        </p>
                            </label>
                            <input type="file" id="imagen_portada" name="imagen_portada"
       accept="image/*" <?php echo $local ? '' : 'required'; ?>
       style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                        </div>

                        <div id="mas-imagenes-local" class="d-flex align-items-center justify-content-center">
                            <label for="imagenes_adicionales" style="cursor: pointer; text-align: center; padding: 20px;">
                                <i class="bi bi-images" style="font-size: 40px;"></i>
                                <p class="mb-0">Más fotos del local (opcional)</p>
                            </label>
                            <input type="file" id="imagenes_adicionales" name="imagenes_adicionales[]"
       accept="image/*" multiple
       style="position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0,0,0,0); white-space: nowrap; border: 0;">
                        </div>

                        <p class="text-muted" style="font-size: 12px; padding: 0 20px; text-align: center;">
                           En "Más fotos", <strong>el orden en que las elijas es el orden
                            en que se van a mostrar</strong> en la galería del local.
                        </p>

                    </div>


                    
                </div>
                
                         <div style="text-align: right; margin-top: 15px;">
    <?php if (!$local): ?>
        <a href="index.php" class="btn boton text-decoration-none" style="margin-right: 15px;">
            Omitir por ahora
        </a>
    <?php endif; ?>
    <button type="submit" class="btn d-inline-block text-center boton">
        Guardar Local
    </button>
</div>
            </form>

        </div>

    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/preview_imagen.js"></script>

</body>
</html>
<?php ob_end_flush(); ?>
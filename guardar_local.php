<?php
ob_start();
session_start();

// Validación de seguridad estricta
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
    <title>Resultado del Local - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-centrada">
    <div class="fondo"></div>

    <div class="container">

        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Tomamos el ID del vendedor directamente de la sesión
            $usuario_id = $_SESSION['usuario_id'];

            // Capturamos los datos
            $nombre_local = trim($_POST['nombre_local']);
            $direccion    = trim($_POST['direccion']);
            $entre_calles = trim($_POST['entre_calles'] ?? '');
            $descripcion  = trim($_POST['descripcion']);
            $instagram = trim($_POST['instagram'] ?? '');
            $whatsapp  = trim($_POST['whatsapp'] ?? '');
            $facebook  = trim($_POST['facebook'] ?? '');
            $tiktok    = trim($_POST['tiktok'] ?? '');

            // Si vinieron vacíos, los guardamos como NULL (no como string vacío)
            $instagram = $instagram !== '' ? $instagram : null;
            $whatsapp  = $whatsapp  !== '' ? $whatsapp  : null;
            $facebook  = $facebook  !== '' ? $facebook  : null;
            $tiktok    = $tiktok    !== '' ? $tiktok    : null;

            // Buscamos si este vendedor ya tiene un local (y de paso, su imagen actual)
            $sql_check = "SELECT id, imagen_portada FROM locales WHERE usuario_id = ?";
            $stmt_check = $conexion->prepare($sql_check);
            $stmt_check->bind_param("i", $usuario_id);
            $stmt_check->execute();
            $local_existente = $stmt_check->get_result()->fetch_assoc();
            $stmt_check->close();

            // ¿Subió una foto de portada NUEVA en este envío?
            $subio_portada_nueva = isset($_FILES["imagen_portada"])
                && $_FILES["imagen_portada"]["error"] !== UPLOAD_ERR_NO_FILE;
            $error_portada = false;

            // Si NO subió una imagen nueva, y ya tenía una, mantenemos la vieja
            // (columna imagen_portada — la dejamos de compatibilidad, aunque
            // la que se usa de verdad para mostrar es la tabla imagenes_local)
            if (!$subio_portada_nueva && $local_existente) {
                $ruta_imagen = $local_existente['imagen_portada'];
            } else {
                $directorio_subida = "uploads/";
                $nombre_archivo = time() . "_" . basename($_FILES["imagen_portada"]["name"]);
                $ruta_imagen = $directorio_subida . $nombre_archivo;
                if (!move_uploaded_file($_FILES["imagen_portada"]["tmp_name"], $ruta_imagen)) {
                    $error_portada = true;
                }
            }

            if ($error_portada) {
                echo "<h2 style='color: red;'>Error con la imagen</h2>";
                echo "<p>No se pudo guardar la foto de portada. Verificá que la carpeta 'uploads' exista en tu proyecto.</p>";
                $conexion->close();
                ob_end_flush();
                exit();
            }

            $sql = "INSERT INTO locales (usuario_id, nombre_local, direccion, entre_calles, descripcion, imagen_portada, instagram, whatsapp, facebook, tiktok)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                    ON DUPLICATE KEY UPDATE
                        nombre_local = VALUES(nombre_local),
                        direccion = VALUES(direccion),
                        entre_calles = VALUES(entre_calles),
                        descripcion = VALUES(descripcion),
                        imagen_portada = VALUES(imagen_portada),
                        instagram = VALUES(instagram),
                        whatsapp = VALUES(whatsapp),
                        facebook = VALUES(facebook),
                        tiktok = VALUES(tiktok)";
            $stmt = $conexion->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("isssssssss", $usuario_id, $nombre_local, $direccion, $entre_calles, $descripcion, $ruta_imagen, $instagram, $whatsapp, $facebook, $tiktok);

                if ($stmt->execute()) {
                    $stmt->close();

                    // insert_id NO es confiable acá: con ON DUPLICATE KEY UPDATE,
                    // si fue un UPDATE (el vendedor ya tenía local) puede no traer
                    // el id correcto. Lo buscamos de nuevo por las dudas, así
                    // funciona igual de bien si fue creación o edición.
                    $sql_id = "SELECT id FROM locales WHERE usuario_id = ?";
                    $stmt_id = $conexion->prepare($sql_id);
                    $stmt_id->bind_param("i", $usuario_id);
                    $stmt_id->execute();
                    $local_id = $stmt_id->get_result()->fetch_assoc()['id'];
                    $stmt_id->close();

                    // Portada = orden 0 en la galería. Solo la tocamos si
                    // subió una foto nueva; si no, dejamos la fila vieja como
                    // está (ni siquiera hace falta reescribirla).
                    if ($subio_portada_nueva) {
                        $sql_portada = "INSERT INTO imagenes_local (local_id, ruta, orden) VALUES (?, ?, 0)
                                        ON DUPLICATE KEY UPDATE ruta = VALUES(ruta)";
                        $stmt_portada = $conexion->prepare($sql_portada);
                        $stmt_portada->bind_param("is", $local_id, $ruta_imagen);
                        $stmt_portada->execute();
                        $stmt_portada->close();
                    }

                    // Fotos adicionales: se AGREGAN a la galería, nunca borran
                    // las que ya había. Seguimos el orden donde quedó la vez
                    // anterior (por si el vendedor edita el local más de una vez).
                    if (!empty($_FILES['imagenes_adicionales']['name'][0])) {
                        $sql_max = "SELECT COALESCE(MAX(orden), 0) AS maximo FROM imagenes_local WHERE local_id = ?";
                        $stmt_max = $conexion->prepare($sql_max);
                        $stmt_max->bind_param("i", $local_id);
                        $stmt_max->execute();
                        $orden = (int) $stmt_max->get_result()->fetch_assoc()['maximo'] + 1;
                        $stmt_max->close();

                        $directorio_subida = "uploads/";
                        $cantidad = count($_FILES['imagenes_adicionales']['name']);

                        $sql_extra = "INSERT INTO imagenes_local (local_id, ruta, orden) VALUES (?, ?, ?)";
                        $stmt_extra = $conexion->prepare($sql_extra);

                        for ($i = 0; $i < $cantidad; $i++) {
                            if ($_FILES['imagenes_adicionales']['error'][$i] !== UPLOAD_ERR_OK) {
                                continue; // esta en particular falló, seguimos con las demás
                            }

                            $nombre_extra = time() . "_" . $orden . "_" . basename($_FILES['imagenes_adicionales']['name'][$i]);
                            $ruta_extra = $directorio_subida . $nombre_extra;

                            if (move_uploaded_file($_FILES['imagenes_adicionales']['tmp_name'][$i], $ruta_extra)) {
                                $stmt_extra->bind_param("isi", $local_id, $ruta_extra, $orden);
                                $stmt_extra->execute();
                                $orden++;
                            }
                        }
                        $stmt_extra->close();
                    }

                    $conexion->close();
                    header("Location: index.php");
                    exit();
                } else {
                    echo "<h2 style='color: red;'>Error al registrar</h2>";
                    echo "<p>Hubo un problema: " . $stmt->error . "</p>";
                    echo "<br><a href='SubidaLocal.php'>Volver a intentar</a>";
                }

            } else {
                echo "<h2 style='color: red;'>Error de base de datos</h2>";
                echo "<p>" . $conexion->error . "</p>";
            }

            $conexion->close();
        } else {
            // Si entran sin mandar formulario
            header("Location: SubidaLocal.php");
            exit();
        }
        ?>

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
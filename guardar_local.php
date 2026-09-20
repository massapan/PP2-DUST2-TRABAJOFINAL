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

            // Procesamos la imagen de portada 
            $directorio_subida = "uploads/";
            $nombre_archivo = time() . "_" . basename($_FILES["imagen_portada"]["name"]);
            $ruta_imagen = $directorio_subida . $nombre_archivo;

            if (move_uploaded_file($_FILES["imagen_portada"]["tmp_name"], $ruta_imagen)) {

                // Buscamos si este vendedor ya tiene un local (y de paso, su imagen actual)
$sql_check = "SELECT id, imagen_portada FROM locales WHERE usuario_id = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$local_existente = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();

// Si NO subió una imagen nueva, y ya tenía una, mantenemos la vieja
if ($_FILES["imagen_portada"]["error"] === UPLOAD_ERR_NO_FILE && $local_existente) {
    $ruta_imagen = $local_existente['imagen_portada'];
} else {
    $directorio_subida = "uploads/";
    $nombre_archivo = time() . "_" . basename($_FILES["imagen_portada"]["name"]);
    $ruta_imagen = $directorio_subida . $nombre_archivo;
    move_uploaded_file($_FILES["imagen_portada"]["tmp_name"], $ruta_imagen);
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
            } else {
                echo "<h2 style='color: red;'>Error con la imagen</h2>";
                echo "<p>No se pudo guardar la foto de portada. Verificá que la carpeta 'uploads' exista en tu proyecto.</p>";
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
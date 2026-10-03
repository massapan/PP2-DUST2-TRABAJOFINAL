<?php
session_start();
header('Content-Type: application/json');

// Validación de seguridad estricta
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'Tenés que iniciar sesión como vendedor.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido.']);
    exit();
}

include 'conexion.php';

$usuario_id = $_SESSION['usuario_id'];

$nombre_local = trim($_POST['nombre_local']);
$direccion    = trim($_POST['direccion'] ?? '');
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

// ¿Ya tiene un local? (y de paso, su imagen de portada actual, por si no
// sube una nueva)
$sql_check = "SELECT id, imagen_portada FROM locales WHERE usuario_id = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param("i", $usuario_id);
$stmt_check->execute();
$local_existente = $stmt_check->get_result()->fetch_assoc();
$stmt_check->close();
$esEdicion = (bool) $local_existente;

// ¿Subió una foto de portada NUEVA en este envío?
$subio_portada_nueva = isset($_FILES["imagen_portada"])
    && $_FILES["imagen_portada"]["error"] !== UPLOAD_ERR_NO_FILE;

if (!$subio_portada_nueva && $local_existente) {
    // No subió nada nuevo: mantenemos la ruta que ya tenía.
    $ruta_imagen = $local_existente['imagen_portada'];
} elseif (!$subio_portada_nueva && !$local_existente) {
    // Está creando el local por primera vez y no mandó portada: no se puede.
    $conexion->close();
    echo json_encode(['exito' => false, 'mensaje' => 'Tenés que subir una foto de portada.']);
    exit();
} else {
    $directorio_subida = "uploads/";
    $nombre_archivo = time() . "_" . basename($_FILES["imagen_portada"]["name"]);
    $ruta_imagen = $directorio_subida . $nombre_archivo;

    if (!move_uploaded_file($_FILES["imagen_portada"]["tmp_name"], $ruta_imagen)) {
        $conexion->close();
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No se pudo guardar la foto de portada. Verificá que la carpeta "uploads" exista en tu proyecto.',
        ]);
        exit();
    }
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

if (!$stmt) {
    $conexion->close();
    echo json_encode(['exito' => false, 'mensaje' => 'Error de base de datos: ' . $conexion->error]);
    exit();
}

$stmt->bind_param("isssssssss", $usuario_id, $nombre_local, $direccion, $entre_calles, $descripcion, $ruta_imagen, $instagram, $whatsapp, $facebook, $tiktok);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    $conexion->close();
    echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar en la base de datos: ' . $error]);
    exit();
}
$stmt->close();

// insert_id NO es confiable acá: con ON DUPLICATE KEY UPDATE, si fue un
// UPDATE puede no traer el id correcto. Lo buscamos de nuevo, así funciona
// igual de bien si fue creación o edición.
$sql_id = "SELECT id FROM locales WHERE usuario_id = ?";
$stmt_id = $conexion->prepare($sql_id);
$stmt_id->bind_param("i", $usuario_id);
$stmt_id->execute();
$local_id = $stmt_id->get_result()->fetch_assoc()['id'];
$stmt_id->close();

// Portada = orden 0 en la galería. Solo la tocamos si subió una foto nueva;
// si no, dejamos la fila vieja como está.
if ($subio_portada_nueva) {
    $sql_portada = "INSERT INTO imagenes_local (local_id, ruta, orden) VALUES (?, ?, 0)
                    ON DUPLICATE KEY UPDATE ruta = VALUES(ruta)";
    $stmt_portada = $conexion->prepare($sql_portada);
    $stmt_portada->bind_param("is", $local_id, $ruta_imagen);
    $stmt_portada->execute();
    $stmt_portada->close();
}

// Fotos adicionales: se AGREGAN a la galería, nunca borran las que ya
// había. Seguimos el orden donde quedó la vez anterior (por si el vendedor
// edita el local más de una vez).
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
            continue;
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

echo json_encode([
    'exito' => true,
    'mensaje' => $esEdicion
        ? 'Los cambios en "' . $nombre_local . '" se guardaron con éxito.'
        : '¡Tu local "' . $nombre_local . '" se registró con éxito!',
    'local_id' => $local_id,
]);
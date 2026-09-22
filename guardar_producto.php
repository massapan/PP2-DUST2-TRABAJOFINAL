<?php
session_start();
header('Content-Type: application/json');

// Validación estricta: Solo vendedores logueados.
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

// 1. Buscamos el local de este usuario
$sql_local = "SELECT id FROM locales WHERE usuario_id = ?";
$stmt_local = $conexion->prepare($sql_local);
$stmt_local->bind_param("i", $usuario_id);
$stmt_local->execute();
$resultado = $stmt_local->get_result();

if ($resultado->num_rows === 0) {
    $stmt_local->close();
    $conexion->close();
    echo json_encode([
        'exito' => false,
        'mensaje' => 'Todavía no tenés un local registrado. Registrá uno antes de subir productos.',
    ]);
    exit();
}

$local = $resultado->fetch_assoc();
$local_id = $local['id'];
$stmt_local->close();

// 2. ¿Es una edición? Si vino producto_id, verificamos que sea DE ESTE
//    local antes de tocar nada — si no, cualquiera podría editar un
//    producto ajeno con solo mandar otro id en el formulario.
$producto_id = null;
$esEdicion = false;

if (!empty($_POST['producto_id'])) {
    $producto_id_solicitado = (int) $_POST['producto_id'];

    $sqlCheck = "SELECT id FROM productos WHERE id = ? AND local_id = ?";
    $stmtCheck = $conexion->prepare($sqlCheck);
    $stmtCheck->bind_param("ii", $producto_id_solicitado, $local_id);
    $stmtCheck->execute();
    $existe = $stmtCheck->get_result()->fetch_assoc();
    $stmtCheck->close();

    if (!$existe) {
        $conexion->close();
        echo json_encode(['exito' => false, 'mensaje' => 'Ese producto no existe o no te pertenece.']);
        exit();
    }

    $producto_id = $producto_id_solicitado;
    $esEdicion = true;
}

// 3. Capturamos textos
$nombre_producto = trim($_POST['nombre_producto']);
$descripcion     = trim($_POST['descripcion'] ?? '');
$talle           = trim($_POST['talle'] ?? '');
$talle           = $talle !== '' ? $talle : null;
$precio          = $_POST['precio'];
$categoria_id    = (int) $_POST['categoria_id'];

$directorio_subida = "uploads/";

// 4. ¿Subió una foto principal NUEVA en este envío?
$subioPortadaNueva = isset($_FILES['imagen']) && $_FILES['imagen']['error'] !== UPLOAD_ERR_NO_FILE;
$ruta_principal = null;

if ($subioPortadaNueva) {
    $nombre_archivo = time() . "_" . basename($_FILES["imagen"]["name"]);
    $ruta_principal = $directorio_subida . $nombre_archivo;

    if (!move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_principal)) {
        $conexion->close();
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No se pudo guardar la foto. Verificá que la carpeta "uploads" exista en tu proyecto.',
        ]);
        exit();
    }
} elseif (!$esEdicion) {
    // Creando un producto nuevo: la portada es obligatoria.
    $conexion->close();
    echo json_encode(['exito' => false, 'mensaje' => 'Tenés que subir una foto principal.']);
    exit();
}

if ($esEdicion) {

    // 5a. Actualizamos el producto existente
    $sql_prod = "UPDATE productos SET nombre_producto = ?, descripcion = ?, talle = ?, precio = ?, categoria_id = ? WHERE id = ?";
    $stmt_prod = $conexion->prepare($sql_prod);
    // "sssdii" = string, string, string, double, integer, integer
    $stmt_prod->bind_param("sssdii", $nombre_producto, $descripcion, $talle, $precio, $categoria_id, $producto_id);

    if (!$stmt_prod->execute()) {
        $error = $stmt_prod->error;
        $stmt_prod->close();
        $conexion->close();
        echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar en la base de datos: ' . $error]);
        exit();
    }
    $stmt_prod->close();

    // Si subió una portada nueva, reemplazamos la fila de orden 0.
    // Si no subió nada, ni tocamos esa fila: la vieja queda como estaba.
    if ($subioPortadaNueva) {
        $sql_img = "INSERT INTO imagenes_producto (producto_id, ruta, orden) VALUES (?, ?, 0)
                    ON DUPLICATE KEY UPDATE ruta = VALUES(ruta)";
        $stmt_img = $conexion->prepare($sql_img);
        $stmt_img->bind_param("is", $producto_id, $ruta_principal);
        $stmt_img->execute();
        $stmt_img->close();
    }

    $mensaje = 'Los cambios en "' . $nombre_producto . '" se guardaron con éxito.';

} else {

    // 5b. Creamos el producto nuevo
    $sql_prod = "INSERT INTO productos (local_id, nombre_producto, descripcion, talle, precio, categoria_id) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt_prod = $conexion->prepare($sql_prod);
    // "isssdi" = integer, string, string, string, double, integer
    $stmt_prod->bind_param("isssdi", $local_id, $nombre_producto, $descripcion, $talle, $precio, $categoria_id);

    if (!$stmt_prod->execute()) {
        $error = $stmt_prod->error;
        $stmt_prod->close();
        $conexion->close();
        echo json_encode(['exito' => false, 'mensaje' => 'Error al guardar en la base de datos: ' . $error]);
        exit();
    }

    $producto_id = $conexion->insert_id;
    $stmt_prod->close();

    // Portada = orden 0 (ya la validamos como obligatoria más arriba)
    $sql_img = "INSERT INTO imagenes_producto (producto_id, ruta, orden) VALUES (?, ?, 0)";
    $stmt_img = $conexion->prepare($sql_img);
    $stmt_img->bind_param("is", $producto_id, $ruta_principal);
    $stmt_img->execute();
    $stmt_img->close();

    $mensaje = 'El artículo "' . $nombre_producto . '" ya está guardado en tu local.';
}

// 6. Fotos adicionales: se AGREGAN a continuación de las que ya había
//    (tanto si es un producto nuevo como si es una edición), nunca
//    borran ni pisan las existentes.
if (!empty($_FILES['imagenes_adicionales']['name'][0])) {
    $sql_max = "SELECT COALESCE(MAX(orden), 0) AS maximo FROM imagenes_producto WHERE producto_id = ?";
    $stmt_max = $conexion->prepare($sql_max);
    $stmt_max->bind_param("i", $producto_id);
    $stmt_max->execute();
    $orden = (int) $stmt_max->get_result()->fetch_assoc()['maximo'] + 1;
    $stmt_max->close();

    $cantidad = count($_FILES['imagenes_adicionales']['name']);

    $sql_img_extra = "INSERT INTO imagenes_producto (producto_id, ruta, orden) VALUES (?, ?, ?)";
    $stmt_img_extra = $conexion->prepare($sql_img_extra);

    for ($i = 0; $i < $cantidad; $i++) {
        if ($_FILES['imagenes_adicionales']['error'][$i] !== UPLOAD_ERR_OK) {
            continue; // esa imagen en particular falló, seguimos con las demás
        }

        $nombre_extra = time() . "_" . $orden . "_" . basename($_FILES['imagenes_adicionales']['name'][$i]);
        $ruta_extra = $directorio_subida . $nombre_extra;

        if (move_uploaded_file($_FILES['imagenes_adicionales']['tmp_name'][$i], $ruta_extra)) {
            $stmt_img_extra->bind_param("isi", $producto_id, $ruta_extra, $orden);
            $stmt_img_extra->execute();
            $orden++;
        }
    }
    $stmt_img_extra->close();
}

$conexion->close();

echo json_encode([
    'exito' => true,
    'mensaje' => $mensaje,
    'producto_id' => $producto_id,
]);
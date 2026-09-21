<?php
session_start();
header('Content-Type: application/json');

// Validación estricta: Solo vendedores logueados.
// Como esto ahora se llama por fetch() y no por navegación normal, un
// header("Location: ...") no le serviría de nada al JS que lo llamó —
// por eso contestamos JSON, igual que hace toggle_favorito.php.
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

// 1. Buscamos el ID del local de este usuario
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

// 2. Capturamos textos
$nombre_producto = trim($_POST['nombre_producto']);
$descripcion     = trim($_POST['descripcion'] ?? '');
$talle           = trim($_POST['talle'] ?? '');
$talle           = $talle !== '' ? $talle : null;
$precio          = $_POST['precio'];
$categoria_id    = (int) $_POST['categoria_id'];

// 3. Procesamos la imagen principal
$directorio_subida = "uploads/";
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

// 4. Guardamos el producto (con descripcion, talle y categoria_id)
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

// 5. Guardamos la imagen principal con orden 0
$sql_img = "INSERT INTO imagenes_producto (producto_id, ruta, orden) VALUES (?, ?, 0)";
$stmt_img = $conexion->prepare($sql_img);
$stmt_img->bind_param("is", $producto_id, $ruta_principal);
$stmt_img->execute();
$stmt_img->close();

// 6. Guardamos las imágenes adicionales, en el orden en que el navegador
//    las mandó (1, 2, 3...). Si el vendedor no cargó ninguna, este bloque
//    simplemente no hace nada.
if (!empty($_FILES['imagenes_adicionales']['name'][0])) {
    $cantidad = count($_FILES['imagenes_adicionales']['name']);
    $orden = 1;

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
    'mensaje' => 'El artículo "' . $nombre_producto . '" ya está guardado en tu local.',
    'producto_id' => $producto_id,
]);
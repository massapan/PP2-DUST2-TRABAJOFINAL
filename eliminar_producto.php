<?php
// Borra un producto del vendedor logueado. Responde JSON (igual que
// guardar_producto.php) para que lo llame el JS con fetch() sin salir
// de la página "Mis productos".
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    http_response_code(403);
    echo json_encode(['exito' => false, 'mensaje' => 'Tenés que iniciar sesión como vendedor.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['exito' => false, 'mensaje' => 'Método no permitido.']);
    exit();
}

$producto_id = (int) ($_POST['producto_id'] ?? 0);
if ($producto_id <= 0) {
    echo json_encode(['exito' => false, 'mensaje' => 'Producto inválido.']);
    exit();
}

include 'conexion.php';
$usuario_id = $_SESSION['usuario_id'];

// 1. ¿El producto existe Y es de un local de ESTE usuario?
//    Sin este JOIN, cualquier vendedor podría borrar productos ajenos
//    mandando otro id desde las DevTools.
$sql = "SELECT p.id, p.nombre_producto
        FROM productos p
        INNER JOIN locales l ON p.local_id = l.id
        WHERE p.id = ? AND l.usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $producto_id, $usuario_id);
$stmt->execute();
$producto = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$producto) {
    $conexion->close();
    echo json_encode(['exito' => false, 'mensaje' => 'Ese producto no existe o no te pertenece.']);
    exit();
}

// 2. Guardamos las rutas de las fotos ANTES de borrar: cuando se borre
//    el producto, ON DELETE CASCADE elimina las filas de imagenes_producto
//    y de favoritos_productos, y ya no sabríamos qué archivos limpiar.
$rutas = [];
$stmt = $conexion->prepare("SELECT ruta FROM imagenes_producto WHERE producto_id = ?");
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$res = $stmt->get_result();
while ($fila = $res->fetch_assoc()) {
    $rutas[] = $fila['ruta'];
}
$stmt->close();

// 3. Borramos el producto
$stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
$stmt->bind_param("i", $producto_id);

if (!$stmt->execute()) {
    $error = $stmt->error;
    $stmt->close();
    $conexion->close();
    echo json_encode(['exito' => false, 'mensaje' => 'No se pudo borrar: ' . $error]);
    exit();
}
$stmt->close();
$conexion->close();

// 4. Borramos los archivos de uploads/ (solo si la ruta está adentro de esa
//    carpeta y no tiene ".." para que nadie pueda salir de ella).
foreach ($rutas as $ruta) {
    if (strpos($ruta, 'uploads/') === 0 && strpos($ruta, '..') === false && is_file($ruta)) {
        @unlink($ruta);
    }
}

echo json_encode([
    'exito' => true,
    'mensaje' => 'El producto "' . $producto['nombre_producto'] . '" se eliminó.',
]);
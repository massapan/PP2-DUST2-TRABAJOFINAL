<?php
// Endpoint AJAX: agrega o saca un producto/local de favoritos.
// Se llama desde JS/favoritos.js (fetch), nunca se navega directo acá.

header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Requiere sesión. El front-end (favoritos.js) interpreta el 401
// y redirige a login.php.
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'sin_sesion']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'metodo_no_permitido']);
    exit();
}

include 'conexion.php';

// El usuario SIEMPRE sale de la sesión, nunca de un campo del POST
// (mismo criterio que ya usamos al arreglar actualizar_password.php).
$usuario_id = $_SESSION['usuario_id'];

$tipo = $_POST['tipo'] ?? '';
$id   = isset($_POST['id']) ? (int) $_POST['id'] : 0;

// Whitelist estricta de tipo: nunca se arma la consulta con texto
// que venga directo del cliente.
if ($tipo === 'producto') {
    $tabla = 'favoritos_productos';
    $columna_id = 'producto_id';
} elseif ($tipo === 'local') {
    $tabla = 'favoritos_locales';
    $columna_id = 'local_id';
} else {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'tipo_invalido']);
    exit();
}

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'id_invalido']);
    exit();
}

// ¿Ya es favorito? (usamos consultas preparadas siempre)
$sql_check = "SELECT 1 FROM $tabla WHERE usuario_id = ? AND $columna_id = ?";
$stmt_check = $conexion->prepare($sql_check);
$stmt_check->bind_param("ii", $usuario_id, $id);
$stmt_check->execute();
$stmt_check->store_result();
$ya_es_favorito = $stmt_check->num_rows > 0;
$stmt_check->close();

if ($ya_es_favorito) {
    // Ya estaba: lo sacamos.
    $sql = "DELETE FROM $tabla WHERE usuario_id = ? AND $columna_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $id);
    $exito = $stmt->execute();
    $stmt->close();
    $nuevo_estado = false;
} else {
    // No estaba: lo agregamos.
    $sql = "INSERT INTO $tabla (usuario_id, $columna_id) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $id);
    $exito = $stmt->execute();
    $stmt->close();
    $nuevo_estado = true;
}

$conexion->close();

if (!$exito) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'error_bd']);
    exit();
}

echo json_encode(['ok' => true, 'favorito' => $nuevo_estado]);

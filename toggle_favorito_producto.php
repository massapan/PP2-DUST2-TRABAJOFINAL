<?php
session_start();
include 'conexion.php';
header('Content-Type: application/json');

// Solo compradores logueados pueden marcar favoritos.
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'comprador') {
    http_response_code(403);
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$producto_id = isset($_POST['producto_id']) ? (int) $_POST['producto_id'] : 0;

if ($producto_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Prenda inválida']);
    exit();
}

$sql = "SELECT 1 FROM favoritos_productos WHERE usuario_id = ? AND producto_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $producto_id);
$stmt->execute();
$yaEsFavorito = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($yaEsFavorito) {
    $sql = "DELETE FROM favoritos_productos WHERE usuario_id = ? AND producto_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['favorito' => false]);
} else {
    $sql = "INSERT INTO favoritos_productos (usuario_id, producto_id) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $producto_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['favorito' => true]);
}

$conexion->close();
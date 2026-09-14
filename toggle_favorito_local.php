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
$local_id = isset($_POST['local_id']) ? (int) $_POST['local_id'] : 0;

if ($local_id <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Local inválido']);
    exit();
}

$sql = "SELECT 1 FROM favoritos_locales WHERE usuario_id = ? AND local_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("ii", $usuario_id, $local_id);
$stmt->execute();
$yaEsFavorito = $stmt->get_result()->num_rows > 0;
$stmt->close();

if ($yaEsFavorito) {
    $sql = "DELETE FROM favoritos_locales WHERE usuario_id = ? AND local_id = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $local_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['favorito' => false]);
} else {
    $sql = "INSERT INTO favoritos_locales (usuario_id, local_id) VALUES (?, ?)";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("ii", $usuario_id, $local_id);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['favorito' => true]);
}

$conexion->close();
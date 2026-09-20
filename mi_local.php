<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}

include 'conexion.php';

$sql = "SELECT id FROM locales WHERE usuario_id = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$local = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conexion->close();

if ($local) {
    header("Location: index.php?pagina=local&id=" . $local['id']);
} else {
    header("Location: SubidaLocal.php");
}
exit();
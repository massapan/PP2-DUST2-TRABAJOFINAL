<?php
ob_start();
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT id, password, rol FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['password'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['rol']        = $usuario['rol'];

    if ($_SESSION['rol'] === 'vendedor') {

        // Chequeamos si este vendedor ya tiene un local registrado
        $sql_local = "SELECT id FROM locales WHERE usuario_id = ?";
        $stmt_local = $conexion->prepare($sql_local);
        $stmt_local->bind_param("i", $usuario['id']);
        $stmt_local->execute();
        $resultado_local = $stmt_local->get_result();

        if ($resultado_local->num_rows > 0) {
            // Ya tiene local → navegación normal
            header("Location: index.php");
        } else {
            // Todavía no tiene local → lo mandamos a crearlo
            header("Location: SubidaLocal.php");
        }
        $stmt_local->close();

    } else {
        // Es comprador → siempre a index.php
        header("Location: index.php");
    }

    exit();
    }
}
}
ob_end_flush();
?>
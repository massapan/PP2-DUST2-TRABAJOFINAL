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
            $_SESSION['rol'] = $usuario['rol'];
            
            // --- ACÁ ESTÁ LA MAGIA DE LA REDIRECCIÓN POR ROL ---
            if ($usuario['rol'] === 'comprador') {
                // Si es comprador, va directo a ver los productos
                header("Location: catalogo.php");
            } else {
                // Si es vendedor, va a gestionar su negocio
                header("Location: local.php");
            }
            exit();
            // ---------------------------------------------------

        } else {
            header("Location: login.php?error=incorrecta");
            exit();
        }
    } else {
        header("Location: login.php?error=no_existe");
        exit();
    }
    $stmt->close();
} else {
    header("Location: login.php");
    exit();
}
ob_end_flush();
?>
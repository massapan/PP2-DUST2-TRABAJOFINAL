<?php
ob_start();
session_start();
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    if (strlen($password) < 8) {
        header("Location: registro.php?error=password_corta");
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: registro.php?error=email_invalido");
        exit();
    }

    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->close();
        header("Location: registro.php?error=duplicado");
        exit();
    }
    $stmt->close();

    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO usuarios (email, password, rol) VALUES (?, ?, ?)";
    $stmt_insert = $conexion->prepare($sql_insert);
    $stmt_insert->bind_param("sss", $email, $password_encriptada, $rol);

    if ($stmt_insert->execute()) {
        header("Location: login.php?registro=ok");
        exit();
    } else {
        header("Location: registro.php?error=duplicado");
        exit();
    }

} else {
    header("Location: registro.php");
    exit();
}
ob_end_flush();
?>
<?php
ob_start(); // Iniciamos el buffer de salida para evitar errores de redirección
include 'conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    $password_encriptada = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (email, password, rol) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $email, $password_encriptada, $rol);

        try {
            $stmt->execute();
            // ¡ÉXITO! Lo mandamos directo a iniciar sesión. 
            header("Location: login.php"); 
            exit();
            
        } catch (mysqli_sql_exception $e) {
            // ERROR 1062: Correo duplicado
            if ($e->getCode() == 1062) {
                // Lo pateamos de vuelta al registro, pero le mandamos la "señal" de error por la URL
                header("Location: registro.php?error=duplicado");
                exit();
            } else {
                echo "Error crítico: " . $e->getMessage();
            }
        }
        $stmt->close();
    }
    $conexion->close();
} else {
    header("Location: registro.php");
    exit();
}
ob_end_flush();
?>
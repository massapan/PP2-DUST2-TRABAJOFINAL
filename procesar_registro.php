<?php
ob_start(); // Iniciamos el buffer de salida para evitar errores de redirección
session_start();
include 'conexion.php';
include 'config_2fa.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $rol = $_POST['rol'];

    // 1) Antes de nada, verificamos que el correo no esté ya registrado.
    $sql = "SELECT id FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Correo duplicado -> lo mandamos de vuelta con la señal de error.
        $stmt->close();
        header("Location: registro.php?error=duplicado");
        exit();
    }
    $stmt->close();

    // 2) Todavía NO creamos la cuenta. Primero verificamos el email.
    //    Guardamos el "registro pendiente" en la sesión (incluida la
    //    contraseña ya encriptada) y mandamos un código de verificación.
    $codigo = generar_codigo_2fa();

    $_SESSION['registro_pendiente'] = [
        'email'    => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'rol'      => $rol,
        'codigo'   => $codigo,
        'expira'   => time() + DURACION_CODIGO_2FA,
        'intentos' => 0
    ];

    // Enviamos el código (por email real o, en demo, a pantalla).
    enviar_codigo_2fa($email, $codigo);

    // Vamos a la pantalla donde el usuario ingresa el código.
    header("Location: verificar_registro.php");
    exit();

} else {
    header("Location: registro.php");
    exit();
}
ob_end_flush();
?>

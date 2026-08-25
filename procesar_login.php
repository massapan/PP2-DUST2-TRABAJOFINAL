<?php
ob_start();
session_start();
include 'conexion.php';
include 'config_2fa.php';

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

            // --- PASO 1 DEL LOGIN OK: contraseña correcta ---
            // Todavía NO iniciamos sesión. Primero pedimos el 2FA.
            $codigo = generar_codigo_2fa();

            // Guardamos el "login pendiente" en la sesión (no en la BD).
            $_SESSION['2fa_pendiente'] = [
                'usuario_id' => $usuario['id'],
                'rol'        => $usuario['rol'],
                'email'      => $email,
                'codigo'     => $codigo,
                'expira'     => time() + DURACION_CODIGO_2FA,
                'intentos'   => 0
            ];

            // Enviamos el código (por email real o, en demo, a pantalla).
            enviar_codigo_2fa($email, $codigo);

            // Vamos a la pantalla donde el usuario ingresa el código.
            header("Location: verificar_2fa.php");
            exit();
            // ------------------------------------------------

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
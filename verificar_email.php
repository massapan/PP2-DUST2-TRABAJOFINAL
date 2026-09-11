<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
    <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-centrada pagina-auth">
    <div class="fondo"></div>
        <?php
        session_start();
        include 'conexion.php';
        include 'config_2fa.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];

            $sql = "SELECT id, email FROM usuarios WHERE email = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();

                $codigo = generar_codigo_2fa();

                $_SESSION['reset_pendiente'] = [
                    'usuario_id' => $usuario['id'],
                    'email'      => $usuario['email'],
                    'codigo'     => $codigo,
                    'expira'     => time() + DURACION_CODIGO_2FA,
                    'intentos'   => 0
                ];

                enviar_codigo_2fa($usuario['email'], $codigo);

                header("Location: verificar_reset.php");
                exit();

            } else {
                echo "<div class='caja-auth'>";
                echo "<h2 style='color: red;'>Error</h2>";
                echo "<p>No encontramos ninguna cuenta registrada con ese correo.</p>";
                echo "<a href='recuperar.html' class='Boton-secundario'>Volver a intentar</a>";
                echo "</div>";
            }

            $stmt->close();
            $conexion->close();
        } else {
            header("Location: recuperar.html");
            exit();
        }
        ?>
</body>
</html>
<?php ob_end_flush(); ?>
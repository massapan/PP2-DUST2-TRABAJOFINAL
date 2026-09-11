<?php ob_start(); session_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Contraseña - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>
<body class="pagina-centrada pagina-auth">
    <div class="fondo"></div>
    <div class="caja-auth">

        <?php
        // Si no pasaste por la verificación del código, no podés estar acá.
        if (!isset($_SESSION['reset_autorizado'])) {
            header("Location: recuperar.html");
            exit();
        }

        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // El id sale de la sesión (ya verificado), no del formulario.
            $usuario_id = $_SESSION['reset_autorizado'];
            $nueva_password = $_POST['nueva_password'];

            $password_encriptada = password_hash($nueva_password, PASSWORD_DEFAULT);

            $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("si", $password_encriptada, $usuario_id);

                if ($stmt->execute()) {
                    // Ya se usó: limpiamos para que no se pueda reusar la sesión.
                    unset($_SESSION['reset_autorizado']);

                    echo "<h1 style='color: #178017;'>¡Contraseña actualizada!</h1>";
                    echo "<p>Tu clave se cambió con éxito en la base de datos.</p>";
                    echo "<br><p><a href='login.php' style='font-weight: bold;' class='Boton-secundario'>Ir a Iniciar Sesión</a></p>";
                } else {
                    echo "<h2 style='color: red;'>Error</h2>";
                    echo "<p>Hubo un problema al actualizar: " . $stmt->error . "</p>";
                }
                $stmt->close();
            } else {
                echo "<h2 style='color: red;'>Error de base de datos</h2>";
                echo "<p>" . $conexion->error . "</p>";
            }
            $conexion->close();

        } else {
            // Todavía no mandó el formulario: se lo mostramos.
            ?>
            <h1>Elegí tu nueva contraseña</h1>
            <p>Ya verificamos tu identidad. Escribí la nueva contraseña para tu cuenta.</p>

            <form action="actualizar_password.php" method="POST">
                <label for="nueva_password">Nueva contraseña:</label>
                <input type="password" id="nueva_password" name="nueva_password" minlength="8" required>

                <button type="submit">Guardar Cambios</button>
            </form>
            <?php
        }
        ?>

    </div>
    <script src="JS/ver-password.js"></script>
</body>
</html>
<?php ob_end_flush(); ?>
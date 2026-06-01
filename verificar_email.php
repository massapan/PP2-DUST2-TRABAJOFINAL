<?php ob_start(); // Siempre arriba de todo si vamos a usar header() ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="contenedor">
        
        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $email = $_POST['email'];

            $sql = "SELECT id, email FROM usuarios WHERE email = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $resultado = $stmt->get_result();

            if ($resultado->num_rows === 1) {
                $usuario = $resultado->fetch_assoc();
                ?>
                
                <div class="container">
                <h2>Recuperar Contraseña</h2>
                <p>Cuenta encontrada: <strong><?php echo $usuario['email']; ?></strong></p>
                
                <form action="actualizar_password.php" method="POST">
                    <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                    
                    <label for="nueva_password">Escribe tu nueva contraseña:</label>
                    <input type="password" id="nueva_password" name="nueva_password" required>
                    
                    <button type="submit">Guardar Cambios</button>
                </form>
                </div>  
                <?php
            } else {
                echo "<h2 style='color: red;'>Error</h2>";
                echo "<p>No encontramos ninguna cuenta registrada con ese correo.</p>";
                echo "<p><a href='recuperar.html'>Volver a intentar</a></p>";
            }

            $stmt->close();
            $conexion->close();
        } else {
            header("Location: recuperar.html");
            exit();
        }
        ?>

    </div> </body>
</html>
<?php ob_end_flush(); ?>
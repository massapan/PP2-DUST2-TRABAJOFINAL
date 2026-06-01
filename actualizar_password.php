<?php ob_start(); ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Contraseña Actualizada - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Recibimos los datos ocultos y la nueva contraseña
            $usuario_id = $_POST['usuario_id'];
            $nueva_password = $_POST['nueva_password'];

            // Encriptamos la contraseña nueva
            $password_encriptada = password_hash($nueva_password, PASSWORD_DEFAULT);

            // Preparamos la consulta para actualizar el registro
            $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            
           
            if ($stmt) {
                // "si" = string, integer
                $stmt->bind_param("si", $password_encriptada, $usuario_id);
                
                if ($stmt->execute()) {
                    echo "<h1 style='color: #178017;'>¡Contraseña actualizada!</h1>";
                    echo "<p>Tu clave se cambió con éxito en la base de datos.</p>";
                    // Apuntamos al login.php correcto
                    echo "<br><p><a href='login.php' style='font-weight: bold;'>Ir a Iniciar Sesión</a></p>"; 
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
            // Si entran directo por URL, los mandamos a recuperar
            header("Location: recuperar.html");
            exit();
        }
        ?>

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
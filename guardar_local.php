<?php
ob_start();
session_start();

// Validación de seguridad estricta
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado del Local - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Tomamos el ID del vendedor directamente de la sesión
            $usuario_id = $_SESSION['usuario_id']; 
            
            // Capturamos los datos
            $nombre_local = $_POST['nombre_local'];
            $direccion = $_POST['direccion'];
            $descripcion = $_POST['descripcion'];

            $sql = "INSERT INTO locales (usuario_id, nombre_local, direccion, descripcion) VALUES (?, ?, ?, ?)";
            $stmt = $conexion->prepare($sql);
            
            if ($stmt) {
                $stmt->bind_param("isss", $usuario_id, $nombre_local, $direccion, $descripcion);
                
                if ($stmt->execute()) {
                    echo "<h2 style='color: #28a745;'>¡Tu local se ha registrado con éxito!</h2>";
                    echo "<p>El negocio <strong>$nombre_local</strong> ya está en nuestra base de datos.</p>";
                    echo "<p>Ya podés empezar a agregar tu mercadería para que la vean los compradores.</p>";
                    echo "<br><a href='productos.php' style='font-weight: bold;'>Ir a cargar productos</a>";
                } else {
                    echo "<h2 style='color: red;'>Error al registrar</h2>";
                    echo "<p>Hubo un problema: " . $stmt->error . "</p>";
                    echo "<br><a href='local.php'>Volver a intentar</a>";
                }
                $stmt->close();
            } else {
                echo "<h2 style='color: red;'>Error de base de datos</h2>";
                echo "<p>" . $conexion->error . "</p>";
            }
            $conexion->close();
        } else {
            // Si entran sin mandar formulario
            header("Location: local.php");
            exit();
        }
        ?>

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
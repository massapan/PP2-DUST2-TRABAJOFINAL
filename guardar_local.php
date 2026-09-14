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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Local - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-centrada">
    <div class="fondo"></div>

    <div class="container">

        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Tomamos el ID del vendedor directamente de la sesión
            $usuario_id = $_SESSION['usuario_id'];

            // Capturamos los datos
            $nombre_local = trim($_POST['nombre_local']);
            $direccion    = trim($_POST['direccion']);
            $entre_calles = trim($_POST['entre_calles'] ?? '');
            $descripcion  = trim($_POST['descripcion']);

            // Procesamos la imagen de portada 
            $directorio_subida = "uploads/";
            $nombre_archivo = time() . "_" . basename($_FILES["imagen_portada"]["name"]);
            $ruta_imagen = $directorio_subida . $nombre_archivo;

            if (move_uploaded_file($_FILES["imagen_portada"]["tmp_name"], $ruta_imagen)) {

                $sql = "INSERT INTO locales (usuario_id, nombre_local, direccion, entre_calles, descripcion, imagen_portada) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $conexion->prepare($sql);

                if ($stmt) {
                    $stmt->bind_param("isssss", $usuario_id, $nombre_local, $direccion, $entre_calles, $descripcion, $ruta_imagen);

                    if ($stmt->execute()) {
                        echo "<h2 style='color: #28a745;'>¡Tu local se ha registrado con éxito!</h2>";
                        echo "<p>El negocio <strong>" . htmlspecialchars($nombre_local) . "</strong> ya está en nuestra base de datos.</p>";
                        echo "<p>Ya podés empezar a agregar tu mercadería para que la vean los compradores.</p>";
                        echo "<br><a href='productos.php' style='font-weight: bold;' class='Boton-secundario'>Ir a cargar productos</a>";
                    } else {
                        echo "<h2 style='color: red;'>Error al registrar</h2>";
                        echo "<p>Hubo un problema: " . $stmt->error . "</p>";
                        echo "<br><a href='SubidaLocal.php'>Volver a intentar</a>";
                    }
                    $stmt->close();
                } else {
                    echo "<h2 style='color: red;'>Error de base de datos</h2>";
                    echo "<p>" . $conexion->error . "</p>";
                }
            } else {
                echo "<h2 style='color: red;'>Error con la imagen</h2>";
                echo "<p>No se pudo guardar la foto de portada. Verificá que la carpeta 'uploads' exista en tu proyecto.</p>";
            }

            $conexion->close();
        } else {
            // Si entran sin mandar formulario
            header("Location: SubidaLocal.php");
            exit();
        }
        ?>

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
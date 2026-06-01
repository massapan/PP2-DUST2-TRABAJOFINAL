<?php
ob_start();
session_start();

// Validación estricta: Solo vendedores logueados
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultado del Producto - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <?php
        include 'conexion.php';

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $usuario_id = $_SESSION['usuario_id'];
            
            // 1. Buscamos el ID del local de este usuario
            $sql_local = "SELECT id FROM locales WHERE usuario_id = ?";
            $stmt_local = $conexion->prepare($sql_local);
            $stmt_local->bind_param("i", $usuario_id);
            $stmt_local->execute();
            $resultado = $stmt_local->get_result();
            
            if ($resultado->num_rows > 0) {
                $local = $resultado->fetch_assoc();
                $local_id = $local['id'];
                
                // 2. Capturamos textos
                $nombre_producto = $_POST['nombre_producto'];
                $precio = $_POST['precio'];
                
                // 3. Procesamos la imagen
                $directorio_subida = "uploads/";
                $nombre_archivo = time() . "_" . basename($_FILES["imagen"]["name"]);
                $ruta_final = $directorio_subida . $nombre_archivo;
                
                // Movemos la foto a la carpeta
                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_final)) {
                    
                    // 4. Guardamos en MySQL
                    $sql_prod = "INSERT INTO productos (local_id, nombre_producto, precio, imagen_ruta) VALUES (?, ?, ?, ?)";
                    $stmt_prod = $conexion->prepare($sql_prod);
                    
                    // "isds" = integer, string, double, string
                    $stmt_prod->bind_param("isds", $local_id, $nombre_producto, $precio, $ruta_final);
                    
                    if ($stmt_prod->execute()) {
                        echo "<h2 style='color: #28a745;'>¡Producto subido con éxito!</h2>";
                        echo "<p>El artículo <strong>$nombre_producto</strong> ya está guardado en tu local.</p>";
                        echo "<br><p><a href='productos.php' style='font-weight: bold;'>Subir otro producto</a></p>";
                    } else {
                        echo "<h2 style='color: red;'>Error en MySQL</h2>";
                        echo "<p>" . $stmt_prod->error . "</p>";
                    }
                    $stmt_prod->close();
                    
                } else {
                    echo "<h2 style='color: red;'>Error con la imagen</h2>";
                    echo "<p>No se pudo guardar la foto. Verificá que la carpeta 'uploads' exista en tu proyecto.</p>";
                }
            } else {
                echo "<h2 style='color: red;'>Atención</h2>";
                echo "<p>No tenés un local registrado. Tenés que registrar uno antes de subir productos.</p>";
                echo "<br><p><a href='local.php'>Ir a registrar mi local</a></p>";
            }
            
            $stmt_local->close();
            $conexion->close();
        } else {
            header("Location: productos.php");
            exit();
        }
        ?>

        <hr style="margin-top: 20px; margin-bottom: 20px; border: 0; border-top: 1px solid #eee;">
        
        <b><p><a href="catalogo.php">Catálogo público</a></p></b>

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
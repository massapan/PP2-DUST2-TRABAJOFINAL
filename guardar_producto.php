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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado del Producto - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-centrada">
<div class="fondo"></div>
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
                $nombre_producto = trim($_POST['nombre_producto']);
                $descripcion     = trim($_POST['descripcion'] ?? '');
                $precio          = $_POST['precio'];
                $categoria_id    = (int) $_POST['categoria_id'];

                // 3. Procesamos la imagen principal
                $directorio_subida = "uploads/";
                $nombre_archivo = time() . "_" . basename($_FILES["imagen"]["name"]);
                $ruta_principal = $directorio_subida . $nombre_archivo;

                if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_principal)) {

                    // 4. Guardamos el producto (con descripcion y categoria_id)
                    $sql_prod = "INSERT INTO productos (local_id, nombre_producto, descripcion, precio, categoria_id) VALUES (?, ?, ?, ?, ?)";
                    $stmt_prod = $conexion->prepare($sql_prod);

                    // "issdi" = integer, string, string, double, integer
                    $stmt_prod->bind_param("issdi", $local_id, $nombre_producto, $descripcion, $precio, $categoria_id);

                    if ($stmt_prod->execute()) {
                        $producto_id = $conexion->insert_id;

                        // 5. Guardamos la imagen principal con orden 0
                        $sql_img = "INSERT INTO imagenes_producto (producto_id, ruta, orden) VALUES (?, ?, 0)";
                        $stmt_img = $conexion->prepare($sql_img);
                        $stmt_img->bind_param("is", $producto_id, $ruta_principal);
                        $stmt_img->execute();
                        $stmt_img->close();

                        // 6. Guardamos las imágenes adicionales, en el orden en que
                        //    el navegador las mandó (1, 2, 3...). Si el vendedor no
                        //    cargó ninguna, este bloque simplemente no hace nada.
                        if (!empty($_FILES['imagenes_adicionales']['name'][0])) {
                            $cantidad = count($_FILES['imagenes_adicionales']['name']);
                            $orden = 1;

                            $sql_img_extra = "INSERT INTO imagenes_producto (producto_id, ruta, orden) VALUES (?, ?, ?)";
                            $stmt_img_extra = $conexion->prepare($sql_img_extra);

                            for ($i = 0; $i < $cantidad; $i++) {
                                if ($_FILES['imagenes_adicionales']['error'][$i] !== UPLOAD_ERR_OK) {
                                    continue; // esa imagen en particular falló, seguimos con las demás
                                }

                                $nombre_extra = time() . "_" . $orden . "_" . basename($_FILES['imagenes_adicionales']['name'][$i]);
                                $ruta_extra = $directorio_subida . $nombre_extra;

                                if (move_uploaded_file($_FILES['imagenes_adicionales']['tmp_name'][$i], $ruta_extra)) {
                                    $stmt_img_extra->bind_param("isi", $producto_id, $ruta_extra, $orden);
                                    $stmt_img_extra->execute();
                                    $orden++;
                                }
                            }
                            $stmt_img_extra->close();
                        }

                        echo "<h2 style='color: #28a745;'>¡Producto subido con éxito!</h2>";
                        echo "<p>El artículo <strong>" . htmlspecialchars($nombre_producto) . "</strong> ya está guardado en tu local.</p>";
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
                header("Location: productos.php?error=sin_local");
                exit();
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
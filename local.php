<?php
ob_start();
session_start();

// Validamos seguridad: Si no hay sesión o no es vendedor, lo mandamos al login
if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'vendedor') {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrar Local - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container">
        
        <h2>Registrar Local</h2>
        <br>
        <p>Completá los datos de tu negocio para poder empezar a subir productos.</p>
<br>
        <form action="guardar_local.php" method="POST">
            
            <b><label for="nombre_local">Nombre de tu local:</label></b>
            <input type="text" id="nombre_local" name="nombre_local" required>

            <b><label for="direccion">Dirección:</label></b>
            <input type="text" id="direccion" name="direccion" required>

            <b><label for="descripcion">Descripción del local:</label></b>
            <textarea id="descripcion" name="descripcion" rows="4" required></textarea>

            <button type="submit">Guardar Local</button>

        </form>

    </div>

</body>
</html>
<?php ob_end_flush(); ?>
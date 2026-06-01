<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    
<div class="container">
    <h1>Crear Cuenta</h1>
    <form action="procesar_registro.php" method="POST">
        
        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'duplicado') {
            echo "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>Ese correo ya está en uso. Por favor, elegí otro.</p>";
        }
        ?>

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>

        <label for="rol">¿Qué tipo de usuario eres?</label>
        <select id="rol" name="rol" required>
            <option value="" disabled selected>Selecciona una opción</option>
            <option value="comprador">Comprador</option>
            <option value="vendedor">Vendedor (Tengo un local)</option>
        </select>

        <button type="submit">Registrarse</button>
    </form>

</div>

</body>
</html>
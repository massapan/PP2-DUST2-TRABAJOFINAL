<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>

<body class="pagina-centrada pagina-auth">
    <div class="fondo"></div>
    <div class="caja-auth">
        <h1>Crear Cuenta</h1>
        <form action="procesar_registro.php" method="POST">

        <?php
        if (isset($_GET['error']) && $_GET['error'] == 'duplicado') {
            echo "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>Ese correo ya está en uso. Por favor, elegí otro.</p>";
        } elseif (isset($_GET['error']) && $_GET['error'] == 'intentos') {
            echo "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>Demasiados intentos. Volvé a registrarte.</p>";
        } elseif (isset($_GET['error']) && $_GET['error'] == 'password_corta') {
            echo "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>La contraseña debe tener al menos 8 caracteres.</p>";
        } elseif (isset($_GET['error']) && $_GET['error'] == 'email_invalido') {
            echo "<p style='color: red; font-weight: bold; margin-bottom: 15px;'>El correo electrónico no tiene un formato válido. Ejemplo: usuario@dominio.com</p>";}

        ?> 

        <label for="email">Correo Electrónico:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required minlength="8">

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
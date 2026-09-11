<!DOCTYPE html>
<html lang="es">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>
<body class="pagina-centrada pagina-auth">
    <div class="fondo"></div>
    <div class="caja-auth">
        <div>
        <h1>Iniciar Sesión</h1>
        </div>
        <?php
        if (isset($_GET['registro']) && $_GET['registro'] == 'ok') {
            echo "<p style='color: green; font-weight: bold;'>¡Cuenta verificada! Ya podés iniciar sesión.</p>";
        }
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'incorrecta') {
                echo "<p style='color: red; font-weight: bold;'>Contraseña incorrecta. Intentá de nuevo.</p>";
            } elseif ($_GET['error'] == 'no_existe') {
                echo "<p style='color: red; font-weight: bold;'>El email no está registrado.</p>";
            }
        }
        ?>

        <form action="procesar_login.php" method="POST">
            
            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" required>

            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>

            <button type="submit">Entrar</button>
            
        </form>

        <div>
        <p>¿Olvidaste tu contraseña?</p>
        </div>
        <div>
            <a href="recuperar.html" class="Boton-secundario">Recuperar contraseña</a>
        </div>

        <div>
        <p>¿No tenés cuenta?</p>
        </div>
        <div>       
        <a href="registro.php" class="Boton-secundario">Registrate</a>
        </div>
        
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Iniciar Sesión - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="fondo"></div>
    <div class="container">
        <div>
        <h1>Iniciar Sesión</h1>
        </div>
        <?php
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

        <div style="margin-top: 10px;">
        <p>¿Olvidaste tu contraseña?</p>
        </div>
        <div>
            <a href="recuperar.html" class="Boton-secundario">Recuperar contraseña</a>
        </div>

        <div style="margin-top: 10px;">
        <p>¿No tenés cuenta?</p>
        </div>
        <div>       
        <a href="registro.php" class="Boton-secundario">Registrate</a>
        </div>
        
    </div>
</body>
</html>
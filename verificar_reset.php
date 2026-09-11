<?php
session_start();
include 'config_2fa.php';

if (!isset($_SESSION['reset_pendiente'])) {
    header("Location: recuperar.html");
    exit();
}

$pendiente = $_SESSION['reset_pendiente'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificá tu correo - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>
<body class="pagina-centrada pagina-auth">
    <div class="fondo"></div>
    <div class="caja-auth">
        <h1>Verificá tu correo</h1>
        <p>Para verificar tu identidad, ingresá el código de 6 dígitos que
           enviamos a <strong><?php echo htmlspecialchars($pendiente['email']); ?></strong>.</p>

        <?php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'incorrecto') {
                echo "<p style='color: red; font-weight: bold;'>Código incorrecto. Intentá de nuevo.</p>";
            } elseif ($_GET['error'] == 'expirado') {
                echo "<p style='color: red; font-weight: bold;'>El código venció. Te generamos uno nuevo.</p>";
            }
        }

        if (MODO_2FA === 'demo') {
            echo "<div style='margin:15px 0; padding:10px; border:2px dashed #888; border-radius:8px;'>";
            echo "<p style='margin:0;'><strong>[MODO DEMO]</strong> Tu código es:</p>";
            echo "<p style='font-size:28px; letter-spacing:6px; margin:5px 0; font-weight:bold;'>"
                 . htmlspecialchars($pendiente['codigo']) . "</p>";
            echo "<p style='margin:0; font-size:12px; color:#555;'>En el modo real, este código llega al email.</p>";
            echo "</div>";
        }
        ?>

        <form action="procesar_verificar_reset.php" method="POST">
            <label for="codigo">Código de verificación:</label>
            <input type="text" id="codigo" name="codigo"
                   inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                   placeholder="000000" required autofocus>

            <button type="submit">Verificar</button>
        </form>

        <a href="reenviar_reset.php" class="Boton-secundario">Reenviar código</a>
        <a href="recuperar.html" class="Boton-secundario">Cancelar</a>
    </div>
</body>
</html>
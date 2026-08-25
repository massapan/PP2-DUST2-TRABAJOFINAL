<?php
session_start();
include 'config_2fa.php';

// Si no hay un login pendiente, no hay nada que verificar: al login.
if (!isset($_SESSION['2fa_pendiente'])) {
    header("Location: login.php");
    exit();
}

$pendiente = $_SESSION['2fa_pendiente'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificación en dos pasos - Ituzaingó a un toque</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="pagina-centrada">
    <div class="fondo"></div>
    <div class="container">
        <h1>Verificación en dos pasos</h1>
        <p>Ingresá el código de 6 dígitos que enviamos a
           <strong><?php echo htmlspecialchars($pendiente['email']); ?></strong>.</p>

        <?php
        // Mensajes de error que vienen de procesar_2fa.php
        if (isset($_GET['error'])) {
            if ($_GET['error'] == 'incorrecto') {
                echo "<p style='color: red; font-weight: bold;'>Código incorrecto. Intentá de nuevo.</p>";
            } elseif ($_GET['error'] == 'expirado') {
                echo "<p style='color: red; font-weight: bold;'>El código venció. Te generamos uno nuevo.</p>";
            }
        }

        // --- MODO DEMO: mostramos el código en pantalla ---
        if (MODO_2FA === 'demo') {
            echo "<div style='margin:15px 0; padding:10px; border:2px dashed #888; border-radius:8px;'>";
            echo "<p style='margin:0;'><strong>[MODO DEMO]</strong> Tu código es:</p>";
            echo "<p style='font-size:28px; letter-spacing:6px; margin:5px 0; font-weight:bold;'>"
                 . htmlspecialchars($pendiente['codigo']) . "</p>";
            echo "<p style='margin:0; font-size:12px; color:#555;'>En el modo real, este código llega al email.</p>";
            echo "</div>";
        }
        ?>

        <form action="procesar_2fa.php" method="POST">
            <label for="codigo">Código de verificación:</label>
            <input type="text" id="codigo" name="codigo"
                   inputmode="numeric" pattern="[0-9]{6}" maxlength="6"
                   placeholder="000000" required autofocus>

            <button type="submit">Verificar</button>
        </form>

        <div style="margin-top: 10px;">
            <a href="reenviar_2fa.php" class="Boton-secundario">Reenviar código</a>
        </div>
        <div style="margin-top: 10px;">
            <a href="login.php" class="Boton-secundario">Cancelar</a>
        </div>
    </div>
</body>
</html>

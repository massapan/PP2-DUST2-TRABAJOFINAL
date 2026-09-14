<?php
ob_start();
session_start();
include 'config_2fa.php';

if (!isset($_SESSION['reset_pendiente'])) {
    header("Location: recuperar.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: verificar_reset.php");
    exit();
}

$pendiente = $_SESSION['reset_pendiente'];
$codigo_ingresado = trim($_POST['codigo'] ?? '');

if (time() > $pendiente['expira']) {
    $nuevo = generar_codigo_2fa();
    $_SESSION['reset_pendiente']['codigo']   = $nuevo;
    $_SESSION['reset_pendiente']['expira']   = time() + DURACION_CODIGO_2FA;
    $_SESSION['reset_pendiente']['intentos'] = 0;
    enviar_codigo_2fa($pendiente['email'], $nuevo);

    header("Location: verificar_reset.php?error=expirado");
    exit();
}

if (hash_equals($pendiente['codigo'], $codigo_ingresado)) {
    // Código correcto: recién ahora autorizamos el cambio de contraseña.
    $_SESSION['reset_autorizado'] = $pendiente['usuario_id'];
    unset($_SESSION['reset_pendiente']);

    header("Location: actualizar_password.php");
    exit();
}

$_SESSION['reset_pendiente']['intentos']++;

if ($_SESSION['reset_pendiente']['intentos'] >= MAX_INTENTOS_2FA) {
    unset($_SESSION['reset_pendiente']);
    header("Location: recuperar.html");
    exit();
}

header("Location: verificar_reset.php?error=incorrecto");
exit();
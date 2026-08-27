<?php
ob_start();
session_start();
include 'config_2fa.php';

// Sin login pendiente no se puede verificar nada.
if (!isset($_SESSION['2fa_pendiente'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: verificar_2fa.php");
    exit();
}

$pendiente = $_SESSION['2fa_pendiente'];
$codigo_ingresado = trim($_POST['codigo'] ?? '');

// 1) ¿Venció el código? -> generamos uno nuevo y volvemos a pedirlo.
if (time() > $pendiente['expira']) {
    $nuevo = generar_codigo_2fa();
    $_SESSION['2fa_pendiente']['codigo']   = $nuevo;
    $_SESSION['2fa_pendiente']['expira']   = time() + DURACION_CODIGO_2FA;
    $_SESSION['2fa_pendiente']['intentos'] = 0;
    enviar_codigo_2fa($pendiente['email'], $nuevo);

    header("Location: verificar_2fa.php?error=expirado");
    exit();
}

// 2) ¿El código coincide? (hash_equals evita fugas de tiempo)
if (hash_equals($pendiente['codigo'], $codigo_ingresado)) {

    // ¡ÉXITO! Recién ahora iniciamos la sesión de verdad.
    $_SESSION['usuario_id'] = $pendiente['usuario_id'];
    $_SESSION['rol']        = $pendiente['rol'];

    // Limpiamos el estado temporal del 2FA.
    unset($_SESSION['2fa_pendiente']);

    // Misma redirección por rol que tenía el login original.
    if ($_SESSION['rol'] === 'comprador') {
        header("Location: index.php");
    } else {
        header("Location: index.php");
    }
    exit();
}

// 3) Código incorrecto -> sumamos un intento fallido.
$_SESSION['2fa_pendiente']['intentos']++;

if ($_SESSION['2fa_pendiente']['intentos'] >= MAX_INTENTOS_2FA) {
    // Demasiados intentos: cancelamos el login por seguridad.
    unset($_SESSION['2fa_pendiente']);
    header("Location: login.php?error=incorrecta");
    exit();
}

header("Location: verificar_2fa.php?error=incorrecto");
exit();

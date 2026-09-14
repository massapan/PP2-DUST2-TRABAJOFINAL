<?php
ob_start();
session_start();
include 'config_2fa.php';

if (!isset($_SESSION['reset_pendiente'])) {
    header("Location: recuperar.html");
    exit();
}

$nuevo = generar_codigo_2fa();
$_SESSION['reset_pendiente']['codigo']   = $nuevo;
$_SESSION['reset_pendiente']['expira']   = time() + DURACION_CODIGO_2FA;
$_SESSION['reset_pendiente']['intentos'] = 0;

enviar_codigo_2fa($_SESSION['reset_pendiente']['email'], $nuevo);

header("Location: verificar_reset.php");
exit();
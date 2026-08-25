<?php
ob_start();
session_start();
include 'config_2fa.php';

// Solo tiene sentido reenviar si hay un login pendiente.
if (!isset($_SESSION['2fa_pendiente'])) {
    header("Location: login.php");
    exit();
}

// Generamos un código nuevo, reiniciamos el tiempo y los intentos.
$nuevo = generar_codigo_2fa();
$_SESSION['2fa_pendiente']['codigo']   = $nuevo;
$_SESSION['2fa_pendiente']['expira']   = time() + DURACION_CODIGO_2FA;
$_SESSION['2fa_pendiente']['intentos'] = 0;

enviar_codigo_2fa($_SESSION['2fa_pendiente']['email'], $nuevo);

header("Location: verificar_2fa.php");
exit();

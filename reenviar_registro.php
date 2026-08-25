<?php
ob_start();
session_start();
include 'config_2fa.php';

// Solo tiene sentido reenviar si hay un registro pendiente.
if (!isset($_SESSION['registro_pendiente'])) {
    header("Location: registro.php");
    exit();
}

// Generamos un código nuevo, reiniciamos el tiempo y los intentos.
$nuevo = generar_codigo_2fa();
$_SESSION['registro_pendiente']['codigo']   = $nuevo;
$_SESSION['registro_pendiente']['expira']   = time() + DURACION_CODIGO_2FA;
$_SESSION['registro_pendiente']['intentos'] = 0;

enviar_codigo_2fa($_SESSION['registro_pendiente']['email'], $nuevo);

header("Location: verificar_registro.php");
exit();

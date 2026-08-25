<?php
ob_start();
session_start();
include 'conexion.php';
include 'config_2fa.php';

// Sin registro pendiente no hay nada que verificar.
if (!isset($_SESSION['registro_pendiente'])) {
    header("Location: registro.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: verificar_registro.php");
    exit();
}

$pendiente = $_SESSION['registro_pendiente'];
$codigo_ingresado = trim($_POST['codigo'] ?? '');

// 1) ¿Venció el código? -> generamos uno nuevo y volvemos a pedirlo.
if (time() > $pendiente['expira']) {
    $nuevo = generar_codigo_2fa();
    $_SESSION['registro_pendiente']['codigo']   = $nuevo;
    $_SESSION['registro_pendiente']['expira']   = time() + DURACION_CODIGO_2FA;
    $_SESSION['registro_pendiente']['intentos'] = 0;
    enviar_codigo_2fa($pendiente['email'], $nuevo);

    header("Location: verificar_registro.php?error=expirado");
    exit();
}

// 2) ¿El código coincide? -> creamos la cuenta de verdad.
if (hash_equals($pendiente['codigo'], $codigo_ingresado)) {

    $sql = "INSERT INTO usuarios (email, password, rol) VALUES (?, ?, ?)";
    $stmt = $conexion->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("sss", $pendiente['email'], $pendiente['password'], $pendiente['rol']);
        try {
            $stmt->execute();

            // Registro terminado: limpiamos el estado temporal.
            unset($_SESSION['registro_pendiente']);

            // A iniciar sesión, con un aviso de éxito.
            header("Location: login.php?registro=ok");
            exit();

        } catch (mysqli_sql_exception $e) {
            // Por si alguien registró ese mismo correo mientras tanto.
            if ($e->getCode() == 1062) {
                unset($_SESSION['registro_pendiente']);
                header("Location: registro.php?error=duplicado");
                exit();
            } else {
                echo "Error crítico: " . $e->getMessage();
            }
        }
        $stmt->close();
    }
    $conexion->close();
    exit();
}

// 3) Código incorrecto -> sumamos un intento fallido.
$_SESSION['registro_pendiente']['intentos']++;

if ($_SESSION['registro_pendiente']['intentos'] >= MAX_INTENTOS_2FA) {
    // Demasiados intentos: cancelamos el registro.
    unset($_SESSION['registro_pendiente']);
    header("Location: registro.php?error=intentos");
    exit();
}

header("Location: verificar_registro.php?error=incorrecto");
exit();

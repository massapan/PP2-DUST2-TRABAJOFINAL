<?php
ob_start();
session_start();
include 'conexion.php';

// A49 (volver): evita un "open redirect" -- solo aceptamos una ruta
// interna relativa (ej: "index.php?pagina=favoritos"), nunca una URL
// absoluta a otro sitio ni una "//otrositio.com" (protocolo-relativa).
function es_ruta_interna_valida($ruta) {
    if ($ruta === '') return false;
    if (preg_match('#^https?://#i', $ruta)) return false;
    if (str_starts_with($ruta, '//')) return false;
    if (str_starts_with($ruta, '\\')) return false;
    return true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $volver = $_POST['volver'] ?? '';

    $sql = "SELECT id, password, rol FROM usuarios WHERE email = ?";
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($password, $usuario['password'])) {
    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['rol']        = $usuario['rol'];

    if ($_SESSION['rol'] === 'vendedor') {

        // Chequeamos si este vendedor ya tiene un local registrado
        $sql_local = "SELECT id FROM locales WHERE usuario_id = ?";
        $stmt_local = $conexion->prepare($sql_local);
        $stmt_local->bind_param("i", $usuario['id']);
        $stmt_local->execute();
        $resultado_local = $stmt_local->get_result();

        if ($resultado_local->num_rows > 0) {
            // Ya tiene local → volvemos adonde estaba (o index.php)
            if (es_ruta_interna_valida($volver)) {
                header("Location: $volver");
            } else {
                header("Location: index.php");
            }
        } else {
            // Todavía no tiene local → SIEMPRE lo mandamos a crearlo,
            // sin importar de dónde venía (no tiene sentido volver a
            // favoritos si todavía no completó su alta de local).
            header("Location: SubidaLocal.php");
        }
        $stmt_local->close();

    } else {
        // Es comprador → volvemos adonde estaba (o index.php)
        if (es_ruta_interna_valida($volver)) {
            header("Location: $volver");
        } else {
            header("Location: index.php");
        }
    }

    exit();
    }
}
}
ob_end_flush();
?>
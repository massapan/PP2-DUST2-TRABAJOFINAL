<?php
// ============================================================
//  Configuración del Doble Factor de Verificación (2FA)
//  Tarea A7 - Login seguro
// ============================================================

// Interruptor de modo:
//   'demo'  -> El código NO se envía por email, se muestra en pantalla.
//              Sirve para presentar la lógica en la demo del examen.
//   'email' -> El código se envía de verdad al correo del usuario.
//              (Este será el método "real" más adelante).
define('MODO_2FA', 'demo');

// Tiempo de validez del código, en segundos (5 minutos).
define('DURACION_CODIGO_2FA', 300);

// Cantidad máxima de intentos antes de cancelar el login.
define('MAX_INTENTOS_2FA', 3);


// ------------------------------------------------------------
//  Genera un código numérico de 6 dígitos (ej: 048273)
// ------------------------------------------------------------
function generar_codigo_2fa() {
    // random_int es criptográficamente seguro (mejor que rand()).
    return str_pad((string) random_int(0, 999999), 6, "0", STR_PAD_LEFT);
}


// ------------------------------------------------------------
//  Envía el código al usuario según el modo configurado.
//  Devuelve true si "salió bien".
// ------------------------------------------------------------
function enviar_codigo_2fa($email, $codigo) {

    if (MODO_2FA === 'demo') {
        // En modo demo no mandamos nada: el código se mostrará en
        // pantalla desde verificar_2fa.php. Igual lo dejamos guardado
        // en un archivo de log por si lo querés revisar.
        $linea = date('Y-m-d H:i:s') . " | $email | $codigo" . PHP_EOL;
        @file_put_contents(__DIR__ . '/codigos_2fa.log', $linea, FILE_APPEND);
        return true;
    }

    // MODO_2FA === 'email' -> envío real.
    $asunto  = "Tu código de verificación - Ituzaingó a un Toque";
    $cuerpo  = "Hola!\n\n";
    $cuerpo .= "Tu código de verificación es: $codigo\n\n";
    $cuerpo .= "El código vence en " . (DURACION_CODIGO_2FA / 60) . " minutos.\n";
    $cuerpo .= "Si no intentaste iniciar sesión, ignorá este mensaje.\n";
    $cabeceras  = "From: no-responder@ituzaingoauntoque.com\r\n";
    $cabeceras .= "Content-Type: text/plain; charset=utf-8\r\n";

    // Nota: mail() requiere un servidor SMTP configurado en php.ini.
    // Más adelante se puede reemplazar esta línea por PHPMailer + Gmail.
    return mail($email, $asunto, $cuerpo, $cabeceras);
}

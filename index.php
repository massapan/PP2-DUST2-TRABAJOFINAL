<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Whitelist de fragmentos válidos. Nunca hacemos include() directo de lo
// que venga por GET: si alguien manipulara ?pagina=... a mano, esto evita
// que pueda hacer un include arbitrario de otro archivo del servidor.
$paginas_validas = [
    'catalogo'  => 'catalogo.php',
    'mapa'      => 'mapa.php',
    'favoritos' => 'favoritos.php',
    'local'     => 'local.php',
    'prenda'    => 'prenda.php',
];

$pagina  = $_GET['pagina'] ?? 'catalogo';
$archivo = $paginas_validas[$pagina] ?? 'catalogo.php';

// navegacion.js manda este header en sus fetch(). Si está presente,
// devolvemos SOLO el fragmento (sin <html>, sin header, sin footer) para
// insertarlo directo en #contenido. Si NO está (alguien entró por la URL
// de verdad, F5, etc.), servimos la página completa más abajo.
$esPeticionAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($esPeticionAjax) {
    include $archivo;
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ituzaingó a un Toque</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="icon" href="img/logo-removebg-preview.png" type="image/png">
</head>
<body>

    <?php include 'header.php'; ?>

    <div id="contenido">
        <?php include $archivo; ?>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/navegacion.js"></script>
    <script src="js/filtros.js"></script>
    <script src="js/favoritos.js"></script>

</body>
</html>
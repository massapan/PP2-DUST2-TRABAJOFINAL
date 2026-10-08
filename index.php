<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Whitelist de fragmentos válidos. Nunca hacemos include() directo de lo
// que venga por GET: si alguien manipulara ?pagina=... a mano, esto evita
// que pueda hacer un include arbitrario de otro archivo del servidor.
$paginas_validas = [
    'catalogo'       => 'catalogo.php',
    'mapa'           => 'mapa.php',
    'favoritos'      => 'favoritos.php',
    'local'          => 'local.php',
    'prenda'         => 'prenda.php',
    'busqueda'       => 'busqueda.php',
    'subir-local'    => 'SubidaLocal.php',
    'subir-producto' => 'productos.php',
    'mis-productos'  => 'mis_productos.php',
];

$pagina  = $_GET['pagina'] ?? 'catalogo';

// 'mi-local' no es un archivo fijo: antes vivía en mi_local.php haciendo un
// redirect server-side, pero eso no funciona bien llamado por fetch(). Acá
// resolvemos la misma decisión ("¿ya tiene local? mostráselo. si no, que lo
// cree") directamente como parte del router.
if ($pagina === 'mi-local') {
    if (isset($_SESSION['usuario_id']) && ($_SESSION['rol'] ?? null) === 'vendedor') {
        include 'conexion.php';
        $sql_mi_local = "SELECT id FROM locales WHERE usuario_id = ?";
        $stmt_mi_local = $conexion->prepare($sql_mi_local);
        $stmt_mi_local->bind_param("i", $_SESSION['usuario_id']);
        $stmt_mi_local->execute();
        $mi_local = $stmt_mi_local->get_result()->fetch_assoc();
        $stmt_mi_local->close();
        $conexion->close();

        if ($mi_local) {
            $_GET['id'] = $mi_local['id'];
            $archivo = 'local.php';
        } else {
            $archivo = 'SubidaLocal.php';
        }
    } else {
        $archivo = 'catalogo.php';
    }
} else {
    $archivo = $paginas_validas[$pagina] ?? 'catalogo.php';
}

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
    <script src="JS/navegacion.js"></script>
<script src="JS/filtros.js"></script>
<script src="JS/favoritos.js"></script>
<script src="JS/galeria.js"></script>
<script src="JS/preview_imagen.js"></script>
<script src="JS/formularios_ajax.js"></script>
</body>
</html>
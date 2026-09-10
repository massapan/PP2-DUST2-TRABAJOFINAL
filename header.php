<?php
// Si la sesión no está iniciada todavía, la iniciamos acá.
// (usamos session_status() para no pisar una sesión que ya haya
// arrancado otro archivo incluido después, como catalogo.php)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logueado = isset($_SESSION['usuario_id']);
?>

<header class="d-flex flex-wrap justify-content-between align-items-center py-3 border-bottom Header">
    <a href="/" class="d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <span class="fs-4">Ituzaingó a un toque</span>
    </a>
    <ul class="nav nav-pills">
        <li class="nav-item"><a href="mapa.php" class="nav-link">Mapa</a></li>
        <li class="nav-item"><a href="catalogo.php" class="nav-link" aria-current="page">Catálogo</a></li>
        <li class="nav-item"><a href="favoritos.php" class="nav-link">Favoritos</a></li>
    </ul>

    <form class="d-flex ms-md-4 buscador-wrapper" role="search">
        <input type="search" class="form-control buscador-input" placeholder="Buscar locales o prendas...">
    </form>

    <?php if ($logueado): ?>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center justify-content-center rounded-circle text-white avatar-circulo" style="width: 40px; height: 40px;" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar sesión</a></li>
            </ul>
        </div>
    <?php else: ?>
        <a href="iniciar.html" class="btn btn-success">Iniciar sesión / Registrarse</a>
    <?php endif; ?>
</header>
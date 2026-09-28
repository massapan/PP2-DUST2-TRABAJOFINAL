<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logueado = isset($_SESSION['usuario_id']);
$rol = $_SESSION['rol'] ?? null; // 'comprador' | 'vendedor' | null
?>

<header class="d-flex flex-wrap justify-content-between align-items-center py-3 border-bottom Header">
    <a class="d-flex align-items-center mb-3 mb-md-0 link-body-emphasis text-decoration-none">
        <span class="fs-4" style="margin-left: 20px;">Ituzaingó a un toque</span>
    </a>
    <ul class="nav nav-pills">
        <li class="nav-item"><a href="index.php?pagina=mapa" class="nav-link">Mapa</a></li>
        <li class="nav-item"><a href="index.php?pagina=catalogo" class="nav-link" aria-current="page">Catálogo</a></li>
        <li class="nav-item"><a href="index.php?pagina=favoritos" class="nav-link">Favoritos</a></li>
    </ul>

    <form class="d-flex ms-md-4 buscador-wrapper col-md-3" role="search">
        <input type="search" class="form-control buscador-input" placeholder="Buscar locales o prendas...">
    </form>

    <?php if ($logueado): ?>
        <div class="dropdown d-flex align-items-center" style="gap: 10px; margin-right: 20px;">

            <a href="#" class="d-flex align-items-center justify-content-center rounded-circle text-white avatar-circulo <?php echo $rol === 'vendedor' ? 'avatar-vendedor' : ''; ?>" style="width: 40px; height: 40px;" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i>
            </a>
            <ul class="dropdown-menu dropdown-menu-end">
                <?php if ($rol === 'vendedor'): ?>
                    <li><a class="dropdown-item" href="mi_local.php">Mi Local</a></li>
                    <li><a class="dropdown-item" href="mis_productos.php">Mis Productos</a></li>
                    <li><a class="dropdown-item" href="productos.php">Subir Producto</a></li>
                    <li><hr class="dropdown-divider"></li>
                <?php endif; ?>
                <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar sesión</a></li>
            </ul>
        </div>
    <?php else: ?>
        <a href="iniciar.html" class="btn btn-success" style=" margin-right: 20px;">Iniciar sesión / Registrarse</a>
    <?php endif; ?>
</header>

<!-- A49: Modal de aviso al intentar marcar favoritos sin sesión iniciada.
     Vive acá (fuera de #contenido) porque header.php no se recarga cuando
     navegacion.js reemplaza el contenido, así que favoritos.js siempre
     lo va a encontrar en el DOM sin importar en qué página esté. -->
     
<div class="modal fade" id="modalLoginFavoritos" tabindex="-1" aria-labelledby="modalLoginFavoritosLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalLoginFavoritosLabel">Eepa! a donde vas?xdxd</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Para marcar productos o locales como favoritos necesitás tener una cuenta. Podés iniciar sesión o registrarte en un momento.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Ahora no</button>
                <a href="iniciar.html" id="btnModalIrALogin" class="btn btn-success">Iniciar sesión / Registrarme</a>
            </div>
        </div>
    </div>
</div>
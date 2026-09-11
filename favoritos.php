<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$logueado = isset($_SESSION['usuario_id']);
?>

<div class="fondo"></div>
<div class="container-fluid">
    <div class="row">
         <aside class="col-md-2 border-end bg-white">
            <?php include 'sidebar_prendas_locales.php'; ?>
        </aside>
        <div class="col-md-9 my-3">
            <div class="contenedor-catalogo">
                <h2>Favoritos</h2>
                <?php if ($logueado): ?>
                    <p>Acá va el contenido de favoritos (en construcción).</p>
                <?php else: ?>
                    <p>Para ver tus favoritos necesitás iniciar sesión.</p>
                    <a href="iniciar.html" class="Boton-secundario" style="width: 20%;">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
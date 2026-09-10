<div class="fondo"></div>
<div class="container-fluid my-3">
    <div class="row">
        <aside class="col-md-3 border-end bg-white">
            <?php include 'sidebar_prendas_locales.php'; ?>
        </aside>
        <div class="col-md-9">
            <div class="contenedor-catalogo">
                <h2>Favoritos</h2>
                <p>Para ver tus prendas favoritas, necesitas estar logueado.</p>
                <?php if (!isset($_SESSION['usuario'])): ?>
                    <a href="iniciar.html" class="btn btn-primary">Iniciar sesión</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
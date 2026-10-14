<div class="fondo"></div>
<div class="container-fluid">
    <div class="row">
        <aside class="col-md-3 border-end bg-white">
            <?php include 'sidebar_mapa.php'; ?>
        </aside>
        <div class="col-md-9  my-3">
            <div class="contenedor-catalogo">
                <h2>Mapa</h2>
                <p>Tocá un local para ver un adelanto de su catálogo.</p>

                <!-- Ojo: aca NO va ningun <script>. navegacion.js inserta este
                     fragmento con innerHTML, y el navegador no ejecuta los
                     scripts insertados de esa forma. El mapa lo levanta
                     JS/mapa.js, que se carga una sola vez desde index.php y
                     detecta cuando aparece este contenedor. -->
                <div class="mapa-zona">
                    <div id="mapa" class="mapa-lienzo">
                        <!-- Mensaje de respaldo: queda visible si el mapa no
                             llega a iniciarse (sin internet, CDN caido).
                             mapa.js lo saca apenas dibuja. -->
                        <p class="mapa-aviso">Cargando el mapa…</p>
                    </div>

                    <!-- Panel del local elegido. Arranca oculto y lo completa
                         mapa.js al tocar un marcador. -->
                    <aside id="panelLocal" class="mapa-panel" hidden>
                        <button type="button" class="mapa-panel-cerrar"
                                aria-label="Cerrar panel">&times;</button>
                        <div id="panelLocalContenido"></div>
                    </aside>
                </div>

            </div>
        </div>
    </div>
</div>

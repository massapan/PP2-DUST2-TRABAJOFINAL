/* ============================================================
   Selector de ubicacion del local (OpenLayers)
   ============================================================
   Mapita del formulario de alta de local: el vendedor toca donde
   esta su negocio y se completan los campos ocultos latitud y
   longitud. No se le pide escribir coordenadas a mano porque nadie
   sabe las suyas de memoria.

   Igual que JS/mapa.js, vive aca y no dentro de SubidaLocal.php:
   navegacion.js inserta los fragmentos con innerHTML y el navegador
   no ejecuta los <script> insertados asi.

   Lo que se guarda termina de validarlo guardar_local.php: esto es
   solo la forma comoda de elegir el punto.
   ============================================================ */

(function () {
    'use strict';

    var CENTRO_ITUZAINGO = [-58.6726, -34.6585];
    var ZOOM_SIN_UBICACION = 14;
    // Al editar un local que ya tiene ubicacion se abre mas cerca, para
    // que se vea la cuadra y no haya que acercarse a mano.
    var ZOOM_CON_UBICACION = 17;

    // Mismo pin que usa el mapa de locales, para que se reconozca igual.
    var PIN_SVG =
        '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="44" viewBox="0 0 32 44">' +
        '<path d="M16 1C8.3 1 2 7.3 2 15c0 10 14 28 14 28s14-18 14-28c0-7.7-6.3-14-14-14z"' +
        ' fill="#00953c" stroke="#fbfbfb" stroke-width="2"/>' +
        '<circle cx="16" cy="15" r="5.5" fill="#fbfbfb"/>' +
        '</svg>';

    function estiloPin() {
        return new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
                src: 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(PIN_SVG)
            })
        });
    }

    function iniciarSelector(contenedor) {
        var campoLat = document.getElementById('latitud');
        var campoLon = document.getElementById('longitud');
        var estado = document.getElementById('selectorMapaEstado');
        var botonBorrar = document.getElementById('selectorMapaBorrar');

        if (!campoLat || !campoLon) {
            return;
        }

        if (typeof ol === 'undefined') {
            // Sin el mapa igual se puede guardar el local: la ubicacion es
            // opcional. Se avisa en vez de dejar un recuadro mudo.
            contenedor.innerHTML =
                '<p class="mapa-aviso">No se pudo cargar el mapa. ' +
                'Pod&eacute;s guardar el local igual y marcar la ubicaci&oacute;n m&aacute;s tarde.</p>';
            return;
        }

        contenedor.innerHTML = '';

        var fuente = new ol.source.Vector();
        var marcador = null;

        var mapa = new ol.Map({
            target: contenedor,
            layers: [
                new ol.layer.Tile({ source: new ol.source.OSM() }),
                new ol.layer.Vector({ source: fuente })
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat(CENTRO_ITUZAINGO),
                zoom: ZOOM_SIN_UBICACION
            })
        });

        function actualizarTexto() {
            var hay = campoLat.value !== '' && campoLon.value !== '';
            if (estado) {
                estado.textContent = hay
                    ? 'Ubicación marcada'
                    : 'Sin ubicación: no vas a aparecer en el mapa';
            }
            if (botonBorrar) {
                botonBorrar.hidden = !hay;
            }
        }

        function ponerMarcador(lon, lat) {
            if (marcador) {
                fuente.removeFeature(marcador);
            }
            marcador = new ol.Feature({
                geometry: new ol.geom.Point(ol.proj.fromLonLat([lon, lat]))
            });
            marcador.setStyle(estiloPin());
            fuente.addFeature(marcador);

            // toFixed(7) para no mandar mas decimales de los que entran en
            // el DECIMAL(10,7) de la columna.
            campoLat.value = lat.toFixed(7);
            campoLon.value = lon.toFixed(7);
            actualizarTexto();
        }

        function quitarMarcador() {
            if (marcador) {
                fuente.removeFeature(marcador);
                marcador = null;
            }
            campoLat.value = '';
            campoLon.value = '';
            actualizarTexto();
        }

        // Si el local ya tenia ubicacion guardada, se muestra y se centra ahi.
        var latGuardada = parseFloat(campoLat.value);
        var lonGuardada = parseFloat(campoLon.value);
        if (!isNaN(latGuardada) && !isNaN(lonGuardada)) {
            ponerMarcador(lonGuardada, latGuardada);
            mapa.getView().setCenter(ol.proj.fromLonLat([lonGuardada, latGuardada]));
            mapa.getView().setZoom(ZOOM_CON_UBICACION);
        }
        actualizarTexto();

        mapa.on('singleclick', function (evento) {
            var coord = ol.proj.toLonLat(evento.coordinate);
            ponerMarcador(coord[0], coord[1]);
        });

        mapa.on('pointermove', function (evento) {
            if (evento.dragging) {
                return;
            }
            mapa.getTargetElement().style.cursor = 'crosshair';
        });

        if (botonBorrar) {
            botonBorrar.addEventListener('click', quitarMarcador);
        }
    }

    function revisarSelector() {
        var contenedor = document.getElementById('selectorMapa');
        if (!contenedor || contenedor.dataset.iniciado === '1') {
            return;
        }
        contenedor.dataset.iniciado = '1';
        iniciarSelector(contenedor);
    }

    document.addEventListener('DOMContentLoaded', function () {
        revisarSelector();

        var zona = document.getElementById('contenido');
        if (zona) {
            new MutationObserver(revisarSelector).observe(zona, { childList: true });
        }
    });
})();

/* ============================================================
   Mapa de locales con OpenLayers
   ============================================================
   Dibuja un marcador por cada local que tenga coordenadas y, al
   tocarlo, abre un panel con un adelanto de su catalogo.

   Por que vive aca y no dentro de mapa.php:
   navegacion.js inserta los fragmentos con innerHTML, y el navegador
   NO ejecuta los <script> insertados de esa forma. Entonces este
   archivo se carga una sola vez desde index.php y se queda mirando
   el contenedor #contenido para arrancar cuando aparece el mapa,
   tanto si se entra por URL directa como si se llega navegando.

   Los datos salen de mapa_locales.php.
   ============================================================ */

(function () {
    'use strict';

    // Centro de Ituzaingo. Solo se usa si no hay ningun local ubicable;
    // si los hay, la vista se ajusta sola para que entren todos.
    var CENTRO_ITUZAINGO = [-58.6726, -34.6585];
    var ZOOM_INICIAL = 14;
    // Hasta donde puede acercarse el ajuste automatico. Sin este tope, un
    // unico local dejaria el mapa pegadisimo al piso.
    var ZOOM_MAXIMO_AJUSTE = 16;

    // Icon Symbolizer: el marcador es un SVG inline convertido a data URI.
    // Va embebido y no como archivo aparte para que no dependa de una
    // request mas ni de que exista el .png en el servidor.
    var PIN_SVG =
        '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="44" viewBox="0 0 32 44">' +
        '<path d="M16 1C8.3 1 2 7.3 2 15c0 10 14 28 14 28s14-18 14-28c0-7.7-6.3-14-14-14z"' +
        ' fill="#00953c" stroke="#fbfbfb" stroke-width="2"/>' +
        '<circle cx="16" cy="15" r="5.5" fill="#fbfbfb"/>' +
        '</svg>';

    // Mismo pin en azul para el local que esta abierto en el panel.
    var PIN_SVG_ACTIVO = PIN_SVG.replace('fill="#00953c"', 'fill="#2d77c7"');

    function aDataUri(svg) {
        return 'data:image/svg+xml;charset=utf-8,' + encodeURIComponent(svg);
    }

    // Se crean una sola vez y se reusan en todos los marcadores: OpenLayers
    // recomienda compartir los estilos en vez de instanciar uno por feature.
    var estiloPin = null;
    var estiloPinActivo = null;

    function construirEstilos() {
        estiloPin = new ol.style.Style({
            image: new ol.style.Icon({
                // anchor abajo-centro: la punta del pin marca el punto exacto
                anchor: [0.5, 1],
                src: aDataUri(PIN_SVG)
            })
        });
        estiloPinActivo = new ol.style.Style({
            image: new ol.style.Icon({
                anchor: [0.5, 1],
                src: aDataUri(PIN_SVG_ACTIVO),
                scale: 1.15
            })
        });
    }

    function formatearPrecio(valor) {
        return '$' + Number(valor).toLocaleString('es-AR', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    // Todo lo que viene de la base se inserta como texto, nunca como HTML.
    function texto(valor) {
        var d = document.createElement('div');
        d.textContent = valor == null ? '' : String(valor);
        return d.innerHTML;
    }

    function armarPanel(local) {
        var html = '<h3 class="mapa-panel-titulo">' + texto(local.nombre) + '</h3>';

        if (local.direccion) {
            html += '<p class="mapa-panel-dato">' + texto(local.direccion) + '</p>';
        }
        if (local.entre_calles) {
            html += '<p class="mapa-panel-dato">Entre ' + texto(local.entre_calles) + '</p>';
        }
        if (local.horario) {
            html += '<p class="mapa-panel-dato">' + texto(local.horario) + '</p>';
        }

        if (local.productos.length === 0) {
            html += '<p class="mapa-panel-vacio">Este local todav&iacute;a no public&oacute; productos.</p>';
        } else {
            html += '<div class="mapa-panel-productos">';
            local.productos.forEach(function (p) {
                html += '<a class="mapa-panel-producto enlace-interno"' +
                        ' href="index.php?pagina=prenda&id=' + encodeURIComponent(p.id) + '">';
                if (p.imagen) {
                    html += '<img src="' + texto(p.imagen) + '" alt="' + texto(p.nombre) + '">';
                } else {
                    html += '<span class="mapa-panel-sinfoto"></span>';
                }
                html += '<span class="mapa-panel-nombre">' + texto(p.nombre) + '</span>' +
                        '<span class="mapa-panel-precio">' + formatearPrecio(p.precio) + '</span>' +
                        '</a>';
            });
            html += '</div>';

            var restantes = local.total_productos - local.productos.length;
            if (restantes > 0) {
                html += '<p class="mapa-panel-restantes">y ' + restantes +
                        (restantes === 1 ? ' producto m&aacute;s' : ' productos m&aacute;s') + '</p>';
            }
        }

        // enlace-interno hace que navegacion.js lo abra dentro de la SPA.
        html += '<a class="Boton-secundario enlace-interno mapa-panel-ir"' +
                ' href="index.php?pagina=local&id=' + encodeURIComponent(local.id) + '">' +
                'Ver el local</a>';

        return html;
    }

    function iniciarMapa(contenedor) {
        // Si la libreria no llego (sin internet, CDN caido), se avisa en vez
        // de dejar un recuadro vacio y un error mudo en la consola.
        if (typeof ol === 'undefined') {
            contenedor.innerHTML =
                '<p class="mapa-aviso">No se pudo cargar el mapa. ' +
                'Revis&aacute; tu conexi&oacute;n a internet y volv&eacute; a intentar.</p>';
            return;
        }

        // El aviso de "Cargando" ya cumplio: ol necesita el div limpio.
        contenedor.innerHTML = '';

        construirEstilos();

        var capaLocales = new ol.layer.Vector({
            source: new ol.source.Vector()
        });

        var mapa = new ol.Map({
            target: contenedor,
            layers: [
                new ol.layer.Tile({ source: new ol.source.OSM() }),
                capaLocales
            ],
            view: new ol.View({
                center: ol.proj.fromLonLat(CENTRO_ITUZAINGO),
                zoom: ZOOM_INICIAL
            })
        });

        var panel = document.getElementById('panelLocal');
        var panelContenido = document.getElementById('panelLocalContenido');
        var marcadorActivo = null;

        function cerrarPanel() {
            if (marcadorActivo) {
                marcadorActivo.setStyle(estiloPin);
                marcadorActivo = null;
            }
            if (panel) {
                panel.hidden = true;
            }
        }

        function abrirPanel(marcador) {
            if (marcadorActivo && marcadorActivo !== marcador) {
                marcadorActivo.setStyle(estiloPin);
            }
            marcadorActivo = marcador;
            marcador.setStyle(estiloPinActivo);

            if (panel && panelContenido) {
                panelContenido.innerHTML = armarPanel(marcador.get('local'));
                panel.hidden = false;
                panel.scrollTop = 0;
            }
        }

        if (panel) {
            var botonCerrar = panel.querySelector('.mapa-panel-cerrar');
            if (botonCerrar) {
                botonCerrar.addEventListener('click', cerrarPanel);
            }
        }

        // Un clic sobre un marcador abre su panel; sobre el mapa vacio, cierra.
        mapa.on('singleclick', function (evento) {
            var marcador = mapa.forEachFeatureAtPixel(evento.pixel, function (f) {
                return f;
            });
            if (marcador && marcador.get('local')) {
                abrirPanel(marcador);
            } else {
                cerrarPanel();
            }
        });

        // Manito del cursor cuando se pasa por encima de un marcador.
        mapa.on('pointermove', function (evento) {
            if (evento.dragging) {
                return;
            }
            var hay = mapa.hasFeatureAtPixel(evento.pixel);
            mapa.getTargetElement().style.cursor = hay ? 'pointer' : '';
        });

        fetch('mapa_locales.php', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
            .then(function (r) { return r.json(); })
            .then(function (datos) {
                if (!datos.ok || !datos.locales.length) {
                    return;
                }

                var fuente = capaLocales.getSource();

                datos.locales.forEach(function (local) {
                    var marcador = new ol.Feature({
                        geometry: new ol.geom.Point(
                            ol.proj.fromLonLat([local.lon, local.lat])
                        )
                    });
                    marcador.set('local', local);
                    marcador.setStyle(estiloPin);
                    fuente.addFeature(marcador);
                });

                // Encuadra la vista para que entren todos los locales.
                mapa.getView().fit(fuente.getExtent(), {
                    padding: [60, 60, 60, 60],
                    maxZoom: ZOOM_MAXIMO_AJUSTE
                });
            })
            .catch(function (error) {
                console.error('No se pudieron cargar los locales del mapa:', error);
            });
    }

    // Arranca el mapa si el contenedor esta presente y todavia no se inicio.
    // El flag en dataset evita montar dos mapas sobre el mismo div cuando se
    // navega varias veces a la pestana.
    function revisarMapa() {
        var contenedor = document.getElementById('mapa');
        if (!contenedor || contenedor.dataset.iniciado === '1') {
            return;
        }
        contenedor.dataset.iniciado = '1';
        iniciarMapa(contenedor);
    }

    document.addEventListener('DOMContentLoaded', function () {
        // Caso 1: se entro directo por index.php?pagina=mapa.
        revisarMapa();

        // Caso 2: se llego navegando por la SPA. En vez de tocar
        // navegacion.js (archivo compartido), se observa el contenedor:
        // cuando su contenido cambia, se revisa si aparecio el mapa.
        var zona = document.getElementById('contenido');
        if (zona) {
            new MutationObserver(revisarMapa).observe(zona, { childList: true });
        }
    });
})();

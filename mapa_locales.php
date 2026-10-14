<?php
// Endpoint AJAX: devuelve los locales que tienen coordenadas cargadas,
// junto con una muestra de su catalogo, para dibujarlos en el mapa.
// Se llama desde JS/mapa.js (fetch), nunca se navega directo aca.

header('Content-Type: application/json; charset=utf-8');

include 'conexion.php';

// Cuantos productos se muestran en el panel al tocar un local. No se traen
// todos: el panel es una vista rapida y traer el catalogo entero de cada
// local multiplicaria el tamano de la respuesta sin que se llegue a ver.
const PRODUCTOS_POR_LOCAL = 4;

// Solo los locales ubicables. Un local sin latitud/longitud no se puede
// dibujar, asi que directamente no viaja al navegador.
$sql_locales = "SELECT id, nombre_local, direccion, entre_calles,
                       horario_texto, imagen_portada, latitud, longitud
                FROM locales
                WHERE latitud IS NOT NULL AND longitud IS NOT NULL
                ORDER BY nombre_local ASC";

$resultado = $conexion->query($sql_locales);

if (!$resultado) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'error' => 'consulta_fallida']);
    exit();
}

$locales = [];
$ids = [];

while ($fila = $resultado->fetch_assoc()) {
    $id = (int) $fila['id'];
    $ids[] = $id;

    $locales[$id] = [
        'id'           => $id,
        'nombre'       => $fila['nombre_local'],
        'direccion'    => $fila['direccion'],
        'entre_calles' => $fila['entre_calles'],
        'horario'      => $fila['horario_texto'],
        'portada'      => $fila['imagen_portada'],
        // El mapa necesita numeros, no los strings que devuelve MySQL
        // para un DECIMAL: ol espera [lon, lat] numericos.
        'lat'          => (float) $fila['latitud'],
        'lon'          => (float) $fila['longitud'],
        'productos'    => [],
        'total_productos' => 0,
    ];
}

// Si no hay locales ubicables se corta aca: sin esto, el IN (...) de abajo
// quedaria vacio y romperia la consulta.
if (!$ids) {
    echo json_encode(['ok' => true, 'locales' => []]);
    exit();
}

// Una sola consulta para los productos de todos los locales, en vez de una
// por local dentro del while de arriba (el clasico problema N+1).
$marcadores = implode(',', array_fill(0, count($ids), '?'));
$tipos = str_repeat('i', count($ids));

$sql_productos = "SELECT p.id, p.local_id, p.nombre_producto, p.precio,
                         (SELECT ip.ruta FROM imagenes_producto ip
                          WHERE ip.producto_id = p.id
                          ORDER BY ip.orden ASC LIMIT 1) AS imagen_ruta
                  FROM productos p
                  WHERE p.local_id IN ($marcadores)
                  ORDER BY p.local_id ASC, p.creado_en DESC";

$stmt = $conexion->prepare($sql_productos);
$stmt->bind_param($tipos, ...$ids);
$stmt->execute();
$res_productos = $stmt->get_result();

while ($producto = $res_productos->fetch_assoc()) {
    $local_id = (int) $producto['local_id'];
    if (!isset($locales[$local_id])) {
        continue;
    }

    // El total cuenta todos, pero solo se mandan los primeros: asi el panel
    // puede decir "y 6 mas" sin tener que traerlos.
    $locales[$local_id]['total_productos']++;

    if (count($locales[$local_id]['productos']) < PRODUCTOS_POR_LOCAL) {
        $locales[$local_id]['productos'][] = [
            'id'     => (int) $producto['id'],
            'nombre' => $producto['nombre_producto'],
            'precio' => (float) $producto['precio'],
            'imagen' => $producto['imagen_ruta'],
        ];
    }
}

$stmt->close();
$conexion->close();

// array_values para que viaje como lista JSON y no como objeto indexado
// por id, que es lo que haria json_encode con las claves numericas.
echo json_encode([
    'ok'      => true,
    'locales' => array_values($locales),
]);

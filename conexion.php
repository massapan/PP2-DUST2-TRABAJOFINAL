<?php
// Parámetros de la base de datos
$servidor = "localhost";
$usuario = "root";
$password = ""; 
$base_datos = "ituzaingo_a_un_toque"; 

//Crear la conexión usando mysqli
$conexion = new mysqli($servidor, $usuario, $password, $base_datos);

//Verificar si hubo algún error en la conexión
if ($conexion->connect_error) {
    die("Error crítico de conexión: " . $conexion->connect_error);
}

//Configurar la codificación de caracteres (para no tener problemas con ñ y tildes)
$conexion->set_charset("utf8mb4");


// echo "¡Conexión exitosa con mysqli!";
?>
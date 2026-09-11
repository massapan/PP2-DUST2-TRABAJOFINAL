<?php
session_start();

// Vaciamos todas las variables de sesión
$_SESSION = [];

// Destruimos la sesión del todo
session_destroy();

// Volvemos a la pantalla de inicio
header("Location: iniciar.html");
exit();
<?php
$serverName = "localhost:8080";
$connectionOptions = array(
    "Database" => "DELIVERY2",
    "Uid" => "sa",
    "PWD" => ""
);

// Establecer conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}

// Configuración de zona horaria
date_default_timezone_set('America/Caracas');
?>

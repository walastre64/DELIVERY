<?php
$serverName = "192.168.23.7";
$connectionOptions = array(
    "Database" => "DELIVERY2",
    "Uid" => "sa",
    "PWD" => "Ec14312183.-"
);

// Establecer conexión
$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    die("Error de conexión: " . print_r(sqlsrv_errors(), true));
}

// Configuración de zona horaria
date_default_timezone_set('America/Caracas');
?>
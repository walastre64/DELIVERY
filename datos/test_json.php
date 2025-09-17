<?php
header('Content-Type: application/json');
function conectate2(){

		$serverName = "192.168.23.7"; //serverName\instanceName
		$connectionInfo = array( "Database"		=>"DELIVERY2",
								 "UID"	   		=>"sa",
								 "PWD"	   		=>"Ec14312183.-",
								 "CharacterSet" =>"UTF-8"
								 );
		$conn = sqlsrv_connect( $serverName, $connectionInfo);
	
		if( $conn  == false) {
			 echo "Conexión no se pudo establecer.<br />";
			 die( print_r( sqlsrv_errors(), true));
		}else{
			return $conn;
		}
}

$conn = conectate2();

// Obtener maestro por ID
function getMaestroById2() {
    global $conn;

    $tabla = 'vendedor';
    $id = 1;
	$datos = "";    

// verifico que el usuario exista con esa cedula

   switch($tabla) {
        case 'vendedor':
        case 'motorizado':
            $sql = "SELECT id, nombre, activo FROM $tabla";
            break;
        case 'turno':
            $sql = "SELECT id, nombre, descripcion FROM $tabla";
            break;
        case 'destino':
            $sql = "SELECT id, nombre FROM $tabla";
            break;
        default:
            return json_encode(array());
    }
    
    $sql .= " ORDER BY nombre";

	$stmt = sqlsrv_query($conn, $sql);
	
	
	
	if ($stmt === false) {
		die(print_r(sqlsrv_errors(), true)); // Verifica si hay errores en la consulta
	}
	
	
	$datos = array();
	

	while ($rowbus = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
		$datos[] =($rowbus); // Muestra la estructura del array
		//$datos[] = $rowbus; 
	}
	
	return json_encode($datos);
}



getMaestroById2();

?>
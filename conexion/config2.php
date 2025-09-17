<?PHP
function conectate2(){

		$serverName = "localhost:8080"; //serverName\instanceName
		$connectionInfo = array( "Database"		=>"DELIVERY2",
								 "UID"	   		=>"sa",
								 "PWD"	   		=>"",
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

?>

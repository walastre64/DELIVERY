<?PHP
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

?>

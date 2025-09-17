<?PHP
function conectate(){

		$serverName = "WILMARALASTRE\MSSQLSERVER2014"; //serverName\instanceName		
		$connectionInfo = array( "Database"		=>"DELIVERY2",
								 "UID"	   		=>"sa",
								 "PWD"	   		=>"",
								 "CharacterSet" =>"UTF-8",
								 "TrustServerCertificate" => "yes"
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

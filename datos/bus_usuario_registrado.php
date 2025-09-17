<?PHP
header("Content-Type: text/html;charset=utf-8");
setlocale(LC_TIME, 'es_VE'); # Localiza en espa�ol es_Venezuela
date_default_timezone_set('America/Caracas');
include("../conexion/conexion.php");
include("../validacion/validacion_general2.php");
//include('../restringir/restringir.ini.php');
// ------------------------------------------------->
//session_valida();
$conn = conectate();
session_start();


$txt_usuario	= sanear_string($_POST['txt_usuario']);	
$txt_password	= ($_POST['txt_password']);

$valor_consulta = 0;

// verifico que el usuario exista con esa cedula
$qry = "SET ANSI_NULLS ON SET ANSI_WARNINGS ON EXEC SP_BUS_USUARIO_WEB '".$txt_usuario."','".$txt_password."'";
//echo $qry;
$rst = sqlsrv_query( $conn, $qry, array(), array("Scrollable"=>"buffered"));
if (! $rst) {  
   echo "Error en la ejecuci�n de la instrucci�n ".$qry.".\n";
   die( print_r2( sqlsrv_errors(), true));  
   exit;
}else{
	
	$rowCount = sqlsrv_num_rows( $rst );	

	if($rowCount > 0)
	{		
		while( $rowusu = sqlsrv_fetch_array($rst)) 		
		{		
				
				// esto es si le haga hash
				if ($rowusu[3] == ($txt_password) ) //md5($txt_password)
				{
					
					$valor_consulta  = "1"."/";					

				}else{
						$valor_consulta  = "2"."/";
						$valor_consulta .= $txt_usuario;
				}

			
		} // fin while
	
	}else{
		
			// usuario no existente
			$valor_consulta  = "3"."/";
			$valor_consulta .= $txt_usuario;

	} //fin if rowCount	
	
} // fin if rs

sqlsrv_free_stmt( $rst );  
sqlsrv_close( $conn );  

echo $valor_consulta;
?>
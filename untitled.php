<?PHP
include("conexion/conexion.php");
$conn = conectate();

function login($conn, $username, $password) {
    $sql = "SELECT id, username, password_hash, nombre_completo, activo FROM usuario WHERE username = ?";
    $params = array($username);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        return false;
    }
    
    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    
    if ($user && $user['activo'] == 1) {
        // Actualizar último login
        $updateSql = "UPDATE usuario SET ultimo_login = GETDATE() WHERE id = ?";
        sqlsrv_query($conn, $updateSql, array($user['id']));
        
        // Establecer sesión
	    $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo $_SESSION['nombre_completo'] = $user['nombre_completo'];
        $_SESSION['logged_in'] = true;
        
        return true;
    }
    
    return false;
}

// No olvides iniciar la sesión si vas a usar variables de sesión
session_start();

login($conn, 'admin', '123456');
?>
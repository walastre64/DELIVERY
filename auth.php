<?php
require_once 'conexion/config.php';

class Auth {
    private $conn;
    
    public function __construct($conn) {
        $this->conn = $conn;
        $this->initSession();
    }
    
    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 3600,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => true,
                'httponly' => true,
                'samesite' => 'Strict'
            ]);
            session_start();
            session_regenerate_id(true);
        }
    }
    
    public function login($username, $password) {
        // Limpieza básica de inputs
        $username = trim($username);
        $password = trim($password);
        
        if (empty($username) || empty($password)) {
            error_log("Intento de login con campos vacíos");
            return false;
        }
        
        $sql = "SELECT id, username, password_hash, nombre_completo, activo FROM usuario WHERE username = ?";
        $params = array($username);
        $stmt = sqlsrv_query($this->conn, $sql, $params);
        
        if ($stmt === false) {
            error_log("Error en consulta SQL: " . print_r(sqlsrv_errors(), true));
            return false;
        }
        
        $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        
        // Verificación de contraseña sin password_verify
        if ($user && $user['password_hash'] === $password) {
            if ($user['activo'] == 1) {
                // Actualizar último login
                $updateSql = "UPDATE usuario SET ultimo_login = GETDATE() WHERE id = ?";
                sqlsrv_query($this->conn, $updateSql, array($user['id']));
                
                // Regenerar ID de sesión
                session_regenerate_id(true);
                
                // Establecer sesión
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['nombre_completo'] = $user['nombre_completo'];
                $_SESSION['logged_in'] = true;
                $_SESSION['last_activity'] = time();
                
                return true;
            } else {
                error_log("Intento de login con cuenta inactiva: " . $username);
            }
        } else {
            error_log("Intento de login fallido para usuario: " . $username);
        }
        
        return false;
    }
    
    public function logout() {
        $_SESSION = array();
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }
    
    public function getUserRoles($user_id) {
        $sql = "SELECT r.id, r.nombre FROM rol r
                JOIN usuario_rol ur ON r.id = ur.rol_id
                WHERE ur.usuario_id = ?";
        $stmt = sqlsrv_query($this->conn, $sql, array($user_id));
        
        $roles = array();
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            $roles[] = $row;
        }
        
        return $roles;
    }
    
    public function hasPermission($user_id, $permission_name) {
        $sql = "SELECT COUNT(*) as total FROM permiso p
                JOIN rol_permiso rp ON p.id = rp.permiso_id
                JOIN usuario_rol ur ON rp.rol_id = ur.rol_id
                WHERE ur.usuario_id = ? AND p.nombre = ?";
        $stmt = sqlsrv_query($this->conn, $sql, array($user_id, $permission_name));
        
        if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            return $row['total'] > 0;
        }
        
        return false;
    }
    
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header("Location: login.php");
            exit();
        }
    }
    
    public function requirePermission($permission_name) {
        $this->requireLogin();
        
        if (!$this->hasPermission($_SESSION['user_id'], $permission_name)) {
            header("Location: acceso_denegado.php");
            exit();
        }
    }
    
    public function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }
}
?>
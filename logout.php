<?php
require_once 'conexion/config.php';
require_once 'auth.php';

$auth = new Auth($conn);
$auth->logout();

header("Location: login.php");
exit();
?>
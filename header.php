<?php
// Iniciar la sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'conexion/config.php';
require_once 'auth.php';

$auth = new Auth($conn);

// Verificar si el usuario está logueado
if (!$auth->isLoggedIn()) {
    header("Location: login.php");
    exit();
}

$currentUser = [
    'id' => $_SESSION['user_id'],
    'username' => $_SESSION['username'],
    'nombre_completo' => $_SESSION['nombre_completo']
];

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Control de Ventas - Delivery</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/styles.css">
    
	<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
	<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
	<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Control Ventas Delivery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <?php if ($auth->hasPermission($currentUser['id'], 'view_sales')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas.php"><i class="fas fa-shopping-cart"></i> Ventas</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($auth->hasPermission($currentUser['id'], 'manage_masters')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="maestros.php"><i class="fas fa-cog"></i> Maestros</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($auth->hasPermission($currentUser['id'], 'manage_goals')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="metas.php"><i class="fas fa-bullseye"></i> Metas</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($auth->hasPermission($currentUser['id'], 'manage_rates')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="tasas.php"><i class="fas fa-exchange-alt"></i> Tasas</a>
                    </li>
                    <?php endif; ?>
                    <?php if ($auth->hasPermission($currentUser['id'], 'manage_users')): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="usuarios.php"><i class="fas fa-users"></i> Usuarios</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($currentUser['nombre_completo']); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="perfil.php"><i class="fas fa-user"></i> Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
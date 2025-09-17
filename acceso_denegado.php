<?php
require_once 'conexion/config.php';
require_once 'auth.php';

$auth = new Auth($conn);
$auth->requireLogin();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso Denegado | Sistema de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <?php require_once 'header.php'; ?>
    
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h4><i class="fas fa-ban"></i> Acceso Denegado</h4>
                    </div>
                    <div class="card-body text-center">
                        <i class="fas fa-lock fa-5x text-danger mb-4"></i>
                        <h3>No tienes permiso para acceder a esta página</h3>
                        <p class="lead">Contacta al administrador del sistema si necesitas acceso.</p>
                        <a href="index.php" class="btn btn-primary mt-3">
                            <i class="fas fa-home"></i> Volver al Inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require_once 'footer.php'; ?>
</body>
</html>
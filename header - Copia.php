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

<style>


#card-cumplimiento {
    background-color: #FFD700 !important; /* Un amarillo dorado para mejorar la visibilidad */
    color: #000 !important; /* Texto en negro para mayor contraste */
}

</style>    
    
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Control Ventas Delivery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ventas.php"><i class="fas fa-shopping-cart"></i> Ventas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="maestros.php"><i class="fas fa-cog"></i> Maestros</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="metas.php"><i class="fas fa-bullseye"></i> Metas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pagina/tasas.php"><i class="fas fa-exchange-alt"></i> Tasas</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-fluid mt-4">
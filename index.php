<?php
include("conexion/conexion.php");
conectate();
require_once 'header.php';
?>

<div class="row">
    <!-- Resumen de Ventas -->
    <div class="col-md-12">
        <div class="card mb-4">
            <div class="card-header bg-success text-white">
                <h5><i class="fas fa-chart-line"></i> Dashboard de Ventas</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Ventas del Mes Actual -->
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-header">Ventas Mes Actual (USD)</div>
                            <div class="card-body">
                                <h2 class="card-title" id="ventas-mes-actual">$0.00</h2>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Meta del Mes -->
                    <div class="col-md-3">
                        <div class="card text-white bg-info mb-3">
                            <div class="card-header">Meta del Mes (USD)</div>
                            <div class="card-body">
                                <h2 class="card-title" id="meta-mes">$0.00</h2>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Porcentaje de Cumplimiento -->
                    <div class="col-md-3">
                        <div class="card text-white mb-3" id="card-cumplimiento">
                            <div class="card-header">Cumplimiento de Meta</div>
                            <div class="card-body">
                                <h2 class="card-title" id="porcentaje-cumplimiento">0%</h2>
                                <div class="progress">
                                    <div id="progress-cumplimiento" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Vendedor Destacado -->
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-header">Vendedor Destacado</div>
                            <div class="card-body">
                                <h4 class="card-title" id="vendedor-destacado">-</h4>
                                <p class="card-text" id="monto-vendedor-destacado">$0.00</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Gráficos -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Ventas por Vendedor (Mes Actual)
                            </div>
                            <div class="card-body">
                                <canvas id="chartVentasVendedor" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                Ventas por Destino (Mes Actual)
                            </div>
                            <div class="card-body">
                                <canvas id="chartVentasDestino" height="300"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tabla de Últimas Ventas -->
                <div class="row mt-4">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header">
                                Últimas Ventas Registradas
                            </div>
                            <div class="card-body">
                                <table id="tablaUltimasVentas" class="table table-striped table-bordered" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Fecha</th>
                                            <th>Vendedor</th>
                                            <th>Monto USD</th>
                                            <th>Monto BS</th>
                                            <th>Destino</th>
                                            <th>Motorizado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Datos cargados por AJAX -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<!-- Tu script -->


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- DataTables CSS y JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>

<!-- Incluir Chart.js antes de dashboard.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="js/dashboard.js"></script>

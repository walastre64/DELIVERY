// JavaScript Document
$(document).ready(function() {
    // Cargar datos del dashboard
    cargarDashboard();
    
    // Configurar gráficos
    const ctxVendedor = document.getElementById('chartVentasVendedor').getContext('2d');
    const ctxDestino = document.getElementById('chartVentasDestino').getContext('2d');
    
    let chartVentasVendedor = new Chart(ctxVendedor, {
        type: 'bar',
        data: { labels: [], datasets: [{ label: 'Ventas USD', data: [], backgroundColor: '#007bff' }] },
        options: { responsive: true, scales: { y: { beginAtZero: true } } }
    });
    
    let chartVentasDestino = new Chart(ctxDestino, {
        type: 'pie',
        data: { labels: [], datasets: [{ label: 'Ventas USD', data: [], backgroundColor: ['#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8'] }] },
        options: { responsive: true }
    });
    
    function cargarDashboard() {
        $.ajax({
            url: 'datos/funciones.php',
            type: 'POST',
            data: { action: 'getDashboardData' },
            dataType: 'json',
            success: function(response) {
                // Actualizar resumen
                $('#ventas-mes-actual').text('$' + response.totalVentasMes.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                $('#meta-mes').text('$' + response.metaMes.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                
                // Calcular porcentaje de cumplimiento
                let porcentaje = (response.totalVentasMes / response.metaMes) * 100;
                porcentaje = Math.min(porcentaje, 100); // Máximo 100%
                
                $('#porcentaje-cumplimiento').text(porcentaje.toFixed(2) + '%');
                $('#progress-cumplimiento').css('width', porcentaje + '%');
                
                // Cambiar color según cumplimiento
                if (porcentaje >= 100) {
                    $('#card-cumplimiento').removeClass().addClass('card text-white bg-success mb-3');
                    $('#progress-cumplimiento').removeClass().addClass('progress-bar bg-success');
                } else if (porcentaje >= 75) {
                    $('#card-cumplimiento').removeClass().addClass('card text-white bg-info mb-3');
                    $('#progress-cumplimiento').removeClass().addClass('progress-bar bg-info');
                } else if (porcentaje >= 50) {
                    $('#card-cumplimiento').removeClass().addClass('card text-white bg-warning mb-3');
                    $('#progress-cumplimiento').removeClass().addClass('progress-bar bg-warning');
                } else {
                    $('#card-cumplimiento').removeClass().addClass('card text-white bg-danger mb-3');
                    $('#progress-cumplimiento').removeClass().addClass('progress-bar bg-danger');
                }
                
                // Vendedor destacado
                if (response.vendedorDestacado) {
                    $('#vendedor-destacado').text(response.vendedorDestacado.nombre);
                    $('#monto-vendedor-destacado').text('$' + response.vendedorDestacado.total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                }
                
                // Actualizar gráficos
                chartVentasVendedor.data.labels = response.ventasPorVendedor.map(v => v.nombre);
                chartVentasVendedor.data.datasets[0].data = response.ventasPorVendedor.map(v => v.total);
                chartVentasVendedor.update();
                
                chartVentasDestino.data.labels = response.ventasPorDestino.map(d => d.nombre);
                chartVentasDestino.data.datasets[0].data = response.ventasPorDestino.map(d => d.total);
                chartVentasDestino.update();
                
                // Cargar últimas ventas
                $('#tablaUltimasVentas').DataTable({
                    data: response.ultimasVentas,
                    columns: [
                        { data: 'id' },
                        { data: 'fecha' },
                        { data: 'vendedor' },
                        { data: 'monto_usd', render: function(data) { return '$' + parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); } },
                        { data: 'monto_bs', render: function(data) { return 'Bs. ' + parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); } },
                        { data: 'destino' },
                        { data: 'motorizado' }
                    ],
                    language: {
                        url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
                    },
                    order: [[0, 'desc']],
                    pageLength: 5
                });
            },
            error: function(xhr, status, error) {
                console.error(error);
                alert('Error al cargar los datos del dashboard');
            }
        });
    }
});
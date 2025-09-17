<?php
// 1. Incluir primero config.php que contiene la conexión
require_once 'conexion/config.php';

// 2. Incluir auth.php que maneja la autenticación
require_once '	auth.php';

// 3. Crear instancia de Auth (esto inicia la sesión)
$auth = new Auth($conn);

// 4. Verificar permisos
$auth->requirePermission('view_sales');

// 5. Incluir el header que contiene HTML
require_once 'header.php';

// Obtener la tasa del día actual
$tasaHoy = 0;
$sql = "SELECT TOP 1 tasa FROM tasa_cambio WHERE fecha = CONVERT(date, GETDATE()) ORDER BY id DESC";
$stmt = sqlsrv_query($conn, $sql);
if ($stmt && sqlsrv_fetch($stmt)) {
    $tasaHoy = sqlsrv_get_field($stmt, 0);
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-cash-register"></i> Registro de Ventas</h5>
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#nuevaVentaModal">
                        <i class="fas fa-plus"></i> Nueva Venta
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="filtroFechaInicio" class="form-label">Fecha Inicio</label>
                        <input type="date" class="form-control" id="filtroFechaInicio">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroFechaFin" class="form-label">Fecha Fin</label>
                        <input type="date" class="form-control" id="filtroFechaFin">
                    </div>
                    <div class="col-md-3">
                        <label for="filtroVendedor" class="form-label">Vendedor</label>
                        <select class="form-select" id="filtroVendedor">
                            <option value="">Todos</option>
                            <?php
                            $sql = "SELECT id, nombre FROM vendedor WHERE activo = 1 ORDER BY nombre";
                            $stmt = sqlsrv_query($conn, $sql);
                            while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary" id="btnFiltrar">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                        <button class="btn btn-secondary ms-2" id="btnResetFiltros">
                            <i class="fas fa-undo"></i> Resetear
                        </button>
                    </div>
                </div>

                <!-- Tabla de Ventas -->
                <table id="tablaVentas" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Vendedor</th>
                            <th>Turno</th>
                            <th>Destino</th>
                            <th>Motorizado</th>
                            <th>Monto USD</th>
                            <th>Monto BS</th>
                            <th>Observación</th>
                            <th>Acciones</th>
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

<!-- Modal para Nueva Venta -->
<div class="modal fade" id="nuevaVentaModal" tabindex="-1" aria-labelledby="nuevaVentaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaVentaModalLabel">Registrar Nueva Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaVenta">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ventaFecha" class="form-label">Fecha de Venta</label>
                            <input type="date" class="form-control" id="ventaFecha" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ventaVendedor" class="form-label">Vendedor</label>
                            <select class="form-select" id="ventaVendedor" required>
                                <option value="">Seleccionar...</option>
                                <?php
                                $sql = "SELECT id, nombre FROM vendedor WHERE activo = 1 ORDER BY nombre";
                                $stmt = sqlsrv_query($conn, $sql);
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ventaTurno" class="form-label">Turno</label>
                            <select class="form-select" id="ventaTurno" required>
                                <option value="">Seleccionar...</option>
                                <?php
                                $sql = "SELECT id, nombre FROM turno ORDER BY nombre";
                                $stmt = sqlsrv_query($conn, $sql);
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ventaDestino" class="form-label">Destino</label>
                            <select class="form-select" id="ventaDestino" required>
                                <option value="">Seleccionar...</option>
                                <?php
                                $sql = "SELECT id, nombre FROM destino ORDER BY nombre";
                                $stmt = sqlsrv_query($conn, $sql);
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ventaMotorizado" class="form-label">Motorizado</label>
                            <select class="form-select" id="ventaMotorizado" required>
                                <option value="">Seleccionar...</option>
                                <?php
                                $sql = "SELECT id, nombre FROM motorizado WHERE activo = 1 ORDER BY nombre";
                                $stmt = sqlsrv_query($conn, $sql);
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="'.$row['id'].'">'.$row['nombre'].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ventaTasa" class="form-label">Tasa del Día (USD a BS)</label>
                            <input type="number" step="0.01" class="form-control" id="ventaTasa" value="<?php echo $tasaHoy; ?>" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ventaMontoUSD" class="form-label">Monto en USD</label>
                            <input type="number" step="0.01" class="form-control" id="ventaMontoUSD" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="ventaMontoBS" class="form-label">Monto en BS</label>
                            <input type="number" step="0.01" class="form-control" id="ventaMontoBS" readonly>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="ventaObservacion" class="form-label">Observación</label>
                        <textarea class="form-control" id="ventaObservacion" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Venta -->
<div class="modal fade" id="editarVentaModal" tabindex="-1" aria-labelledby="editarVentaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarVentaModalLabel">Editar Venta</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarVenta">
                <input type="hidden" id="editarVentaId">
                <div class="modal-body">
                    <!-- Contenido similar al modal de nueva venta -->
                    <!-- Se carga dinámicamente via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Venta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<!-- JavaScript para ventas.php -->
<script>
$(document).ready(function() {
    // Inicializar DataTable
    const tablaVentas = $('#tablaVentas').DataTable({
        ajax: {
            url: '../datos/funciones.php',
            type: 'POST',
            data: function(d) {
                d.action = 'getVentas';
            },
            dataSrc: ''
        },
        columns: [
            { data: 'id' },
            { data: 'fecha' },
            { data: 'vendedor' },
            { data: 'turno' },
            { data: 'destino' },
            { data: 'motorizado' },
            { 
                data: 'monto_usd',
                render: function(data) {
                    return '$' + parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: 'monto_bs',
                render: function(data) {
                    return 'Bs. ' + parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { data: 'observacion' },
            {
                data: 'id',
                render: function(data) {
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary editar-venta" data-id="${data}">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger eliminar-venta" data-id="${data}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    `;
                },
                orderable: false
            }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        order: [[1, 'desc']],
        responsive: true
    });

    // Calcular BS automáticamente al cambiar USD o tasa
    $('#ventaMontoUSD, #ventaTasa').on('input', function() {
        const montoUSD = parseFloat($('#ventaMontoUSD').val()) || 0;
        const tasa = parseFloat($('#ventaTasa').val()) || 0;
        const montoBS = montoUSD * tasa;
        $('#ventaMontoBS').val(montoBS.toFixed(2));
    });

    // Filtrar ventas
    $('#btnFiltrar').click(function() {
        const fechaInicio = $('#filtroFechaInicio').val();
        const fechaFin = $('#filtroFechaFin').val();
        const vendedorId = $('#filtroVendedor').val();
        
        tablaVentas.ajax.url('../datos/funciones.php?action=getVentas&fechaInicio=' + fechaInicio + 
                            '&fechaFin=' + fechaFin + '&vendedorId=' + vendedorId).load();
    });

    // Resetear filtros
    $('#btnResetFiltros').click(function() {
        $('#filtroFechaInicio').val('');
        $('#filtroFechaFin').val('');
        $('#filtroVendedor').val('');
        tablaVentas.ajax.url('../datos/funciones.php?action=getVentas').load();
    });

    // Guardar nueva venta
    $('#formNuevaVenta').submit(function(e) {
        e.preventDefault();
        
        const ventaData = {
            action: 'guardarVenta',
            fecha: $('#ventaFecha').val(),
            id_vendedor: $('#ventaVendedor').val(),
            id_turno: $('#ventaTurno').val(),
            id_destino: $('#ventaDestino').val(),
            id_motorizado: $('#ventaMotorizado').val(),
            monto_usd: $('#ventaMontoUSD').val(),
            monto_bs: $('#ventaMontoBS').val(),
            tasa: $('#ventaTasa').val(),
            observacion: $('#ventaObservacion').val()
        };
        
        $.ajax({
            url: '../datos/funciones.php',
            type: 'POST',
            data: ventaData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#nuevaVentaModal').modal('hide');
                    tablaVentas.ajax.reload();
                    $('#formNuevaVenta')[0].reset();
                    toastr.success('Venta registrada correctamente');
                } else {
                    toastr.error(response.message || 'Error al registrar la venta');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    });

    // Editar venta (cargar datos en modal)
    $(document).on('click', '.editar-venta', function() {
        const ventaId = $(this).data('id');
        
        $.ajax({
            url: '../datos/funciones.php',
            type: 'POST',
            data: { action: 'getVentaById', id: ventaId },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    $('#editarVentaId').val(response.id);
                    
                    // Cargar formulario de edición
                    const modalBody = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaFecha" class="form-label">Fecha de Venta</label>
                                <input type="date" class="form-control" id="editarVentaFecha" value="${response.fecha}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaVendedor" class="form-label">Vendedor</label>
                                <select class="form-select" id="editarVentaVendedor" required>
                                    ${generateOptions('vendedor', response.id_vendedor)}
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaTurno" class="form-label">Turno</label>
                                <select class="form-select" id="editarVentaTurno" required>
                                    ${generateOptions('turno', response.id_turno)}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaDestino" class="form-label">Destino</label>
                                <select class="form-select" id="editarVentaDestino" required>
                                    ${generateOptions('destino', response.id_destino)}
                                </select>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaMotorizado" class="form-label">Motorizado</label>
                                <select class="form-select" id="editarVentaMotorizado" required>
                                    ${generateOptions('motorizado', response.id_motorizado)}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaTasa" class="form-label">Tasa del Día (USD a BS)</label>
                                <input type="number" step="0.01" class="form-control" id="editarVentaTasa" value="${response.tasa}" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaMontoUSD" class="form-label">Monto en USD</label>
                                <input type="number" step="0.01" class="form-control" id="editarVentaMontoUSD" value="${response.monto_usd}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarVentaMontoBS" class="form-label">Monto en BS</label>
                                <input type="number" step="0.01" class="form-control" id="editarVentaMontoBS" value="${response.monto_bs}" readonly>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editarVentaObservacion" class="form-label">Observación</label>
                            <textarea class="form-control" id="editarVentaObservacion" rows="2">${response.observacion || ''}</textarea>
                        </div>
                    `;
                    
                    $('#editarVentaModal .modal-body').html(modalBody);
                    
                    // Calcular BS automáticamente al cambiar USD o tasa
                    $('#editarVentaMontoUSD, #editarVentaTasa').on('input', function() {
                        const montoUSD = parseFloat($('#editarVentaMontoUSD').val()) || 0;
                        const tasa = parseFloat($('#editarVentaTasa').val()) || 0;
                        const montoBS = montoUSD * tasa;
                        $('#editarVentaMontoBS').val(montoBS.toFixed(2));
                    });
                    
                    $('#editarVentaModal').modal('show');
                } else {
                    toastr.error('No se encontró la venta');
                }
            },
            error: function() {
                toastr.error('Error al cargar los datos de la venta');
            }
        });
    });

    // Actualizar venta
    $('#formEditarVenta').submit(function(e) {
        e.preventDefault();
        
        const ventaData = {
            action: 'actualizarVenta',
            id: $('#editarVentaId').val(),
            fecha: $('#editarVentaFecha').val(),
            id_vendedor: $('#editarVentaVendedor').val(),
            id_turno: $('#editarVentaTurno').val(),
            id_destino: $('#editarVentaDestino').val(),
            id_motorizado: $('#editarVentaMotorizado').val(),
            monto_usd: $('#editarVentaMontoUSD').val(),
            monto_bs: $('#editarVentaMontoBS').val(),
            tasa: $('#editarVentaTasa').val(),
            observacion: $('#editarVentaObservacion').val()
        };
        
        $.ajax({
            url: '../datos/funciones.php',
            type: 'POST',
            data: ventaData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editarVentaModal').modal('hide');
                    tablaVentas.ajax.reload();
                    toastr.success('Venta actualizada correctamente');
                } else {
                    toastr.error(response.message || 'Error al actualizar la venta');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    });

    // Eliminar venta
    $(document).on('click', '.eliminar-venta', function() {
        const ventaId = $(this).data('id');
        
        if (confirm('¿Está seguro de eliminar esta venta?')) {
            $.ajax({
                url: '../datos/funciones.php',
                type: 'POST',
                data: { action: 'eliminarVenta', id: ventaId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        tablaVentas.ajax.reload();
                        toastr.success('Venta eliminada correctamente');
                    } else {
                        toastr.error(response.message || 'Error al eliminar la venta');
                    }
                },
                error: function() {
                    toastr.error('Error al conectar con el servidor');
                }
            });
        }
    });

    // Función para generar opciones de select
    function generateOptions(table, selectedId) {
        let options = '<option value="">Seleccionar...</option>';
        
        // En una implementación real, harías una llamada AJAX para obtener los datos
        // Aquí simplificamos con un array de ejemplo
        const data = {
            vendedor: <?php
                $sql = "SELECT id, nombre FROM vendedor WHERE activo = 1 ORDER BY nombre";
                $stmt = sqlsrv_query($conn, $sql);
                $vendedores = array();
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $vendedores[] = $row;
                }
                echo json_encode($vendedores);
                ?>,
            turno: <?php
                $sql = "SELECT id, nombre FROM turno ORDER BY nombre";
                $stmt = sqlsrv_query($conn, $sql);
                $turnos = array();
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $turnos[] = $row;
                }
                echo json_encode($turnos);
                ?>,
            destino: <?php
                $sql = "SELECT id, nombre FROM destino ORDER BY nombre";
                $stmt = sqlsrv_query($conn, $sql);
                $destinos = array();
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $destinos[] = $row;
                }
                echo json_encode($destinos);
                ?>,
            motorizado: <?php
                $sql = "SELECT id, nombre FROM motorizado WHERE activo = 1 ORDER BY nombre";
                $stmt = sqlsrv_query($conn, $sql);
                $motorizados = array();
                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                    $motorizados[] = $row;
                }
                echo json_encode($motorizados);
                ?>
        };
        
        data[table].forEach(item => {
            options += `<option value="${item.id}" ${item.id == selectedId ? 'selected' : ''}>${item.nombre}</option>`;
        });
        
        return options;
    }
});
</script>
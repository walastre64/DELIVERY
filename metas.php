<?php
require_once 'conexion/config.php';
require_once 'header.php';

//$auth = new Auth($conn);
//$auth->requirePermission('permiso_requerido');

// Obtener el año actual y los próximos 5 años para el selector
$anioActual = date('Y');
$anios = range($anioActual, $anioActual + 5);
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-bullseye"></i> Administración de Metas Mensuales</h5>
                    <button class="btn btn-sm btn-light" data-bs-toggle="modal" data-bs-target="#nuevaMetaModal">
                        <i class="fas fa-plus"></i> Nueva Meta
                    </button>
                </div>
            </div>
            <div class="card-body">
                <!-- Filtros -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <label for="filtroAnio" class="form-label">Año</label>
                        <select class="form-select" id="filtroAnio">
                            <?php foreach ($anios as $anio): ?>
                                <option value="<?php echo $anio; ?>" <?php echo $anio == $anioActual ? 'selected' : ''; ?>>
                                    <?php echo $anio; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary" id="btnFiltrar">
                            <i class="fas fa-filter"></i> Filtrar
                        </button>
                    </div>
                </div>

                <!-- Tabla de Metas -->
                <table id="tablaMetas" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <th>Año</th>
                            <th>Mes</th>
                            <th>Meta (USD)</th>
                            <th>Ventas Actuales (USD)</th>
                            <th>Cumplimiento</th>
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

<!-- Modal para Nueva Meta -->
<div class="modal fade" id="nuevaMetaModal" tabindex="-1" aria-labelledby="nuevaMetaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevaMetaModalLabel">Nueva Meta Mensual</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevaMeta">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="metaAnio" class="form-label">Año</label>
                            <select class="form-select" id="metaAnio" required>
                                <?php foreach ($anios as $anio): ?>
                                    <option value="<?php echo $anio; ?>" <?php echo $anio == $anioActual ? 'selected' : ''; ?>>
                                        <?php echo $anio; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="metaMes" class="form-label">Mes</label>
                            <select class="form-select" id="metaMes" required>
                                <option value="">Seleccionar...</option>
                                <?php
                                $meses = [
                                    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
                                    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
                                    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
                                ];
                                foreach ($meses as $num => $nombre): ?>
                                    <option value="<?php echo $num; ?>"><?php echo $nombre; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="metaMonto" class="form-label">Monto en USD</label>
                        <input type="number" step="0.01" min="0" class="form-control" id="metaMonto" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar Meta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Meta -->
<div class="modal fade" id="editarMetaModal" tabindex="-1" aria-labelledby="editarMetaModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarMetaModalLabel">Editar Meta Mensual</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarMeta">
                <input type="hidden" id="editarMetaId">
                <div class="modal-body">
                    <!-- Contenido cargado dinámicamente -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar Meta</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<!-- JavaScript para metas.php -->
<script>
$(document).ready(function() {
    // Inicializar DataTable
    const tablaMetas = $('#tablaMetas').DataTable({
        ajax: {
            url: '../datos/funciones.php',
            type: 'POST',
            data: function(d) {
                d.action = 'getMetas';
                d.anio = $('#filtroAnio').val();
            },
            dataSrc: ''
        },
        columns: [
            { data: 'anio' },
            { 
                data: 'mes',
                render: function(data) {
                    const meses = [
                        '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ];
                    return meses[data];
                }
            },
            { 
                data: 'monto',
                render: function(data) {
                    return '$' + parseFloat(data).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: 'ventas_actuales',
                render: function(data) {
                    return '$' + parseFloat(data || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                }
            },
            { 
                data: null,
                render: function(data) {
                    const porcentaje = (data.ventas_actuales / data.monto) * 100;
                    const porcentajeFormateado = porcentaje.toFixed(2) + '%';
                    
                    let color = 'danger';
                    if (porcentaje >= 100) color = 'success';
                    else if (porcentaje >= 75) color = 'info';
                    else if (porcentaje >= 50) color = 'warning';
                    
                    return `
                        <div class="d-flex align-items-center">
                            <div class="progress flex-grow-1" style="height: 20px;">
                                <div class="progress-bar bg-${color}" role="progressbar" 
                                     style="width: ${Math.min(porcentaje, 100)}%" 
                                     aria-valuenow="${porcentaje}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="100">
                                </div>
                            </div>
                            <small class="ms-2">${porcentajeFormateado}</small>
                        </div>
                    `;
                }
            },
            {
                data: 'id',
                render: function(data, type, row) {
                    // No permitir editar metas de meses pasados
                    const hoy = new Date();
                    const mesActual = hoy.getMonth() + 1; // Los meses en JS van de 0-11
                    const anioActual = hoy.getFullYear();
                    
                    const puedeEditar = row.anio > anioActual || 
                                      (row.anio == anioActual && row.mes >= mesActual);
                    
                    return `
                        <div class="btn-group">
                            <button class="btn btn-sm btn-outline-primary editar-meta" 
                                    data-id="${data}" 
                                    ${!puedeEditar ? 'disabled title="No se puede editar metas de meses pasados"' : ''}>
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-outline-danger eliminar-meta" data-id="${data}">
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
        order: [[0, 'desc'], [1, 'desc']],
        responsive: true
    });

    // Filtrar metas por año
    $('#btnFiltrar').click(function() {
        tablaMetas.ajax.reload();
    });

    // Guardar nueva meta
    $('#formNuevaMeta').submit(function(e) {
        e.preventDefault();
        
        const metaData = {
            action: 'guardarMeta',
            anio: $('#metaAnio').val(),
            mes: $('#metaMes').val(),
            monto: $('#metaMonto').val()
        };
        
        $.ajax({
            url: '../datos/funciones.php',
            type: 'POST',
            data: metaData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#nuevaMetaModal').modal('hide');
                    tablaMetas.ajax.reload();
                    $('#formNuevaMeta')[0].reset();
                    toastr.success('Meta registrada correctamente');
                } else {
                    toastr.error(response.message || 'Error al registrar la meta');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    });

    // Cargar datos para edición
    $(document).on('click', '.editar-meta', function() {
        const metaId = $(this).data('id');
        
        $.ajax({
            url: '../datos/funciones.php',
            type: 'POST',
            data: { action: 'getMetaById', id: metaId },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    $('#editarMetaId').val(response.id);
                    
                    const meses = [
                        '', 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
                        'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
                    ];
                    
                    let optionsAnio = '';
                    for (let anio = <?php echo $anioActual; ?>; anio <= <?php echo $anioActual + 5; ?>; anio++) {
                        optionsAnio += `<option value="${anio}" ${anio == response.anio ? 'selected' : ''}>${anio}</option>`;
                    }
                    
                    let optionsMes = '';
                    for (let mes = 1; mes <= 12; mes++) {
                        optionsMes += `<option value="${mes}" ${mes == response.mes ? 'selected' : ''}>${meses[mes]}</option>`;
                    }
                    
                    const modalBody = `
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="editarMetaAnio" class="form-label">Año</label>
                                <select class="form-select" id="editarMetaAnio" required>
                                    ${optionsAnio}
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="editarMetaMes" class="form-label">Mes</label>
                                <select class="form-select" id="editarMetaMes" required>
                                    <option value="">Seleccionar...</option>
                                    ${optionsMes}
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="editarMetaMonto" class="form-label">Monto en USD</label>
                            <input type="number" step="0.01" min="0" class="form-control" 
                                   id="editarMetaMonto" value="${response.monto}" required>
                        </div>
                    `;
                    
                    $('#editarMetaModal .modal-body').html(modalBody);
                    $('#editarMetaModal').modal('show');
                } else {
                    toastr.error('No se encontró la meta');
                }
            },
            error: function() {
                toastr.error('Error al cargar los datos de la meta');
            }
        });
    });

    // Actualizar meta
    $('#formEditarMeta').submit(function(e) {
        e.preventDefault();
        
        const metaData = {
            action: 'actualizarMeta',
            id: $('#editarMetaId').val(),
            anio: $('#editarMetaAnio').val(),
            mes: $('#editarMetaMes').val(),
            monto: $('#editarMetaMonto').val()
        };
        
        $.ajax({
            url: '../datos/funciones.php',
            type: 'POST',
            data: metaData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editarMetaModal').modal('hide');
                    tablaMetas.ajax.reload();
                    toastr.success('Meta actualizada correctamente');
                } else {
                    toastr.error(response.message || 'Error al actualizar la meta');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    });

    // Eliminar meta
    $(document).on('click', '.eliminar-meta', function() {
        const metaId = $(this).data('id');
        
        if (confirm('¿Está seguro de eliminar esta meta?')) {
            $.ajax({
                url: '../datos/funciones.php',
                type: 'POST',
                data: { action: 'eliminarMeta', id: metaId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        tablaMetas.ajax.reload();
                        toastr.success('Meta eliminada correctamente');
                    } else {
                        toastr.error(response.message || 'Error al eliminar la meta');
                    }
                },
                error: function() {
                    toastr.error('Error al conectar con el servidor');
                }
            });
        }
    });
});
</script>
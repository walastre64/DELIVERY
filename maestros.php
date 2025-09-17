<?php
require_once 'conexion/config.php';
require_once 'header.php';

// Determinar qué maestro mostrar
$tabla = isset($_GET['tabla']) ? $_GET['tabla'] : 'vendedor';

echo $tabla;

$titulos = [
    'vendedor' => 'Vendedores',
    'turno' => 'Turnos',
    'destino' => 'Destinos',
    'motorizado' => 'Motorizados'
];
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5><i class="fas fa-cog"></i> Administración de Maestros - <?php echo $titulos[$tabla]; ?></h5>
                    <div class="btn-group">
                        <button type="button" class="btn btn-sm btn-light dropdown-toggle" data-bs-toggle="dropdown">
                            Cambiar Maestro
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item <?php echo $tabla == 'vendedor' ? 'active' : ''; ?>" href="?tabla=vendedor">Vendedores</a></li>
                            <li><a class="dropdown-item <?php echo $tabla == 'turno' ? 'active' : ''; ?>" href="?tabla=turno">Turnos</a></li>
                            <li><a class="dropdown-item <?php echo $tabla == 'destino' ? 'active' : ''; ?>" href="?tabla=destino">Destinos</a></li>
                            <li><a class="dropdown-item <?php echo $tabla == 'motorizado' ? 'active' : ''; ?>" href="?tabla=motorizado">Motorizados</a></li>
                        </ul>
                        <button class="btn btn-sm btn-light ms-2" data-bs-toggle="modal" data-bs-target="#nuevoRegistroModal">
                            <i class="fas fa-plus"></i> Nuevo
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Tabla de registros -->
                <table id="tablaMaestros" class="table table-striped table-bordered" style="width:100%">
                    <thead>
                        <tr>
                            <?php if ($tabla == 'vendedor' || $tabla == 'motorizado'): ?>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            <?php elseif ($tabla == 'turno'): ?>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            <?php elseif ($tabla == 'destino'): ?>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            <?php endif; ?>
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

<!-- Modal para Nuevo Registro -->
<div class="modal fade" id="nuevoRegistroModal" tabindex="-1" aria-labelledby="nuevoRegistroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="nuevoRegistroModalLabel">Nuevo <?php echo $titulos[$tabla]; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formNuevoRegistro">
                <input type="hidden" name="tabla" value="<?php echo $tabla; ?>">
                <div class="modal-body">
                    <?php if ($tabla == 'vendedor' || $tabla == 'motorizado'): ?>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="activo" name="activo" checked>
                            <label class="form-check-label" for="activo">Activo</label>
                        </div>
                    <?php elseif ($tabla == 'turno'): ?>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Turno</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3"></textarea>
                        </div>
                    <?php elseif ($tabla == 'destino'): ?>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre del Destino</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para Editar Registro -->
<div class="modal fade" id="editarRegistroModal" tabindex="-1" aria-labelledby="editarRegistroModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editarRegistroModalLabel">Editar <?php echo $titulos[$tabla]; ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="formEditarRegistro">
                <input type="hidden" name="tabla" value="<?php echo $tabla; ?>">
                <input type="hidden" id="editarId" name="id">
                <div class="modal-body">
                    <!-- Contenido dinámico según el tipo de maestro -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'footer.php'; ?>

<!-- JavaScript para maestros.php -->
<script>
$(document).ready(function() {
    const tabla = '<?php echo $tabla; ?>';
    
    // Configuración de DataTable
    const tablaMaestros = $('#tablaMaestros').DataTable({
        ajax: {
            url: 'datos/funciones.php',
            type: 'POST',
            data: {
                action: 'getMaestros',
                tabla: tabla
            },
            dataSrc: ''
        },
        columns: getColumnsConfig(tabla),
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json'
        },
        responsive: true
    });

    // Función para configurar las columnas según la tabla
    function getColumnsConfig(tabla) {
        const baseColumns = [
            { data: 'id' },
            { data: 'nombre' }
        ];

        switch(tabla) {
            case 'vendedor':
            case 'motorizado':
                baseColumns.push({
                    data: 'activo',
                    render: function(data) {
                        return data ? '<span class="badge bg-success">Activo</span>' : '<span class="badge bg-danger">Inactivo</span>';
                    }
                });
                baseColumns.push({
                    data: 'id',
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary editar-registro" data-id="${data}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger eliminar-registro" data-id="${data}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                    orderable: false
                });
                break;
                
            case 'turno':
                baseColumns.push({ data: 'descripcion' });
                baseColumns.push({
                    data: 'id',
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary editar-registro" data-id="${data}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger eliminar-registro" data-id="${data}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                    orderable: false
                });
                break;
                
            case 'destino':
                baseColumns.push({
                    data: 'id',
                    render: function(data) {
                        return `
                            <div class="btn-group">
                                <button class="btn btn-sm btn-outline-primary editar-registro" data-id="${data}">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger eliminar-registro" data-id="${data}">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        `;
                    },
                    orderable: false
                });
                break;
        }
        
        return baseColumns;
    }

    // Guardar nuevo registro
    $('#formNuevoRegistro').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'datos/funciones.php?action=guardarMaestro',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#nuevoRegistroModal').modal('hide');
                    tablaMaestros.ajax.reload();
                    $('#formNuevoRegistro')[0].reset();
                    toastr.success('Registro creado correctamente');
                } else {
                    toastr.error(response.message || 'Error al crear el registro');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    });

    // Cargar datos para edición
    $(document).on('click', '.editar-registro', function() {
        const id = $(this).data('id');
        
        $.ajax({
            url: 'datos/funciones.php',
            type: 'POST',
            data: {
                action: 'getMaestroById',
                tabla: tabla,
                id: id
            },
            dataType: 'json',
            success: function(response) {
                if (response) {
                    $('#editarId').val(response.id);
                    
                    let modalBody = '';
                    
                    if (tabla === 'vendedor' || tabla === 'motorizado') {
                        modalBody = `
                            <div class="mb-3">
                                <label for="editarNombre" class="form-label">Nombre</label>
                                <input type="text" class="form-control" id="editarNombre" name="nombre" value="${response.nombre}" required>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" class="form-check-input" id="editarActivo" name="activo" ${response.activo ? 'checked' : ''}>
                                <label class="form-check-label" for="editarActivo">Activo</label>
                            </div>
                        `;
                    } else if (tabla === 'turno') {
                        modalBody = `
                            <div class="mb-3">
                                <label for="editarNombre" class="form-label">Nombre del Turno</label>
                                <input type="text" class="form-control" id="editarNombre" name="nombre" value="${response.nombre}" required>
                            </div>
                            <div class="mb-3">
                                <label for="editarDescripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="editarDescripcion" name="descripcion" rows="3">${response.descripcion || ''}</textarea>
                            </div>
                        `;
                    } else if (tabla === 'destino') {
                        modalBody = `
                            <div class="mb-3">
                                <label for="editarNombre" class="form-label">Nombre del Destino</label>
                                <input type="text" class="form-control" id="editarNombre" name="nombre" value="${response.nombre}" required>
                            </div>
                        `;
                    }
                    
                    $('#editarRegistroModal .modal-body').html(modalBody);
                    $('#editarRegistroModal').modal('show');
                } else {
                    toastr.error('No se encontró el registro');
                }
            },
            error: function() {
                toastr.error('Error al cargar los datos del registro');
            }
        });
    });

    // Actualizar registro
    $('#formEditarRegistro').submit(function(e) {
        e.preventDefault();
        
        const formData = $(this).serialize();
        
        $.ajax({
            url: 'datos/funciones.php?action=actualizarMaestro',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    $('#editarRegistroModal').modal('hide');
                    tablaMaestros.ajax.reload();
                    toastr.success('Registro actualizado correctamente');
                } else {
                    toastr.error(response.message || 'Error al actualizar el registro');
                }
            },
            error: function() {
                toastr.error('Error al conectar con el servidor');
            }
        });
    });

    // Eliminar registro
    $(document).on('click', '.eliminar-registro', function() {
        const id = $(this).data('id');
        
        if (confirm('¿Está seguro de eliminar este registro?')) {
            $.ajax({
                url: 'datos/funciones.php',
                type: 'POST',
                data: {
                    action: 'eliminarMaestro',
                    tabla: tabla,
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        tablaMaestros.ajax.reload();
                        toastr.success('Registro eliminado correctamente');
                    } else {
                        toastr.error(response.message || 'Error al eliminar el registro');
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
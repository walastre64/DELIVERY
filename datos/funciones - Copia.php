<?php
header('Content-Type: application/json');
require_once '../conexion/config.php';



if (isset($_POST['action'])) {
    $action = $_POST['action'];
    
    switch ($action) {
        case 'getDashboardData':
            echo getDashboardData();
            break;
            
        // Otros casos...
    }
}

function getDashboardData() {
    global $conn;
    
    $response = array();
    $mesActual = date('m');
    $anioActual = date('Y');
    
    // 1. Obtener total de ventas del mes actual
    $sql = "SELECT SUM(monto_usd) as total FROM venta 
            WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?";
    $params = array($mesActual, $anioActual);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
			
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $response['totalVentasMes'] = $row['total'] ? (float)$row['total'] : 0;
    
    // 2. Obtener meta del mes actual
    $sql = "SELECT monto FROM meta_mes WHERE mes = ? AND anio = ?";
    $stmt = sqlsrv_query($conn, $sql, array($mesActual, $anioActual));
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $response['metaMes'] = $row ? (float)$row['monto'] : 0;
    
    // 3. Obtener vendedor destacado (mayor monto vendido)
    $sql = "SELECT TOP 1 v.nombre, SUM(ve.monto_usd) as total 
            FROM venta ve 
            JOIN vendedor v ON ve.id_vendedor = v.id 
            WHERE MONTH(ve.fecha) = ? AND YEAR(ve.fecha) = ?
            GROUP BY v.nombre 
            ORDER BY total DESC";
    $stmt = sqlsrv_query($conn, $sql, array($mesActual, $anioActual));
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    $response['vendedorDestacado'] = $row ? array('nombre' => $row['nombre'], 'total' => (float)$row['total']) : null;
    
    // 4. Ventas por vendedor
    $sql = "SELECT v.nombre, SUM(ve.monto_usd) as total 
            FROM venta ve 
            JOIN vendedor v ON ve.id_vendedor = v.id 
            WHERE MONTH(ve.fecha) = ? AND YEAR(ve.fecha) = ?
            GROUP BY v.nombre 
            ORDER BY total DESC";
    $stmt = sqlsrv_query($conn, $sql, array($mesActual, $anioActual));
    
    $response['ventasPorVendedor'] = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $response['ventasPorVendedor'][] = array(
            'nombre' => $row['nombre'],
            'total' => (float)$row['total']
        );
    }
    
    // 5. Ventas por destino
    $sql = "SELECT d.nombre, SUM(ve.monto_usd) as total 
            FROM venta ve 
            JOIN destino d ON ve.id_destino = d.id 
            WHERE MONTH(ve.fecha) = ? AND YEAR(ve.fecha) = ?
            GROUP BY d.nombre 
            ORDER BY total DESC";
    $stmt = sqlsrv_query($conn, $sql, array($mesActual, $anioActual));
    
    $response['ventasPorDestino'] = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $response['ventasPorDestino'][] = array(
            'nombre' => $row['nombre'],
            'total' => (float)$row['total']
        );
    }
    
    // 6. Últimas ventas registradas
    $sql = "SELECT TOP 10 ve.id, CONVERT(VARCHAR, ve.fecha, 103) as fecha, 
            v.nombre as vendedor, ve.monto_usd, ve.monto_bs, 
            d.nombre as destino, m.nombre as motorizado
            FROM venta ve
            JOIN vendedor v ON ve.id_vendedor = v.id
            JOIN destino d ON ve.id_destino = d.id
            JOIN motorizado m ON ve.id_motorizado = m.id
            ORDER BY ve.fecha DESC, ve.id DESC";
    $stmt = sqlsrv_query($conn, $sql);
    
    $response['ultimasVentas'] = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $response['ultimasVentas'][] = $row;
    }
    
    return json_encode($response);
}

// Otras funciones...
// Obtener listado de ventas
function getVentas() {
    global $conn;
    
    $fechaInicio = isset($_GET['fechaInicio']) ? $_GET['fechaInicio'] : null;
    $fechaFin = isset($_GET['fechaFin']) ? $_GET['fechaFin'] : null;
    $vendedorId = isset($_GET['vendedorId']) ? $_GET['vendedorId'] : null;
    
    $sql = "SELECT v.id, CONVERT(VARCHAR, v.fecha, 103) as fecha, 
            ve.nombre as vendedor, t.nombre as turno, 
            d.nombre as destino, m.nombre as motorizado,
            v.monto_usd, v.monto_bs, v.observacion
            FROM venta v
            JOIN vendedor ve ON v.id_vendedor = ve.id
            JOIN turno t ON v.id_turno = t.id
            JOIN destino d ON v.id_destino = d.id
            JOIN motorizado m ON v.id_motorizado = m.id
            WHERE 1=1";
    
    $params = array();
    
    if ($fechaInicio) {
        $sql .= " AND v.fecha >= ?";
        $params[] = $fechaInicio;
    }
    
    if ($fechaFin) {
        $sql .= " AND v.fecha <= ?";
        $params[] = $fechaFin;
    }
    
    if ($vendedorId) {
        $sql .= " AND v.id_vendedor = ?";
        $params[] = $vendedorId;
    }
    
    $sql .= " ORDER BY v.fecha DESC, v.id DESC";
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    $ventas = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $ventas[] = $row;
    }
    
    return json_encode($ventas);
}

// Guardar nueva venta
function guardarVenta() {
    global $conn;
    
    $fecha = $_POST['fecha'];
    $id_vendedor = $_POST['id_vendedor'];
    $id_turno = $_POST['id_turno'];
    $id_destino = $_POST['id_destino'];
    $id_motorizado = $_POST['id_motorizado'];
    $monto_usd = $_POST['monto_usd'];
    $monto_bs = $_POST['monto_bs'];
    $observacion = $_POST['observacion'];
    
    $sql = "INSERT INTO venta (fecha, id_vendedor, id_turno, id_destino, id_motorizado, monto_usd, monto_bs, observacion)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    
    $params = array($fecha, $id_vendedor, $id_turno, $id_destino, $id_motorizado, $monto_usd, $monto_bs, $observacion);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// Obtener venta por ID
function getVentaById() {
    global $conn;
    
    $id = $_POST['id'];
    
    $sql = "SELECT v.*, tc.tasa 
            FROM venta v
            LEFT JOIN tasa_cambio tc ON CONVERT(date, v.fecha) = tc.fecha
            WHERE v.id = ?";
    
    $stmt = sqlsrv_query($conn, $sql, array($id));
    
    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Formatear fecha para input date
        $fecha = date('Y-m-d', strtotime($row['fecha']));
        $row['fecha'] = $fecha;
        
        // Si no hay tasa registrada para esa fecha, usar la última tasa disponible
        if (!$row['tasa']) {
            $sqlTasa = "SELECT TOP 1 tasa FROM tasa_cambio ORDER BY fecha DESC";
            $stmtTasa = sqlsrv_query($conn, $sqlTasa);
            if ($stmtTasa && $rowTasa = sqlsrv_fetch_array($stmtTasa, SQLSRV_FETCH_ASSOC)) {
                $row['tasa'] = $rowTasa['tasa'];
            }
        }
        
        return json_encode($row);
    }
    
    return json_encode(null);
}

// Actualizar venta
function actualizarVenta() {
    global $conn;
    
    $id = $_POST['id'];
    $fecha = $_POST['fecha'];
    $id_vendedor = $_POST['id_vendedor'];
    $id_turno = $_POST['id_turno'];
    $id_destino = $_POST['id_destino'];
    $id_motorizado = $_POST['id_motorizado'];
    $monto_usd = $_POST['monto_usd'];
    $monto_bs = $_POST['monto_bs'];
    $observacion = $_POST['observacion'];
    
    $sql = "UPDATE venta SET 
            fecha = ?,
            id_vendedor = ?,
            id_turno = ?,
            id_destino = ?,
            id_motorizado = ?,
            monto_usd = ?,
            monto_bs = ?,
            observacion = ?
            WHERE id = ?";
    
    $params = array($fecha, $id_vendedor, $id_turno, $id_destino, $id_motorizado, $monto_usd, $monto_bs, $observacion, $id);
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// Eliminar venta
function eliminarVenta() {
    global $conn;
    
    $id = $_POST['id'];
    
    $sql = "DELETE FROM venta WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, array($id));
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

//// ------  maestros ---- 
// Obtener listado de maestros

function getMaestros() {
    global $conn;
    
    // Limpiar cualquier salida previa
    if (ob_get_length()) ob_clean();
    
    // Establecer encabezado JSON
    header('Content-Type: application/json');
    
    try {
        $tabla = $_POST['tabla'] ?? '';
        $allowedTables = ['vendedor', 'motorizado', 'turno', 'destino'];
        
        if (!in_array($tabla, $allowedTables)) {
            throw new Exception('Tabla no permitida');
        }
        
        // Configurar consulta según tabla
        switch($tabla) {
            case 'vendedor':
            case 'motorizado':
                $sql = "SELECT id, nombre, activo FROM $tabla";
                break;
            case 'turno':
                $sql = "SELECT id, nombre, descripcion FROM $tabla";
                break;
            case 'destino':
                $sql = "SELECT id, nombre FROM $tabla"; // Nota el alias aquí
                break;
        }
        
        $sql .= " ORDER BY nombre";
        
        $stmt = sqlsrv_query($conn, $sql);
        
        if ($stmt === false) {
            throw new Exception('Error SQL: ' . print_r(sqlsrv_errors(), true));
        }
        
        $data = [];
        while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
            // Convertir activo a booleano si existe
            if (isset($row['activo'])) {
                $row['activo'] = (bool)$row['activo'];
            }
            $data[] = $row;
        }
        
        // Si no hay datos, devolver array vacío
        echo json_encode($data ?: []);
        exit;
        
    } catch (Exception $e) {
        // En caso de error, devolver estructura de error estándar
        http_response_code(500);
        echo json_encode([
            'error' => true,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Guardar nuevo registro maestro
function guardarMaestro() {
    global $conn;
    
    $tabla = $_POST['tabla'];
    $nombre = $_POST['nombre'];
    
    switch($tabla) {
        case 'vendedor':
        case 'motorizado':
            $activo = isset($_POST['activo']) ? 1 : 0;
            $sql = "INSERT INTO $tabla (nombre, activo) VALUES (?, ?)";
            $params = array($nombre, $activo);
            break;
        case 'turno':
            $descripcion = $_POST['descripcion'];
            $sql = "INSERT INTO $tabla (nombre, descripcion) VALUES (?, ?)";
            $params = array($nombre, $descripcion);
            break;
        case 'destino':
            $sql = "INSERT INTO $tabla (nombre) VALUES (?)";
            $params = array($nombre);
            break;
        default:
            return json_encode(array('success' => false, 'message' => 'Tabla no válida'));
    }
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// Obtener maestro por ID
function getMaestroById() {
    global $conn;
    
    $tabla = $_POST['tabla'];
    $id = $_POST['id'];
    
    switch($tabla) {
        case 'vendedor':
        case 'motorizado':
            $sql = "SELECT id, nombre, activo FROM $tabla WHERE id = ?";
            break;
        case 'turno':
            $sql = "SELECT id, nombre, descripcion FROM $tabla WHERE id = ?";
            break;
        case 'destino':
            $sql = "SELECT id, nombre FROM $tabla WHERE id = ?";
            break;
        default:
            return json_encode(null);
    }
    
    $stmt = sqlsrv_query($conn, $sql, array($id));
    
    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        return json_encode($row);
    }
    
    return json_encode(null);
}

// Actualizar registro maestro
function actualizarMaestro() {
    global $conn;
    
    $tabla = $_POST['tabla'];
    $id = $_POST['id'];
    $nombre = $_POST['nombre'];
    
    switch($tabla) {
        case 'vendedor':
        case 'motorizado':
            $activo = isset($_POST['activo']) ? 1 : 0;
            $sql = "UPDATE $tabla SET nombre = ?, activo = ? WHERE id = ?";
            $params = array($nombre, $activo, $id);
            break;
        case 'turno':
            $descripcion = $_POST['descripcion'];
            $sql = "UPDATE $tabla SET nombre = ?, descripcion = ? WHERE id = ?";
            $params = array($nombre, $descripcion, $id);
            break;
        case 'destino':
            $sql = "UPDATE $tabla SET nombre = ? WHERE id = ?";
            $params = array($nombre, $id);
            break;
        default:
            return json_encode(array('success' => false, 'message' => 'Tabla no válida'));
    }
    
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// Eliminar registro maestro
function eliminarMaestro() {
    global $conn;
    
    $tabla = $_POST['tabla'];
    $id = $_POST['id'];
    
    // Verificar si el registro está siendo usado antes de eliminar
    if ($tabla === 'vendedor' || $tabla === 'motorizado' || $tabla === 'turno' || $tabla === 'destino') {
        $sqlCheck = "SELECT COUNT(*) as total FROM venta WHERE id_$tabla = ?";
        $stmtCheck = sqlsrv_query($conn, $sqlCheck, array($id));
        
        if ($stmtCheck && $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
            if ($row['total'] > 0) {
                return json_encode(array(
                    'success' => false,
                    'message' => 'No se puede eliminar porque hay ventas asociadas a este registro'
                ));
            }
        }
    }
    
    $sql = "DELETE FROM $tabla WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, array($id));
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// FIN MAESTRO

/// METAS ///

// Obtener listado de metas con ventas actuales
function getMetas() {
    global $conn;
    
    $anio = isset($_POST['anio']) ? $_POST['anio'] : date('Y');
    
    $sql = "SELECT m.id, m.anio, m.mes, m.monto,
            (SELECT ISNULL(SUM(v.monto_usd), 0) 
             FROM venta v 
             WHERE YEAR(v.fecha) = m.anio AND MONTH(v.fecha) = m.mes) as ventas_actuales
            FROM meta_mes m
            WHERE m.anio = ?
            ORDER BY m.anio DESC, m.mes DESC";
    
    $stmt = sqlsrv_query($conn, $sql, array($anio));
    
    $metas = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $metas[] = $row;
    }
    
    // Asegurarse de que todos los meses existan para el año seleccionado
    $metasCompletas = array();
    for ($mes = 1; $mes <= 12; $mes++) {
        $encontrado = false;
        foreach ($metas as $meta) {
            if ($meta['mes'] == $mes) {
                $metasCompletas[] = $meta;
                $encontrado = true;
                break;
            }
        }
        
        if (!$encontrado) {
            $metasCompletas[] = array(
                'id' => null,
                'anio' => $anio,
                'mes' => $mes,
                'monto' => 0,
                'ventas_actuales' => 0
            );
        }
    }
    
    return json_encode($metasCompletas);
}

// Guardar nueva meta
function guardarMeta() {
    global $conn;
    
    $anio = $_POST['anio'];
    $mes = $_POST['mes'];
    $monto = $_POST['monto'];
    
    // Verificar si ya existe una meta para este año/mes
    $sqlCheck = "SELECT COUNT(*) as total FROM meta_mes WHERE anio = ? AND mes = ?";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, array($anio, $mes));
    
    if ($stmtCheck && $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
        if ($row['total'] > 0) {
            return json_encode(array(
                'success' => false,
                'message' => 'Ya existe una meta definida para este año y mes'
            ));
        }
    }
    
    $sql = "INSERT INTO meta_mes (anio, mes, monto) VALUES (?, ?, ?)";
    $stmt = sqlsrv_query($conn, $sql, array($anio, $mes, $monto));
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// Obtener meta por ID
function getMetaById() {
    global $conn;
    
    $id = $_POST['id'];
    
    $sql = "SELECT id, anio, mes, monto FROM meta_mes WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, array($id));
    
    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        return json_encode($row);
    }
    
    return json_encode(null);
}

// Actualizar meta
function actualizarMeta() {
    global $conn;
    
    $id = $_POST['id'];
    $anio = $_POST['anio'];
    $mes = $_POST['mes'];
    $monto = $_POST['monto'];
    
    // Verificar si ya existe otra meta para este año/mes
    $sqlCheck = "SELECT COUNT(*) as total FROM meta_mes WHERE anio = ? AND mes = ? AND id != ?";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, array($anio, $mes, $id));
    
    if ($stmtCheck && $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
        if ($row['total'] > 0) {
            return json_encode(array(
                'success' => false,
                'message' => 'Ya existe otra meta definida para este año y mes'
            ));
        }
    }
    
    $sql = "UPDATE meta_mes SET anio = ?, mes = ?, monto = ? WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, array($anio, $mes, $monto, $id));
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

// Eliminar meta
function eliminarMeta() {
    global $conn;
    
    $id = $_POST['id'];
    
    $sql = "DELETE FROM meta_mes WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, array($id));
    
    if ($stmt) {
        return json_encode(array('success' => true));
    } else {
        return json_encode(array('success' => false, 'message' => print_r(sqlsrv_errors(), true)));
    }
}

?>
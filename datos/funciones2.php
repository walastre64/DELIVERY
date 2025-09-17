<?php
header('Content-Type: application/json');
require_once '../conexion/config.php';

// Permitir errores para depuración (quitar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 0);


// Limpiar buffer de salida
if (ob_get_length()) ob_clean();

// Establecer cabecera JSON
header('Content-Type: application/json');

// Determinar acción
$action = $_POST['action'] ?? '';

switch ($action) {
    case 'getMaestros':
        getMaestros($conn);
        break;
    case 'guardarMaestro':
        guardarMaestro($conn);
        break;
    case 'getMaestroById':
        getMaestroById($conn);
        break;
    case 'actualizarMaestro':
        actualizarMaestro($conn);
        break;
    case 'eliminarMaestro':
        eliminarMaestro($conn);
        break;
    default:
        echo json_encode(['error' => true, 'message' => 'Acción no válida']);
}

// ---------- FUNCIONES ----------

function getMaestros($conn) {
    $tabla = $_POST['tabla'] ?? '';
    $allowed = ['vendedor', 'motorizado', 'turno', 'destino'];

    if (!in_array($tabla, $allowed)) {
        echo json_encode(['error' => true, 'message' => 'Tabla no permitida']);
        exit;
    }

    switch ($tabla) {
        case 'vendedor':
        case 'motorizado':
            $sql = "SELECT id, nombre, activo FROM $tabla ORDER BY nombre";
            break;
        case 'turno':
            $sql = "SELECT id, nombre, descripcion FROM $tabla ORDER BY nombre";
            break;
        case 'destino':
            $sql = "SELECT id, nombre FROM $tabla ORDER BY nombre";
            break;
    }

    $stmt = sqlsrv_query($conn, $sql);

    if ($stmt === false) {
        echo json_encode(['error' => true, 'message' => 'Error SQL: ' . print_r(sqlsrv_errors(), true)]);
        exit;
    }

    $data = [];
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (isset($row['activo'])) {
            $row['activo'] = (bool)$row['activo'];
        }
        $data[] = $row;
    }

    echo json_encode($data);
    exit;
}

function guardarMaestro($conn) {
    $tabla = $_POST['tabla'] ?? '';
    $nombre = trim($_POST['nombre'] ?? '');

    if (empty($nombre)) {
        echo json_encode(['success' => false, 'message' => 'El nombre es obligatorio']);
        exit;
    }

    switch ($tabla) {
        case 'vendedor':
        case 'motorizado':
            $activo = isset($_POST['activo']) ? 1 : 0;
            $sql = "INSERT INTO $tabla (nombre, activo) VALUES (?, ?)";
            $params = [$nombre, $activo];
            break;
        case 'turno':
            $descripcion = trim($_POST['descripcion'] ?? '');
            $sql = "INSERT INTO $tabla (nombre, descripcion) VALUES (?, ?)";
            $params = [$nombre, $descripcion];
            break;
        case 'destino':
            $sql = "INSERT INTO $tabla (nombre) VALUES (?)";
            $params = [$nombre];
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
            exit;
    }

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar']);
    }
    exit;
}

function getMaestroById($conn) {
    $tabla = $_POST['tabla'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    $allowed = ['vendedor', 'motorizado', 'turno', 'destino'];
    if (!in_array($tabla, $allowed) || $id <= 0) {
        echo json_encode(null);
        exit;
    }

    switch ($tabla) {
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
    }

    $stmt = sqlsrv_query($conn, $sql, [$id]);

    if ($stmt && $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        if (isset($row['activo'])) {
            $row['activo'] = (bool)$row['activo'];
        }
        echo json_encode($row);
    } else {
        echo json_encode(null);
    }
    exit;
}

function actualizarMaestro($conn) {
    $tabla = $_POST['tabla'] ?? '';
    $id = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');

    if (empty($nombre) || $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }

    switch ($tabla) {
        case 'vendedor':
        case 'motorizado':
            $activo = isset($_POST['activo']) ? 1 : 0;
            $sql = "UPDATE $tabla SET nombre = ?, activo = ? WHERE id = ?";
            $params = [$nombre, $activo, $id];
            break;
        case 'turno':
            $descripcion = trim($_POST['descripcion'] ?? '');
            $sql = "UPDATE $tabla SET nombre = ?, descripcion = ? WHERE id = ?";
            $params = [$nombre, $descripcion, $id];
            break;
        case 'destino':
            $sql = "UPDATE $tabla SET nombre = ? WHERE id = ?";
            $params = [$nombre, $id];
            break;
        default:
            echo json_encode(['success' => false, 'message' => 'Tabla no válida']);
            exit;
    }

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al actualizar']);
    }
    exit;
}

function eliminarMaestro($conn) {
    $tabla = $_POST['tabla'] ?? '';
    $id = (int)($_POST['id'] ?? 0);

    $allowed = ['vendedor', 'motorizado', 'turno', 'destino'];
    if (!in_array($tabla, $allowed) || $id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
        exit;
    }

    // Verificar si está en uso
    $sqlCheck = "SELECT COUNT(*) AS total FROM venta WHERE id_$tabla = ?";
    $stmtCheck = sqlsrv_query($conn, $sqlCheck, [$id]);

    if ($stmtCheck && $row = sqlsrv_fetch_array($stmtCheck, SQLSRV_FETCH_ASSOC)) {
        if ($row['total'] > 0) {
            echo json_encode(['success' => false, 'message' => 'No se puede eliminar porque hay ventas asociadas']);
            exit;
        }
    }

    $sql = "DELETE FROM $tabla WHERE id = ?";
    $stmt = sqlsrv_query($conn, $sql, [$id]);

    if ($stmt) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al eliminar']);
    }
    exit;
}
?>
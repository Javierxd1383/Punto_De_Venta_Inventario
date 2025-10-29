<?php
// guardar_venta.php (versión que permite id_cliente NULL si no existe)
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    http_response_code(401);
    echo json_encode(['ok' => false, 'message' => 'No autorizado. Inicia sesión como empleado.']);
    exit;
}

include 'conexion.php'; // aporta $conn (procedural mysqli)

// Leer JSON entrante
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'Payload inválido']);
    exit;
}

// Validaciones básicas
$items = $data['items'] ?? [];
$total = floatval($data['total'] ?? 0);
$metodo = isset($data['metodo_pago']) ? $data['metodo_pago'] : 'efectivo';
$efectivo_recibido = floatval($data['efectivo_recibido'] ?? 0);
$monto_tarjeta = floatval($data['monto_tarjeta'] ?? 0);

// Normalizar método
$metodo_lc = strtolower($metodo);
if ($metodo_lc === 'efectivo') $metodo = 'Efectivo';
elseif ($metodo_lc === 'tarjeta') $metodo = 'Tarjeta';
else $metodo = ucfirst($metodo_lc);

if (empty($items) || $total <= 0) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'No hay items o total inválido']);
    exit;
}

// ---------- Validar id_empleado ----------
$id_empleado_session = isset($_SESSION['id_empleado']) ? intval($_SESSION['id_empleado']) : 0;
$id_empleado = 0;
$empleado_valido = false;

if ($id_empleado_session > 0) {
    $sql_check = "SELECT id_empleado FROM empleados WHERE id_empleado = ?";
    $stmt_check = mysqli_prepare($conn, $sql_check);
    if ($stmt_check) {
        mysqli_stmt_bind_param($stmt_check, "i", $id_empleado_session);
        mysqli_stmt_execute($stmt_check);
        mysqli_stmt_store_result($stmt_check);
        if (mysqli_stmt_num_rows($stmt_check) > 0) {
            $empleado_valido = true;
            $id_empleado = $id_empleado_session;
        }
        mysqli_stmt_close($stmt_check);
    }
}

// Fallback: usar primer empleado si no hay id en sesión
if (!$empleado_valido) {
    $sql_any = "SELECT id_empleado FROM empleados LIMIT 1";
    $res_any = mysqli_query($conn, $sql_any);
    if ($res_any && mysqli_num_rows($res_any) > 0) {
        $row_any = mysqli_fetch_assoc($res_any);
        $id_empleado = intval($row_any['id_empleado']);
        $empleado_valido = true;
    }
}

if (!$empleado_valido) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'message' => 'No existe ningún empleado registrado en la base de datos. Agrega un empleado o inicia sesión.']);
    exit;
}
// ---------- fin validar empleado ----------

// ---------- Validar id_cliente (puede ser NULL) ----------
$id_cliente_valid = null; // por defecto NULL

if (isset($_SESSION['id_cliente']) && intval($_SESSION['id_cliente']) > 0) {
    $id_cliente_sess = intval($_SESSION['id_cliente']);
    $sql_cliente = "SELECT id_cliente FROM clientes WHERE id_cliente = ?";
    $stmt_cliente = mysqli_prepare($conn, $sql_cliente);
    if ($stmt_cliente) {
        mysqli_stmt_bind_param($stmt_cliente, "i", $id_cliente_sess);
        mysqli_stmt_execute($stmt_cliente);
        mysqli_stmt_store_result($stmt_cliente);
        if (mysqli_stmt_num_rows($stmt_cliente) > 0) {
            $id_cliente_valid = $id_cliente_sess; // existe y es válido
        }
        mysqli_stmt_close($stmt_cliente);
    }
}
// Si no hay cliente en sesión o no existe, dejamos id_cliente_valid = null
// ---------- fin validar cliente ----------

// Iniciar transacción
if (!mysqli_begin_transaction($conn)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'No se pudo iniciar transacción']);
    exit;
}

try {
    // Insertar en ventas
    $estatus = 'Completada';

    if ($id_cliente_valid === null) {
        // Si id_cliente es NULL, usamos SQL que inserta NULL literal
        $sql_venta = "INSERT INTO ventas (fecha, total, metodo_pago, estatus, id_empleado, id_cliente) VALUES (NOW(), ?, ?, ?, ?, NULL)";
        $stmt = mysqli_prepare($conn, $sql_venta);
        if (!$stmt) throw new Exception("Prepare ventas (NULL cliente): " . mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "dssi", $total, $metodo, $estatus, $id_empleado);
    } else {
        // Si tenemos id_cliente válido, lo incluimos
        $sql_venta = "INSERT INTO ventas (fecha, total, metodo_pago, estatus, id_empleado, id_cliente) VALUES (NOW(), ?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql_venta);
        if (!$stmt) throw new Exception("Prepare ventas (con cliente): " . mysqli_error($conn));
        mysqli_stmt_bind_param($stmt, "dssii", $total, $metodo, $estatus, $id_empleado, $id_cliente_valid);
    }

    if (!mysqli_stmt_execute($stmt)) {
        $err = mysqli_stmt_error($stmt);
        mysqli_stmt_close($stmt);
        throw new Exception("Execute ventas: " . $err);
    }
    $id_venta = mysqli_insert_id($conn);
    mysqli_stmt_close($stmt);

    // Preparar inserción en detalles_ventas
    $sql_det = "INSERT INTO detalles_ventas (id_venta, id_producto, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
    $stmt_det = mysqli_prepare($conn, $sql_det);
    if (!$stmt_det) throw new Exception("Prepare detalles_ventas: " . mysqli_error($conn));

    foreach ($items as $it) {
        $id_producto = intval($it['id_producto'] ?? 0);
        $cantidad = intval($it['cantidad'] ?? 0);
        $precio_unitario = isset($it['precio_unitario']) ? floatval($it['precio_unitario']) : floatval($it['precio'] ?? 0);

        if ($id_producto <= 0 || $cantidad <= 0) {
            throw new Exception("Item inválido (id_producto o cantidad incorrectos).");
        }

        mysqli_stmt_bind_param($stmt_det, "iiid", $id_venta, $id_producto, $cantidad, $precio_unitario);
        if (!mysqli_stmt_execute($stmt_det)) {
            $err = mysqli_stmt_error($stmt_det);
            throw new Exception("Execute detalle: " . $err);
        }
    }
    mysqli_stmt_close($stmt_det);

    // Commit
    if (!mysqli_commit($conn)) {
        throw new Exception("Commit fallido: " . mysqli_error($conn));
    }

    echo json_encode(['ok' => true, 'id_venta' => $id_venta, 'message' => 'Venta guardada correctamente']);
    exit;
} catch (Exception $ex) {
    mysqli_rollback($conn);
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Error guardando la venta: ' . $ex->getMessage()]);
    exit;
} finally {
    mysqli_close($conn);
}

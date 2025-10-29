<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirige al login si no es administrador
    exit();
}

// Inicializar variables para evitar errores
$success = null;
$error = null;
$ventas = []; // Inicializamos como un array vacío
$clientes = [];
$empleados = [];

// Manejo de operaciones (Agregar, Eliminar) con CONSULTAS PREPARADAS
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? '';

    if ($accion === 'agregar') {
        $id_cliente = $_POST['id_cliente'] ?? null;
        $id_empleado = $_POST['id_empleado'] ?? null;
        $metodo_pago = $_POST['metodo_pago'] ?? '';
        $total = $_POST['total'] ?? 0;

        // Validar que los campos no estén vacíos
        if ($id_cliente && $id_empleado && $metodo_pago && $total > 0) {
            
            // *** MEJORA DE SEGURIDAD (CONSULTA PREPARADA) ***
            $query = "INSERT INTO ventas (id_cliente, id_empleado, metodo_pago, total, fecha) 
                      VALUES (?, ?, ?, ?, NOW())";
            
            // Preparamos la consulta
            $stmt = mysqli_prepare($conn, $query);
            
            // 'iisd' significa: (i)nteger, (i)nteger, (s)tring, (d)ouble
            // Vinculamos las variables a los marcadores de posición (?)
            mysqli_stmt_bind_param($stmt, 'iisd', $id_cliente, $id_empleado, $metodo_pago, $total);
            
            // Ejecutamos la consulta
            if (mysqli_stmt_execute($stmt)) {
                $success = "Venta agregada correctamente.";
            } else {
                $error = "Error al agregar venta: " . mysqli_stmt_error($stmt);
            }
            // Cerramos la sentencia
            mysqli_stmt_close($stmt);

        } else {
            $error = "Todos los campos son obligatorios para agregar una venta.";
        }

    } elseif ($accion === 'eliminar') {
        $id_venta = $_POST['id_venta'] ?? null;

        if ($id_venta) {

            // *** MEJORA DE SEGURIDAD (CONSULTA PREPARADA) ***
            $query = "DELETE FROM ventas WHERE id_venta = ?";
            
            $stmt = mysqli_prepare($conn, $query);
            
            // 'i' significa: (i)nteger
            mysqli_stmt_bind_param($stmt, 'i', $id_venta);
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Venta eliminada correctamente.";
            } else {
                $error = "Error al eliminar venta: " . mysqli_stmt_error($stmt);
            }
            mysqli_stmt_close($stmt);

        } else {
            $error = "ID de venta inválido.";
        }
    }
}

// --- OBTENCIÓN DE DATOS PARA MOSTRAR ---

// Obtener todas las ventas (Esta consulta es segura, no usa variables del usuario)
$query_ventas = "SELECT v.id_venta, v.fecha, v.total, v.metodo_pago, 
                        c.nombre AS cliente, e.nombre AS empleado 
                 FROM ventas v
                 JOIN clientes c ON v.id_cliente = c.id_cliente
                 JOIN empleados e ON v.id_empleado = e.id_empleado
                 ORDER BY v.id_venta DESC"; // Ordenar para ver las más nuevas primero
$result_ventas = mysqli_query($conn, $query_ventas);

if ($result_ventas) {
    $ventas = mysqli_fetch_all($result_ventas, MYSQLI_ASSOC);
} else {
    $error = "Error al cargar las ventas: " . mysqli_error($conn);
}

// Obtener los clientes para el formulario modal
$query_clientes = "SELECT id_cliente, nombre FROM clientes";
$result_clientes = mysqli_query($conn, $query_clientes);
if ($result_clientes) {
    $clientes = mysqli_fetch_all($result_clientes, MYSQLI_ASSOC);
}

// Obtener los empleados para el formulario modal
$query_empleados = "SELECT id_empleado, nombre FROM empleados";
$result_empleados = mysqli_query($conn, $query_empleados);
if ($result_empleados) {
    $empleados = mysqli_fetch_all($result_empleados, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #a29bfe, #6c5ce7);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            color: #333;
        }
        .container {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            padding: 20px 30px;
            max-width: 1200px;
            width: 100%;
        }
        h1 {
            color: #6c5ce7;
            font-weight: bold;
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-primary {
            background-color: #6c5ce7;
            border: none;
            border-radius: 25px;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #5a4dcf;
        }
        .btn-secondary {
            border-radius: 25px;
            background-color: #dfe6e9;
            color: #333;
            border: none;
            transition: all 0.3s ease;
        }
        .btn-secondary:hover {
            background-color: #b2bec3;
        }
        .btn-danger {
            border-radius: 25px;
        }
        .table {
            margin-top: 20px;
            border-radius: 10px;
            overflow: hidden;
        }
        .table th {
            background-color: #6c5ce7;
            color: white;
        }
        .table tbody tr:hover {
            background-color: #f4f4f4;
        }
        .modal-header {
            background: #6c5ce7;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestión de Ventas</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="administrador.php" class="btn btn-secondary">Volver al Menú Anterior</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarVentaModal">Agregar Venta</button>
        </div>

        <table class="table table-hover table-bordered text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th>Cliente</th>
                    <th>Empleado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?php echo $venta['id_venta']; ?></td>
                        <td><?php echo $venta['fecha']; ?></td>
                        <td>$<?php echo number_format($venta['total'], 2); ?></td>
                        <td><?php echo $venta['metodo_pago']; ?></td>
                        <td><?php echo $venta['cliente']; ?></td>
                        <td><?php echo $venta['empleado']; ?></td>
                        <td>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="id_venta" value="<?php echo $venta['id_venta']; ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta venta?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="agregarVentaModal" tabindex="-1" aria-labelledby="agregarVentaModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarVentaModalLabel">Agregar Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agregar">
                        
                        <div class="mb-3">
                            <label for="id_cliente" class="form-label">Cliente</label>
                            <select class="form-select" name="id_cliente" required>
                                <option value="">Selecciona un cliente</option>
                                <?php foreach ($clientes as $cliente): ?>
                                    <option value="<?php echo $cliente['id_cliente']; ?>"><?php echo $cliente['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="id_empleado" class="form-label">Empleado</label>
                            <select class="form-select" name="id_empleado" required>
                                <option value="">Selecciona un empleado</option>
                                <?php foreach ($empleados as $empleado): ?>
                                    <option value="<?php echo $empleado['id_empleado']; ?>"><?php echo $empleado['nombre']; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="metodo_pago" class="form-label">Método de Pago</label>
                            <select class="form-select" name="metodo_pago" required>
                                <option value="Efectivo">Efectivo</option>
                                <option value="Tarjeta">Tarjeta</option>
                                <option value="Transferencia">Transferencia</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="total" class="form-label">Total</label>
                            <input type="number" class="form-control" name="total" step="0.01" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Agregar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
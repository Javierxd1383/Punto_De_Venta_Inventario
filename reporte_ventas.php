<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php"); // Redirige al login si no es administrador
    exit();
}

// Inicializar variables para mensajes y datos
$success = "";
$error = "";
$ventas = [];
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

// Obtener las ventas filtradas por fecha
if ($_SERVER["REQUEST_METHOD"] == "POST" && $fecha_inicio && $fecha_fin) {
    
    try {
        // *** INICIO DE LA CORRECCIÓN ***
        
        // 1. Convertimos la fecha fin (ej. '2025-10-30') en un objeto DateTime
        $fecha_fin_dt = new DateTime($fecha_fin);
        
        // 2. Le sumamos 1 día para obtener '2025-10-31'
        $fecha_fin_dt->modify('+1 day');
        
        // 3. La formateamos de nuevo a string
        $fecha_fin_siguiente = $fecha_fin_dt->format('Y-m-d');

        // 4. Preparamos la nueva consulta (nota el WHERE)
        // La lógica ahora es: v.fecha >= '2025-10-28' Y v.fecha < '2025-10-31'
        $query = "SELECT v.id_venta, v.fecha, v.total, v.metodo_pago, 
                         c.nombre AS cliente, e.nombre AS empleado 
                  FROM ventas v
                  JOIN clientes c ON v.id_cliente = c.id_cliente
                  JOIN empleados e ON v.id_empleado = e.id_empleado
                  WHERE v.fecha >= ? AND v.fecha < ?
                  ORDER BY v.fecha ASC";
        
        $stmt = mysqli_prepare($conn, $query);
        
        // 5. Vinculamos las variables correctas: $fecha_inicio y $fecha_fin_siguiente
        mysqli_stmt_bind_param($stmt, 'ss', $fecha_inicio, $fecha_fin_siguiente);
        
        // *** FIN DE LA CORRECCIÓN ***

        // 6. Ejecutamos la consulta
        mysqli_stmt_execute($stmt);
        
        $result = mysqli_stmt_get_result($stmt);
        $ventas = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

        if (empty($ventas)) {
            $error = "No se encontraron ventas en el rango de fechas seleccionado.";
        }
        
        mysqli_stmt_close($stmt);

    } catch (Exception $e) {
        // Captura cualquier error al manipular las fechas
        $error = "Error al procesar las fechas: " . $e->getMessage();
    }

} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    $error = "Por favor selecciona un rango de fechas válido.";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
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
        h1, h3 {
            color: #6c5ce7;
            font-weight: bold;
            text-align: center;
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
        .btn-success {
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
        .alert {
            border-radius: 10px;
        }
         /* Estilos para impresión */
        @media print {
            body {
                background: none;
                padding: 0;
            }
            .container {
                box-shadow: none;
                max-width: 100%;
                padding: 0;
            }
            .btn, .form-label, form {
                display: none; /* Oculta botones y formulario al imprimir */
            }
             .d-flex.justify-content-end.mb-4 {
                 display: none !important;
             }
            h1, h3 {
                color: #000 !important;
                text-align: center;
                margin-bottom: 20px;
            }
            .table {
                margin-top: 0;
            }
            .table th {
                background-color: #eee !important;
                color: #000 !important;
                -webkit-print-color-adjust: exact; /* Fuerza la impresión de fondos en Chrome */
                color-adjust: exact;
            }
            .alert {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reporte de Ventas</h1>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-end mb-4">
            <a href="administrador.php" class="btn btn-secondary">Volver al Menú Anterior</a>
        </div>

        <form method="POST" class="row g-3">
            <div class="col-md-5">
                <label for="fecha_inicio" class="form-label">Fecha Inicio</label>
                <input type="date" class="form-control" name="fecha_inicio" id="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
            </div>
            <div class="col-md-5">
                <label for="fecha_fin" class="form-label">Fecha Fin</label>
                <input type="date" class="form-control" name="fecha_fin" id="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">Generar Reporte</button>
            </div>
        </form>

        <?php if (!empty($ventas)): ?>
            <div class="mt-5">
                <h3>Ventas del <?php echo htmlspecialchars($fecha_inicio); ?> al <?php echo htmlspecialchars($fecha_fin); ?></h3>
                <table class="table table-hover table-bordered text-center">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha</th>
                            <th>Total</th>
                            <th>Método de Pago</th>
                            <th>Cliente</th>
                            <th>Empleado</th>
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
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-success" onclick="window.print()">Imprimir Reporte</Gbutton>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
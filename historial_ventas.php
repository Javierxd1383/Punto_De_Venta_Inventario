<?php
session_start();
include 'conexion.php'; // Asegúrate que define $conn

// Verificar si el usuario tiene el rol de empleado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header('Location: login.php'); // Redirige al login si no es empleado
    exit();
}

// Obtener las ventas de la base de datos
$query_ventas = "
    SELECT 
        v.id_venta, 
        v.fecha, 
        v.total, 
        v.metodo_pago, 
        v.estatus, 
        e.nombre AS empleado, 
        c.nombre AS cliente 
    FROM ventas v
    LEFT JOIN empleados e ON v.id_empleado = e.id_empleado
    LEFT JOIN clientes c ON v.id_cliente = c.id_cliente
    ORDER BY v.fecha DESC
";

$result_ventas = mysqli_query($conn, $query_ventas);

// Manejo de errores de la consulta
if ($result_ventas === false) {
    die('Error en la consulta SQL: ' . mysqli_error($conn));
}

$ventas = mysqli_fetch_all($result_ventas, MYSQLI_ASSOC);

mysqli_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Ventas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #8e44ad, #3498db);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            color: #fff;
        }
        .container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 1200px;
            margin: auto;
            color: #333;
        }
        .table th, .table td {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Historial de Ventas</h1>

        <div class="mb-3 d-flex justify-content-between align-items-center">
            <a href="loginempleado.php" class="btn btn-secondary">Regresar al Menú</a>
            <button class="btn btn-info" onclick="window.location.reload();">Actualizar Historial 🔄</button>
        </div>

        <div class="mb-4">
            <input type="text" id="filtroVentas" class="form-control" placeholder="Buscar por ID de Venta, Empleado, Cliente o Método de Pago..." oninput="filtrarVentas()">
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>ID Venta</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Método de Pago</th>
                    <th>Estatus</th>
                    <th>Empleado</th>
                    <th>Cliente</th>
                </tr>
            </thead>
            <tbody id="cuerpoTablaVentas">
                <?php if (count($ventas) > 0): ?>
                    <?php foreach ($ventas as $venta): ?>
                        <tr 
                            data-id="<?php echo htmlspecialchars($venta['id_venta']); ?>" 
                            data-empleado="<?php echo strtolower(htmlspecialchars($venta['empleado'] ?? '')); ?>"
                            data-cliente="<?php echo strtolower(htmlspecialchars($venta['cliente'] ?? '')); ?>"
                            data-metodo="<?php echo strtolower(htmlspecialchars($venta['metodo_pago'])); ?>"
                        >
                            <td><?php echo htmlspecialchars($venta['id_venta']); ?></td>
                            <td><?php echo htmlspecialchars($venta['fecha']); ?></td>
                            <td>$<?php echo number_format($venta['total'], 2); ?></td>
                            <td><?php echo htmlspecialchars($venta['metodo_pago']); ?></td>
                            <td><?php echo htmlspecialchars($venta['estatus']); ?></td>
                            <td><?php echo htmlspecialchars($venta['empleado'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($venta['cliente'] ?? 'Público General'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7">No se encontraron ventas registradas.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <script>
        function filtrarVentas() {
            const filtro = document.getElementById('filtroVentas').value.toLowerCase();
            const filas = document.querySelectorAll('#cuerpoTablaVentas tr');
            let algunaVisible = false;

            filas.forEach(fila => {
                if (fila.querySelector('td[colspan="7"]')) return; 

                const idVenta = fila.getAttribute('data-id') || '';
                const empleado = fila.getAttribute('data-empleado') || '';
                const cliente = fila.getAttribute('data-cliente') || '';
                const metodo = fila.getAttribute('data-metodo') || '';

                if (
                    idVenta.includes(filtro) ||
                    empleado.includes(filtro) ||
                    cliente.includes(filtro) ||
                    metodo.includes(filtro)
                ) {
                    fila.style.display = '';
                    algunaVisible = true;
                } else {
                    fila.style.display = 'none';
                }
            });

            let noResultadosFila = document.getElementById('no-resultados-fila');
            if (!noResultadosFila) {
                noResultadosFila = document.createElement('tr');
                noResultadosFila.innerHTML = '<td colspan="7">No se encontraron resultados para la búsqueda.</td>';
                noResultadosFila.id = 'no-resultados-fila';
                noResultadosFila.style.textAlign = 'center';
                document.getElementById('cuerpoTablaVentas').appendChild(noResultadosFila);
            }

            noResultadosFila.style.display = algunaVisible ? 'none' : '';
        }

        document.addEventListener('DOMContentLoaded', () => {
            const filasIniciales = document.querySelectorAll('#cuerpoTablaVentas tr');
            if (filasIniciales.length === 1 && filasIniciales[0].querySelector('td[colspan="7"]')) {
                // No hay ventas registradas inicialmente
            } else {
                let noResultadosFila = document.getElementById('no-resultados-fila');
                if (noResultadosFila) noResultadosFila.remove();
            }
        });
    </script>
</body>
</html>

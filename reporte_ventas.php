<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$ventas = [];
$fecha_inicio = $_POST['fecha_inicio'] ?? '';
$fecha_fin = $_POST['fecha_fin'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && $fecha_inicio && $fecha_fin) {
    $stmt = $conn->prepare("SELECT v.*, c.nombre as cliente, e.nombre as empleado FROM ventas v JOIN clientes c ON v.id_cliente=c.id_cliente JOIN empleados e ON v.id_empleado=e.id_empleado WHERE v.fecha BETWEEN ? AND ? ORDER BY v.fecha ASC");
    $fin_query = date('Y-m-d', strtotime($fecha_fin . ' +1 day'));
    $stmt->bind_param("ss", $fecha_inicio, $fin_query);
    $stmt->execute();
    $ventas = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 col-print-none fade-in">
    <div>
        <h2 class="fw-bold mb-1">Reporte Financiero</h2>
        <p class="text-muted">Genera informes detallados por período.</p>
    </div>
    <div>
        <button class="btn btn-light text-muted me-2" onclick="window.print()"><i class="fa fa-print me-2"></i>
            Imprimir</button>
    </div>
</div>

<div class="col-print-none mb-4 fade-in">
    <div class="card-premium p-4">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="text-muted small fw-bold mb-1">Desde</label>
                <input type="date" class="form-control form-control-lg bg-light border-0" name="fecha_inicio"
                    value="<?= $fecha_inicio ?>" required>
            </div>
            <div class="col-md-4">
                <label class="text-muted small fw-bold mb-1">Hasta</label>
                <input type="date" class="form-control form-control-lg bg-light border-0" name="fecha_fin"
                    value="<?= $fecha_fin ?>" required>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary-gradient w-100 py-2 h-100">
                    <i class="fa fa-filter me-2"></i> Generar
                </button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($ventas)): ?>
    <div class="card-premium fade-in">
        <div class="card-header-custom bg-white">
            <h5 class="fw-bold mb-0">Resultados del Período</h5>
        </div>
        <div class="table-responsive">
            <table class="table-premium align-middle">
                <thead>
                    <tr>
                        <th class="ps-4">Fecha/Hora</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Método</th>
                        <th class="text-end pe-4">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total_g = 0;
                    foreach ($ventas as $v):
                        $total_g += $v['total']; ?>
                        <tr>
                            <td class="ps-4 text-muted small"><?= date('d/m/Y H:i', strtotime($v['fecha'])) ?></td>
                            <td class="fw-bold text-dark"><?= htmlspecialchars($v['cliente']) ?></td>
                            <td class="text-muted"><?= htmlspecialchars($v['empleado']) ?></td>
                            <td><span class="badge bg-light text-dark"><?= $v['metodo_pago'] ?></span></td>
                            <td class="text-end pe-4 fw-bold">$<?= number_format($v['total'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <tr class="bg-light">
                        <td colspan="4" class="text-end pe-3 fw-bold text-uppercase text-muted">Total Generado</td>
                        <td class="text-end pe-4 fw-bold text-primary fs-5">$<?= number_format($total_g, 2) ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<style>
    @media print {
        .col-print-none {
            display: none !important;
        }

        .sidebar,
        .topbar {
            display: none !important;
        }

        .main-content {
            margin: 0 !important;
            padding: 0 !important;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>
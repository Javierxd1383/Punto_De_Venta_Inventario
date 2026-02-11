<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}
// Load data
$ventas = mysqli_fetch_all(mysqli_query($conn, "SELECT v.*, c.nombre as cliente, e.nombre as empleado FROM ventas v JOIN clientes c ON v.id_cliente=c.id_cliente JOIN empleados e ON v.id_empleado=e.id_empleado ORDER BY v.id_venta DESC"), MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Ventas Realizadas</h2>
        <p class="text-muted">Registro histórico de transacciones.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-light shadow-sm text-primary fw-bold">
            <i class="fa fa-download me-2"></i> Exportar
        </button>
        <button class="btn btn-primary-gradient" data-bs-toggle="modal" data-bs-target="#modalVenta">
            <i class="fa fa-plus me-2"></i> Nueva Venta
        </button>
    </div>
</div>

<div class="card-premium fade-in">
    <div class="table-responsive">
        <table class="table-premium align-middle">
            <thead>
                <tr>
                    <th class="ps-4">ID Venta</th>
                    <th>Cliente</th>
                    <th>Empleado</th>
                    <th>Fecha</th>
                    <th>Método</th>
                    <th>Total</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $v): ?>
                    <tr>
                        <td class="ps-4"><span
                                class="fw-bold text-muted">#<?= str_pad($v['id_venta'], 5, '0', STR_PAD_LEFT) ?></span></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                    style="width:30px; height:30px; font-size: 0.8rem;">
                                    <?= substr($v['cliente'], 0, 1) ?>
                                </div>
                                <span class="fw-bold small"><?= $v['cliente'] ?></span>
                            </div>
                        </td>
                        <td class="small text-muted"><?= $v['empleado'] ?></td>
                        <td class="small text-muted">
                            <i class="fa fa-calendar me-1"></i> <?= date('d/m/Y', strtotime($v['fecha'])) ?>
                        </td>
                        <td>
                            <?php if ($v['metodo_pago'] == 'Efectivo'): ?>
                                <span
                                    class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3">Efectivo</span>
                            <?php else: ?>
                                <span
                                    class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 px-3">Tarjeta</span>
                            <?php endif; ?>
                        </td>
                        <td class="fw-bold text-dark fs-6">$<?= number_format($v['total'], 2) ?></td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-icon text-muted hover-primary">
                                <i class="fa fa-eye"></i>
                            </button>
                            <button class="btn btn-sm btn-icon text-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
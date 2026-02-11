<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php");
    exit();
}
$id_empleado = $_SESSION['id_usuario'];
$ventas = mysqli_fetch_all(mysqli_query($conn, "SELECT v.*, c.nombre as cliente FROM ventas v JOIN clientes c ON v.id_cliente=c.id_cliente WHERE v.id_empleado = $id_empleado ORDER BY v.fecha DESC"), MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Historial de Ventas</h2>
        <p class="text-muted">Tus transacciones recientes.</p>
    </div>
    <a href="loginempleado.php" class="btn btn-light text-muted"><i class="fa fa-arrow-left me-2"></i> Volver</a>
</div>

<div class="card-premium fade-in">
    <div class="table-responsive">
        <table class="table-premium align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Fecha</th>
                    <th>Cliente</th>
                    <th>Método</th>
                    <th>Total</th>
                    <th class="text-end pe-4">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($ventas)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">No has realizado ventas aún.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($ventas as $v): ?>
                        <tr>
                            <td class="ps-4 small text-muted">
                                <i class="fa fa-clock me-1"></i> <?= date('d/m/Y H:i', strtotime($v['fecha'])) ?>
                            </td>
                            <td class="fw-bold"><?= htmlspecialchars($v['cliente']) ?></td>
                            <td><span class="badge bg-light text-dark border px-3"><?= $v['metodo_pago'] ?></span></td>
                            <td class="fw-bold text-dark">$<?= number_format($v['total'], 2) ?></td>
                            <td class="text-end pe-4"><span class="badge badge-success">Completada</span></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}

$productos = [];
$result = mysqli_query($conn, "SELECT p.nombre, p.descripcion, p.precio, c.nombre AS categoria FROM productos p JOIN categorias c ON p.categoria = c.id_categoria ORDER BY p.nombre ASC");
if ($result) {
    $productos = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Consulta de Inventario</h2>
        <p class="text-muted">Verifica precios y existencias en tiempo real.</p>
    </div>
    <a href="<?php echo $_SESSION['rol'] === 'administrador' ? 'administrador.php' : 'loginempleado.php'; ?>"
        class="btn btn-light text-muted"><i class="fa fa-arrow-left me-2"></i> Volver</a>
</div>

<div class="card-premium fade-in">
    <div class="p-4 border-bottom bg-light">
        <div class="position-relative" style="max-width: 400px;">
            <input type="text" id="buscador" class="form-control form-control-lg border-0 shadow-sm ps-5"
                placeholder="Filtrar por nombre...">
            <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table-premium align-middle">
            <thead>
                <tr>
                    <th class="ps-4">Producto</th>
                    <th>Categoría</th>
                    <th>Detalles</th>
                    <th class="text-end pe-4">Precio</th>
                </tr>
            </thead>
            <tbody id="tablaProductos">
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td class="ps-4 fw-bold text-dark"><?= htmlspecialchars($p['nombre']) ?></td>
                        <td><span class="badge badge-warning"><?= htmlspecialchars($p['categoria']) ?></span></td>
                        <td class="text-muted small"><?= htmlspecialchars($p['descripcion']) ?></td>
                        <td class="text-end pe-4 fw-bold text-primary fs-6">$<?= number_format($p['precio'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    document.getElementById('buscador').addEventListener('keyup', function () {
        let filtro = this.value.toLowerCase();
        let filas = document.querySelectorAll('#tablaProductos tr');
        filas.forEach(fila => {
            let texto = fila.innerText.toLowerCase();
            fila.style.display = texto.includes(filtro) ? '' : 'none';
        });
    });
</script>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
include 'conexion.php';

// Validar sesión
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

$success = null;
$error = null;

// Lógica de Backend
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'];
    if ($accion === 'agregar') {
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock']; // Note: Form sends 'stock', DB needs 'cantidad_stock'
        $categoria = $_POST['categoria'];
        $descripcion = $_POST['descripcion'];

        $query = "INSERT INTO productos (nombre, precio, cantidad_stock, categoria, descripcion) VALUES ('$nombre', '$precio', '$stock', '$categoria', '$descripcion')";
        if (mysqli_query($conn, $query)) {
            $success = "¡Dulce agregado con éxito!";
        } else {
            $error = "Error al agregar: " . mysqli_error($conn);
        }
    } elseif ($accion === 'editar') {
        $id = $_POST['id_producto'];
        $nombre = $_POST['nombre'];
        $precio = $_POST['precio'];
        $stock = $_POST['stock'];
        $categoria = $_POST['categoria'];
        $descripcion = $_POST['descripcion'];

        $query = "UPDATE productos SET nombre='$nombre', precio='$precio', cantidad_stock='$stock', categoria='$categoria', descripcion='$descripcion' WHERE id_producto=$id";
        if (mysqli_query($conn, $query)) {
            $success = "¡Dulce actualizado!";
        } else {
            $error = "Error al actualizar: " . mysqli_error($conn);
        }
    } elseif ($accion === 'eliminar') {
        $id = $_POST['id_producto'];
        if (mysqli_query($conn, "DELETE FROM productos WHERE id_producto=$id")) {
            $success = "Dulce eliminado";
        } else {
            $error = "Error al eliminar: " . mysqli_error($conn);
        }
    }
}

$productos = mysqli_fetch_all(mysqli_query($conn, "SELECT p.*, c.nombre as cat_nombre FROM productos p LEFT JOIN categorias c ON p.categoria = c.id_categoria"), MYSQLI_ASSOC);
$categorias = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM categorias"), MYSQLI_ASSOC);
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1 text-dark">Inventario</h2>
        <p class="text-muted">Gestiona el catálogo de Dulcería Candy.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalProducto">
        <i class="fa fa-plus me-2"></i> Nuevo Producto
    </button>
</div>

<?php if ($success): ?>
    <div class="alert alert-success border-0 bg-success bg-opacity-10 text-success fw-bold"><i
            class="fa fa-check me-2"></i><?= $success ?></div><?php endif; ?>
<?php if ($error): ?>
    <div class="alert alert-danger border-0 bg-danger bg-opacity-10 text-danger fw-bold"><i
            class="fa fa-times me-2"></i><?= $error ?></div><?php endif; ?>

<div class="card border-0 shadow-sm rounded-4 fade-in bg-white">
    <div class="table-responsive">
        <table class="table-premium w-100">
            <thead>
                <tr>
                    <th class="ps-4">Producto</th>
                    <th>Categoría</th>
                    <th>Precio</th>
                    <th>Existencia</th>
                    <th class="text-end pe-4">Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $p): ?>
                    <tr>
                        <td class="ps-4">
                            <h6 class="fw-bold text-dark mb-0"><?= htmlspecialchars($p['nombre']) ?></h6>
                            <small class="text-muted"><?= htmlspecialchars($p['descripcion']) ?></small>
                        </td>
                        <td><span
                                class="badge bg-light text-dark border rounded-pill px-3"><?= htmlspecialchars($p['cat_nombre'] ?? 'General') ?></span>
                        </td>
                        <td class="fw-bold text-dark">$<?= number_format($p['precio'], 2) ?></td>
                        <td>
                            <?php
                            // Using correct column name check
                            $stock = isset($p['cantidad_stock']) ? $p['cantidad_stock'] : (isset($p['stock']) ? $p['stock'] : 0);
                            if ($stock < 10):
                                ?>
                                <span class="text-danger fw-bold"><i class="fa fa-arrow-down me-1"></i> <?= $stock ?></span>
                            <?php else: ?>
                                <span class="text-success fw-bold"><?= $stock ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-end pe-4">
                            <button class="btn btn-sm btn-outline-primary rounded-circle" style="width:32px; height:32px;"
                                onclick='editar(<?= json_encode($p) ?>)' data-bs-toggle="modal"
                                data-bs-target="#modalEditar">
                                <i class="fa fa-pencil-alt small"></i>
                            </button>
                            <form method="POST" class="d-inline"
                                onsubmit="return confirm('¿Seguro que quieres borrar este artículo?');">
                                <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button class="btn btn-sm btn-outline-danger rounded-circle"
                                    style="width:32px; height:32px;"><i class="fa fa-trash small"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal Agregar -->
<div class="modal fade" id="modalProducto" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Agregar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST">
                    <input type="hidden" name="accion" value="agregar">
                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">Nombre</label>
                        <input type="text" class="form-control bg-light border-0 rounded-3" name="nombre" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted mb-1">Precio ($)</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0 rounded-3"
                                name="precio" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted mb-1">Stock</label>
                            <input type="number" class="form-control bg-light border-0 rounded-3" name="stock" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">Categoría</label>
                        <select class="form-select bg-light border-0 rounded-3" name="categoria" required>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id_categoria'] ?>"><?= $c['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small text-muted mb-1">Descripción</label>
                        <input type="text" class="form-control bg-light border-0 rounded-3" name="descripcion">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Guardar
                        Producto</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Editar -->
<div class="modal fade" id="modalEditar" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold">Editar Producto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-0">
                <form method="POST">
                    <input type="hidden" name="accion" value="editar">
                    <input type="hidden" name="id_producto" id="edit_id">
                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">Nombre</label>
                        <input type="text" class="form-control bg-light border-0 rounded-3" name="nombre"
                            id="edit_nombre" required>
                    </div>
                    <div class="row">
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted mb-1">Precio ($)</label>
                            <input type="number" step="0.01" class="form-control bg-light border-0 rounded-3"
                                name="precio" id="edit_precio" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="fw-bold small text-muted mb-1">Stock</label>
                            <input type="number" class="form-control bg-light border-0 rounded-3" name="stock"
                                id="edit_stock" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold small text-muted mb-1">Categoría</label>
                        <select class="form-select bg-light border-0 rounded-3" name="categoria" id="edit_categoria"
                            required>
                            <?php foreach ($categorias as $c): ?>
                                <option value="<?= $c['id_categoria'] ?>"><?= $c['nombre'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="fw-bold small text-muted mb-1">Descripción</label>
                        <input type="text" class="form-control bg-light border-0 rounded-3" name="descripcion"
                            id="edit_descripcion">
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 rounded-pill fw-bold">Actualizar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function editar(data) {
        document.getElementById('edit_id').value = data.id_producto;
        document.getElementById('edit_nombre').value = data.nombre;
        document.getElementById('edit_precio').value = data.precio;
        // Handle specific column name
        document.getElementById('edit_stock').value = data.cantidad_stock !== undefined ? data.cantidad_stock : data.stock;
        document.getElementById('edit_categoria').value = data.categoria;
        document.getElementById('edit_descripcion').value = data.descripcion;
    }
</script>

<?php include 'includes/footer.php'; ?>
<?php
session_start();
// NOTA IMPORTANTE: 'conexion.php' debe usar la conexión orientada a objetos (new mysqli)
include 'conexion.php'; 

// Verificar si el usuario tiene el rol de administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}

// Inicializar variables
$success = null;
$error = null;
$productos = []; 

// Obtener Categorías
$query_categorias = "SELECT id_categoria, nombre FROM categorias";
$result_categorias = mysqli_query($conn, $query_categorias);
$categorias = $result_categorias ? mysqli_fetch_all($result_categorias, MYSQLI_ASSOC) : [];

// =========================================================================
// MANEJO DE OPERACIONES (Agregar, Editar, Eliminar)
// =========================================================================
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $accion = $_POST['accion'] ?? ''; 

    if ($accion === 'agregar') {
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $categoria_id = intval($_POST['categoria'] ?? 0);

        if ($nombre && $descripcion && $precio > 0 && $categoria_id > 0) {
            $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, categoria) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $categoria_id);
            
            if ($stmt->execute()) {
                $success = "Producto agregado correctamente.";
            } else {
                $error = "Error al agregar producto: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Todos los campos son obligatorios o tienen un formato inválido.";
        }
    } elseif ($accion === 'editar') {
        $id_producto = intval($_POST['id_producto'] ?? 0);
        $nombre = trim($_POST['nombre'] ?? '');
        $descripcion = trim($_POST['descripcion'] ?? '');
        $precio = floatval($_POST['precio'] ?? 0);
        $categoria_id = intval($_POST['categoria'] ?? 0);

        if ($id_producto > 0 && $nombre && $descripcion && $precio > 0 && $categoria_id > 0) {
            $stmt = $conn->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ? WHERE id_producto = ?");
            $stmt->bind_param("ssidi", $nombre, $descripcion, $precio, $categoria_id, $id_producto);
            
            if ($stmt->execute()) {
                $success = "Producto actualizado correctamente.";
            } else {
                $error = "Error al actualizar producto: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error = "Todos los campos son obligatorios o tienen un formato inválido para editar.";
        }
    } elseif ($accion === 'eliminar') {
        $id_producto = intval($_POST['id_producto'] ?? 0);

        if ($id_producto > 0) {
            $error_temp = false;
            
            // 1. CORRECCIÓN: Eliminar primero las dependencias (detalles_ventas)
            $stmt_detalles = $conn->prepare("DELETE FROM detalles_ventas WHERE id_producto = ?");
            
            if ($stmt_detalles === false) {
                 $error = "Error al preparar la eliminación de detalles: " . $conn->error;
                 $error_temp = true;
            } else {
                $stmt_detalles->bind_param("i", $id_producto);
                
                if (!$stmt_detalles->execute()) {
                    $error = "Error al eliminar detalles de venta: " . $stmt_detalles->error;
                    $error_temp = true;
                }
                $stmt_detalles->close(); 
            }

            // 2. Eliminar el producto principal
            if (!$error_temp) {
                $stmt_producto = $conn->prepare("DELETE FROM productos WHERE id_producto = ?");
                
                if ($stmt_producto === false) {
                     $error = "Error al preparar la eliminación de producto: " . $conn->error;
                } else {
                    $stmt_producto->bind_param("i", $id_producto);
                    
                    if ($stmt_producto->execute()) {
                        if ($stmt_producto->affected_rows > 0) {
                            $success = "Producto y registros relacionados eliminados correctamente.";
                        } else {
                            $error = "Error: No se encontró el producto con ID: " . $id_producto;
                        }
                    } else {
                        $error = "Error al eliminar producto: " . $stmt_producto->error;
                    }
                    $stmt_producto->close();
                }
            }
        } else {
            $error = "ID del producto inválido.";
        }
    }
}

// Obtener todos los productos para la tabla
$query = "SELECT p.id_producto, p.nombre, p.descripcion, p.precio, p.categoria AS id_categoria, c.nombre AS categoria 
          FROM productos p 
          JOIN categorias c ON p.categoria = c.id_categoria";
$result = mysqli_query($conn, $query);

if ($result) {
    $productos = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
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
        .btn-warning, .btn-danger {
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
        <h1>Gestión de Productos</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="administrador.php" class="btn btn-secondary">Volver al Menú Anterior</a>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#agregarProductoModal">Agregar Producto</button>
        </div>

        <table class="table table-hover table-bordered text-center">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Categoría</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['id_producto']); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars(substr($producto['descripcion'], 0, 50)) . (strlen($producto['descripcion']) > 50 ? '...' : ''); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td><?php echo htmlspecialchars($producto['categoria']); ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editarProductoModal" 
                                onclick="cargarDatosEditar(<?php echo htmlspecialchars(json_encode($producto)); ?>)">Editar</button>
                            <form method="POST" class="d-inline" onsubmit="return confirm('¿Estás seguro de eliminar este producto? Se eliminarán también sus detalles de venta asociados.')">
                                <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id_producto']); ?>">
                                <input type="hidden" name="accion" value="eliminar">
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="modal fade" id="agregarProductoModal" tabindex="-1" aria-labelledby="agregarProductoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="agregarProductoModalLabel">Agregar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="agregar">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" name="precio" id="agregarPrecio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" name="categoria" required>
                                <option value="">Selecciona una categoría</option>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
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

    <div class="modal fade" id="editarProductoModal" tabindex="-1" aria-labelledby="editarProductoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editarProductoModalLabel">Editar Producto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="accion" value="editar">
                        <input type="hidden" name="id_producto" id="editarIdProducto">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombre" id="editarNombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" name="descripcion" id="editarDescripcion" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="precio" class="form-label">Precio</label>
                            <input type="number" class="form-control" name="precio" id="editarPrecio" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoria" class="form-label">Categoría</label>
                            <select class="form-select" name="categoria" id="editarCategoria" required>
                                <?php foreach ($categorias as $categoria): ?>
                                    <option value="<?php echo htmlspecialchars($categoria['id_categoria']); ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Actualizar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function cargarDatosEditar(producto) {
            document.getElementById('editarIdProducto').value = producto.id_producto;
            document.getElementById('editarNombre').value = producto.nombre;
            document.getElementById('editarDescripcion').value = producto.descripcion;
            document.getElementById('editarPrecio').value = producto.precio;
            
            // Usamos el ID de la categoría (id_categoria) para preseleccionar la opción correcta
            document.getElementById('editarCategoria').value = producto.id_categoria; 
        }
    </script>
</body>
</html>
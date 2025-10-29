<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar si el usuario tiene el rol de empleado
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header("Location: login.php"); // Redirige al login si no es empleado
    exit();
}

// Obtener los productos desde la base de datos
$query_productos = "SELECT id_producto, nombre, descripcion, precio, cantidad_stock, codigo_barras, imagen FROM productos";
$result_productos = mysqli_query($conn, $query_productos);
$productos = mysqli_fetch_all($result_productos, MYSQLI_ASSOC);

// Actualizar producto en la base de datos
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['editar_producto'])) {
    $id_producto = $_POST['id_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $cantidad_stock = $_POST['cantidad_stock'];

    $query_update = "UPDATE productos SET 
                        nombre = '$nombre', 
                        descripcion = '$descripcion', 
                        precio = '$precio', 
                        cantidad_stock = '$cantidad_stock'
                     WHERE id_producto = '$id_producto'";
    mysqli_query($conn, $query_update);
    header("Location: inventario.php"); // Redirige para evitar reenvío del formulario
    exit();
}

// Eliminar producto
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['eliminar_producto'])) {
    $id_producto = $_POST['id_producto'];

    $query_delete = "DELETE FROM productos WHERE id_producto = '$id_producto'";
    mysqli_query($conn, $query_delete);
    header("Location: inventario.php"); // Redirige para evitar reenvío del formulario
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario</title>
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
            vertical-align: middle;
        }
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 10px;
        }
        .btn-edit, .btn-delete {
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Inventario</h1>

        <!-- Botón para regresar al menú -->
        <div class="mb-3">
            <a href="loginempleado.php" class="btn btn-secondary">Regresar al Menú</a>
        </div>

        <!-- Tabla de productos -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Stock</th>
                    <th>Código de Barras</th>
                    <th>Imagen</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($productos) > 0): ?>
                    <?php foreach ($productos as $producto): ?>
                        <tr>
                            <form method="POST" action="inventario.php">
                                <td><?php echo $producto['id_producto']; ?></td>
                                <td>
                                    <input type="text" name="nombre" class="form-control" value="<?php echo htmlspecialchars($producto['nombre']); ?>">
                                </td>
                                <td>
                                    <textarea name="descripcion" class="form-control"><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                                </td>
                                <td>
                                    <input type="number" step="0.01" name="precio" class="form-control" value="<?php echo $producto['precio']; ?>">
                                </td>
                                <td>
                                    <input type="number" name="cantidad_stock" class="form-control" value="<?php echo $producto['cantidad_stock']; ?>">
                                </td>
                                <td><?php echo $producto['codigo_barras']; ?></td>
                                <td>
                                    <img src="<?php echo $producto['imagen']; ?>" alt="Imagen del Producto" class="product-img" onerror="this.src='imagenes/default.jpg';">
                                </td>
                                <td>
                                    <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                                    <button type="submit" name="editar_producto" class="btn btn-success btn-sm btn-edit">Guardar</button>
                                    <button type="submit" name="eliminar_producto" class="btn btn-danger btn-sm btn-delete">Eliminar</button>
                                </td>
                            </form>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No hay productos en el inventario.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>

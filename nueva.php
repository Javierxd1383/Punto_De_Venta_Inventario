<?php
session_start();
include 'conexion.php'; // Archivo de conexión a la base de datos

// Verificar rol
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
    header('Location: login.php');
    exit();
}

// Obtener productos
$query_productos = "SELECT id_producto, nombre, precio, codigo_barras, imagen FROM productos";
$result_productos = mysqli_query($conn, $query_productos);
if ($result_productos === false) {
    die('Error al consultar productos: ' . mysqli_error($conn));
}
$productos = mysqli_fetch_all($result_productos, MYSQLI_ASSOC);
mysqli_close($conn);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Sistema de Ventas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
  <style>
    body{background:linear-gradient(135deg,#6a11cb,#2575fc);min-height:100vh;margin:0;padding:20px;color:#333}
    .container{background:#fff;border-radius:15px;padding:20px;box-shadow:0 10px 30px rgba(0,0,0,.2);max-width:1200px;margin:auto}
    .product-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:20px}
    .product-item{background:#f9f9f9;border-radius:10px;padding:15px;text-align:center;box-shadow:0 5px 15px rgba(0,0,0,.1)}
    .product-item img{width:100%;height:150px;object-fit:cover;border-radius:10px}
    .cart-table th,.cart-table td{text-align:center}
    .btn-pay{background:#27ae60;color:#fff;border:none;padding:10px 20px;font-size:18px;border-radius:10px}
    .btn-pay:hover{background:#219150}
    .modal-confirmation{display:none;justify-content:center;align-items:center;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5);z-index:10}
    .modal-confirmation .content{background:#fff;padding:20px;border-radius:10px;text-align:center}
    .modal-confirmation.show{display:flex}
  </style>
</head>
<body>
<div class="container">
  <h1 class="text-center text-primary mb-4">Sistema de Ventas</h1>

  <div class="mb-3">
    <a href="loginempleado.php" class="btn btn-secondary">Regresar al Menú</a>
    <a href="historial_ventas.php" class="btn btn-outline-primary ms-2">Historial de Ventas</a>
  </div>

  <div class="mb-4">
    <input type="text" id="search" class="form-control" placeholder="Buscar producto por nombre o código de barras..." oninput="filtrarProductos()">
  </div>

  <!-- Productos -->
  <div class="product-grid" id="product-grid">
    <?php foreach ($productos as $p):
      $id     = (int)$p['id_producto'];
      $nombre = (string)$p['nombre'];
      $precio = is_numeric($p['precio']) ? (float)$p['precio'] : 0.0;
      $codigo = (string)$p['codigo_barras'];
      $img    = $p['imagen'] ?: 'imagenes/default.jpg';
    ?>
      <div class="product-item"
           data-nombre="<?= htmlspecialchars(strtolower($nombre)) ?>"
           data-codigo="<?= htmlspecialchars($codigo) ?>">
        <img src="<?= htmlspecialchars($img) ?>" alt="<?= htmlspecialchars($nombre) ?>" onerror="this.src='imagenes/default.jpg';">
        <h5><?= htmlspecialchars($nombre) ?></h5>
        <p>$<?= number_format($precio, 2) ?></p>

        <svg id="barcode-<?= $id ?>"></svg>
        <p><?= htmlspecialchars($codigo) ?></p>
        <script>
          JsBarcode("#barcode-<?= $id ?>", <?= json_encode($codigo) ?>, {
            format: "CODE128", width: 2, height: 50, displayValue: false
          });
        </script>

        <!-- Botón robusto con data-* (sin onclick) -->
        <button type="button" class="btn btn-primary btn-add"
                data-id="<?= $id ?>"
                data-nombre="<?= htmlspecialchars($nombre, ENT_QUOTES) ?>"
                data-precio="<?= $precio ?>">
          Agregar
        </button>
      </div>
    <?php endforeach; ?>
  </div>

  <!-- Carrito -->
  <div class="mt-4">
    <h2>Venta</h2>
    <table class="table cart-table">
      <thead>
        <tr>
          <th>Producto</th>
          <th>Cantidad</th>
          <th>Precio</th>
          <th>Total</th>
          <th>Acciones</th>
        </tr>
      </thead>
      <tbody id="cart-body">
        <tr><td colspan="5">No hay productos en el carrito.</td></tr>
      </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center">
      <h3>Total: $<span id="total">0.00</span></h3>
      <button class="btn btn-pay" onclick="mostrarOpcionesPago()">Cobrar</button>
    </div>
  </div>

  <!-- Métodos de Pago -->
  <div class="mt-4" id="metodos-pago" style="display:none;">
    <h2>Seleccionar Método de Pago</h2>
    <div class="form-check">
      <input class="form-check-input" type="radio" id="pago-efectivo" name="metodo-pago" value="efectivo" checked onclick="mostrarInputPago()">
      <label class="form-check-label" for="pago-efectivo">Efectivo</label>
    </div>
    <div class="form-check">
      <input class="form-check-input" type="radio" id="pago-tarjeta" name="metodo-pago" value="tarjeta" onclick="mostrarInputPago()">
      <label class="form-check-label" for="pago-tarjeta">Tarjeta</label>
    </div>
    <div id="input-efectivo" class="mt-3">
      <label for="efectivo" class="form-label">Efectivo recibido:</label>
      <input type="number" id="efectivo" class="form-control" step="0.01" placeholder="Ingrese el efectivo recibido" oninput="calcularCambio()">
      <h4>Cambio: $<span id="cambio">0.00</span></h4>
    </div>
    <div id="input-tarjeta" class="mt-3" style="display:none;">
      <label for="tarjeta" class="form-label">Monto Pagado con Tarjeta:</label>
      <input type="number" id="tarjeta" class="form-control" step="0.01" placeholder="Ingrese el monto pagado con tarjeta">
    </div>
    <button class="btn btn-success mt-3" onclick="realizarCompra()">Confirmar Pago</button>
  </div>
</div>

<!-- Modal de confirmación -->
<div class="modal-confirmation" id="modal-confirmation">
  <div class="content">
    <h2>¡Compra Realizada!</h2>
    <p>Gracias por su compra.</p>
    <div id="venta-info"></div>
    <button class="btn btn-primary mt-2" onclick="cerrarModal()">Cerrar</button>
  </div>
</div>

<script>
// ======= Estado =======
let carrito = [];

// ======= Render =======
function actualizarCarrito() {
  const tbody = document.getElementById('cart-body');
  tbody.innerHTML = '';
  let total = 0;

  carrito.forEach(p => {
    const subtotal = p.precio * p.cantidad;
    total += subtotal;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td>${escapeHtml(p.nombre)}</td>
      <td>
        <button class="btn btn-sm btn-outline-danger" onclick="cambiarCantidad(${p.id}, -1)">-</button>
        ${p.cantidad}
        <button class="btn btn-sm btn-outline-success" onclick="cambiarCantidad(${p.id}, 1)">+</button>
      </td>
      <td>$${p.precio.toFixed(2)}</td>
      <td>$${subtotal.toFixed(2)}</td>
      <td><button class="btn btn-danger btn-sm" onclick="eliminarDelCarrito(${p.id})">Eliminar</button></td>
    `;
    tbody.appendChild(tr);
  });

  if (carrito.length === 0) {
    tbody.innerHTML = '<tr><td colspan="5">No hay productos en el carrito.</td></tr>';
  }

  document.getElementById('total').innerText = total.toFixed(2);
  calcularCambio();
}

// ======= Carrito =======
function agregarAlCarrito(id, nombre, precio) {
  const existente = carrito.find(i => i.id === id);
  if (existente) {
    existente.cantidad++;
  } else {
    carrito.push({ id, nombre, precio: parseFloat(precio), cantidad: 1 });
  }
  actualizarCarrito();
}
function cambiarCantidad(id, cambio) {
  const p = carrito.find(i => i.id === id);
  if (!p) return;
  p.cantidad += cambio;
  if (p.cantidad <= 0) carrito = carrito.filter(i => i.id !== id);
  actualizarCarrito();
}
function eliminarDelCarrito(id) {
  carrito = carrito.filter(i => i.id !== id);
  actualizarCarrito();
}

// ======= Pago UI =======
function mostrarOpcionesPago() {
  if (carrito.length === 0) { alert('Agrega productos antes de cobrar.'); return; }
  document.getElementById('metodos-pago').style.display = 'block';
}
function mostrarInputPago() {
  const efectivo = document.getElementById('input-efectivo');
  const tarjeta  = document.getElementById('input-tarjeta');
  if (document.getElementById('pago-efectivo').checked) {
    efectivo.style.display = 'block'; tarjeta.style.display = 'none';
  } else {
    efectivo.style.display = 'none'; tarjeta.style.display = 'block';
  }
  calcularCambio();
}
function calcularCambio() {
  const total = parseFloat(document.getElementById('total').innerText) || 0;
  if (document.getElementById('pago-efectivo').checked) {
    const efectivo = parseFloat(document.getElementById('efectivo').value) || 0;
    document.getElementById('cambio').innerText = (efectivo - total).toFixed(2);
  } else {
    document.getElementById('cambio').innerText = '0.00';
  }
}
function cerrarModal() {
  document.getElementById('modal-confirmation').classList.remove('show');
  const info = document.getElementById('venta-info');
  if (info) info.innerHTML = '';
}

// ======= Confirmar compra (función completa) =======
async function realizarCompra() {
  const total = parseFloat(document.getElementById('total').innerText) || 0;
  if (carrito.length === 0 || total <= 0) { alert('No hay productos en el carrito.'); return; }

  const metodo = document.querySelector('input[name="metodo-pago"]:checked')?.value || 'efectivo';
  let efectivoRecibido = 0, montoTarjeta = 0;

  if (metodo === 'efectivo') {
    efectivoRecibido = parseFloat(document.getElementById('efectivo').value) || 0;
    if (efectivoRecibido < total) { alert('El efectivo recibido no puede ser menor que el total.'); return; }
  } else {
    montoTarjeta = parseFloat(document.getElementById('tarjeta').value) || 0;
    if (montoTarjeta < total) { alert('El monto con tarjeta no puede ser menor que el total.'); return; }
  }

  const payload = {
    items: carrito.map(p => ({ id_producto: p.id, cantidad: p.cantidad, precio_unitario: p.precio })),
    total,
    metodo_pago: metodo,
    efectivo_recibido: efectivoRecibido,
    monto_tarjeta: montoTarjeta || 0
  };

  try {
    const resp = await fetch('guardar_venta.php', {
      method: 'POST',
      headers: {'Content-Type': 'application/json'},
      body: JSON.stringify(payload),
      credentials: 'same-origin'
    });

    // Esperar y parsear JSON
    const data = await resp.json().catch(() => null);

    if (!resp.ok || !data || !data.ok) {
      const msg = data && data.message ? data.message : 'Error al guardar la venta.';
      console.error('Respuesta del servidor:', data);
      alert(msg);
      return;
    }

    // Éxito: mostrar modal y datos
    const modal = document.getElementById('modal-confirmation');
    const info = document.getElementById('venta-info');
    info.innerHTML = `<p>ID de venta: <strong>${data.id_venta ?? 'N/A'}</strong></p>
                      <p>Total: <strong>$${total.toFixed(2)}</strong> — Método: <strong>${metodo}</strong></p>`;
    if (metodo === 'efectivo') {
      const cambio = (efectivoRecibido - total).toFixed(2);
      info.innerHTML += `<p>Efectivo recibido: <strong>$${efectivoRecibido.toFixed(2)}</strong> — Cambio: <strong>$${cambio}</strong></p>`;
    }
    modal.classList.add('show');

    // Limpiar carrito y UI
    carrito = [];
    actualizarCarrito();
    document.getElementById('metodos-pago').style.display = 'none';
    const ef = document.getElementById('efectivo'); if (ef) ef.value = '';
    const tj = document.getElementById('tarjeta');  if (tj) tj.value = '';
  } catch (e) {
    console.error(e);
    alert('No se pudo conectar con el servidor.');
    return;
  }
}

// ======= Delegación de click en "Agregar" =======
document.addEventListener('click', function (e) {
  const btn = e.target.closest('.btn-add');
  if (!btn) return;
  const id = parseInt(btn.dataset.id, 10);
  const nombre = btn.dataset.nombre || '';
  const precio = parseFloat(btn.dataset.precio) || 0;
  agregarAlCarrito(id, nombre, precio);
});

// Filtro productos
function filtrarProductos() {
  const filtro = (document.getElementById('search').value || '').toLowerCase();
  document.querySelectorAll('.product-item').forEach(prod => {
    const nombre = (prod.getAttribute('data-nombre') || '').toLowerCase();
    const codigo = (prod.getAttribute('data-codigo') || '').toLowerCase();
    prod.style.display = (nombre.includes(filtro) || codigo.includes(filtro)) ? '' : 'none';
  });
}

// Util: escapar HTML simple para evitar XSS al renderizar nombres desde JS
function escapeHtml(unsafe) {
  return String(unsafe)
       .replace(/&/g, "&amp;")
       .replace(/</g, "&lt;")
       .replace(/>/g, "&gt;")
       .replace(/"/g, "&quot;")
       .replace(/'/g, "&#039;");
}
</script>
</body>
</html>

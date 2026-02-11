<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'empleado') {
  header("Location: login.php");
  exit();
}

$productos = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM productos WHERE cantidad_stock > 0"), MYSQLI_ASSOC);
$clientes = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM clientes"), MYSQLI_ASSOC);
?>
<?php include 'includes/header.php'; ?>

<div class="container-fluid fade-in h-100" style="min-height: calc(100vh - 80px);">
  <div class="row h-100 g-4">

    <!-- Left: Catalog -->
    <div class="col-lg-8">
      <div class="d-flex justify-content-between align-items-center mb-4 pt-3">
        <div>
          <h2 class="fw-bold text-dark mb-1">Nueva Venta</h2>
          <p class="text-muted mb-0">Selecciona productos para agregar a la orden.</p>
        </div>
        <div class="position-relative" style="width: 300px;">
          <input type="text" id="buscador" class="form-control rounded-pill border-0 shadow-sm ps-5"
            placeholder="Buscar dulce...">
          <i class="fa fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
        </div>
      </div>

      <div class="row g-3" id="listaProductos" style="max-height: 75vh; overflow-y: auto; padding-bottom: 50px;">
        <?php
        $placeholders = ['Imagenes/P1.png', 'Imagenes/P2.png', 'Imagenes/P3.jpg', 'Imagenes/P4.jpg', 'Imagenes/P5.jpg', 'Imagenes/P6.png'];
        $i = 0;
        foreach ($productos as $p):
          $img = $placeholders[$i % count($placeholders)];
          $i++;
          ?>
          <div class="col-md-3 col-6 producto-item" data-nombre="<?= strtolower($p['nombre']) ?>">
            <div class="card h-100 border-0 shadow-sm product-card-hover cursor-pointer"
              onclick="agregarProducto(<?= $p['id_producto'] ?>, '<?= htmlspecialchars($p['nombre']) ?>', <?= $p['precio'] ?>, <?= $p['cantidad_stock'] ?>)">
              <div class="card-body p-3 text-center">
                <div class="bg-light rounded-3 mb-3 d-flex align-items-center justify-content-center"
                  style="height: 100px;">
                  <img src="<?= $img ?>" class="img-fluid" style="max-height: 80px;">
                </div>
                <h6 class="fw-bold text-dark mb-1 text-truncate"><?= htmlspecialchars($p['nombre']) ?></h6>
                <div class="d-flex justify-content-between align-items-center mt-2">
                  <span class="badge bg-light text-muted border"><?= $p['cantidad_stock'] ?> disp.</span>
                  <span class="text-primary fw-bold">$<?= number_format($p['precio'], 2) ?></span>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Right: Cart -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-lg h-100 d-flex flex-column">
        <div class="card-header bg-white border-bottom p-4">
          <h5 class="fw-bold mb-0 text-primary"><i class="fa fa-shopping-cart me-2"></i>Orden Actual</h5>
        </div>

        <div class="card-body flex-grow-1 overflow-auto p-0" id="cartBody">
          <div id="emptyState" class="text-center py-5 mt-5">
            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3 text-muted">
              <i class="fa fa-basket-shopping fa-3x"></i>
            </div>
            <h6 class="fw-bold text-muted">Tu carrito está vacío</h6>
            <small class="text-muted">Agrega productos para comenzar</small>
          </div>
          <div id="cartItems" class="p-3"></div>
        </div>

        <div class="card-footer bg-white border-top p-4">
          <div class="d-flex justify-content-between mb-2">
            <span class="text-muted">Subtotal</span>
            <span class="fw-bold" id="subtotalVal">$0.00</span>
          </div>
          <div class="d-flex justify-content-between mb-4">
            <span class="fs-4 fw-bold text-dark">Total</span>
            <span class="fs-4 fw-bold text-primary" id="totalVal">$0.00</span>
          </div>

          <div class="mb-3">
            <select id="clienteSelect"
              class="form-select rounded-pill border-0 bg-light fw-bold text-muted text-center cursor-pointer">
              <option value="">-- Cliente General --</option>
              <?php foreach ($clientes as $c): ?>
                <option value="<?= $c['id_cliente'] ?>"><?= htmlspecialchars($c['nombre']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <button class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-md mb-2" onclick="procesarVenta()">
            <i class="fa fa-check me-2"></i> Completar Venta
          </button>
          <button class="btn btn-light text-danger w-100 py-2 rounded-pill fw-bold" onclick="vaciarCarrito()">
            Cancelar
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Success -->
<div class="modal fade" id="successModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-0 shadow-lg text-center p-4">
      <div class="mb-3">
        <div class="bg-success text-white rounded-circle d-inline-flex p-3 shadow-sm">
          <i class="fa fa-check fa-2x"></i>
        </div>
      </div>
      <h4 class="fw-bold mb-2">¡Venta Exitosa!</h4>
      <p class="text-muted">La venta se ha registrado correctamente.</p>
      <button class="btn btn-primary px-5 rounded-pill" data-bs-dismiss="modal"
        onclick="location.reload()">Aceptar</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  let carrito = [];
  const cartItemsContainer = document.getElementById('cartItems');
  const emptyState = document.getElementById('emptyState');
  const totalDisplay = document.getElementById('totalVal');
  const subtotalDisplay = document.getElementById('subtotalVal');

  // Configuración de audio (opcional, profesional touch)
  const beep = new Audio('assets/beep.mp3'); // Asegúrate de tener un sonido o quita esto si no.

  function agregarProducto(id, nombre, precio, stock) {
    const item = carrito.find(p => p.id === id);

    if (item) {
      if (item.cantidad >= stock) {
        alert('No hay más stock disponible de este producto.');
        return;
      }
      item.cantidad++;
    } else {
      carrito.push({ id, nombre, precio, cantidad: 1, stock });
    }
    renderCarrito();
  }

  function renderCarrito() {
    if (carrito.length === 0) {
      cartItemsContainer.innerHTML = '';
      emptyState.style.display = 'block';
      totalDisplay.innerText = '$0.00';
      subtotalDisplay.innerText = '$0.00';
      return;
    }

    emptyState.style.display = 'none';
    cartItemsContainer.innerHTML = '';
    let total = 0;

    carrito.forEach((p, index) => {
      total += p.precio * p.cantidad;
      const div = document.createElement('div');
      div.className = 'd-flex justify-content-between align-items-center mb-3 p-3 bg-light rounded-3';
      div.innerHTML = `
                <div class="d-flex align-items-center">
                    <div class="bg-white p-2 rounded-circle shadow-sm me-3 text-primary fw-bold" style="width:35px; height:35px; display:flex; justify-content:center; align-items:center;">
                        ${p.cantidad}
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">${p.nombre}</h6>
                        <small class="text-muted">$${p.precio.toFixed(2)} c/u</small>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                     <span class="fw-bold text-dark me-3">$${(p.precio * p.cantidad).toFixed(2)}</span>
                     <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" onclick="eliminarItem(${index})"><i class="fa fa-trash"></i></button>
                </div>
            `;
      cartItemsContainer.appendChild(div);
    });

    totalDisplay.innerText = '$' + total.toFixed(2);
    subtotalDisplay.innerText = '$' + total.toFixed(2);
  }

  function eliminarItem(index) {
    carrito.splice(index, 1);
    renderCarrito();
  }

  function vaciarCarrito() {
    if (confirm('¿Estás seguro de vaciar el carrito?')) {
      carrito = [];
      renderCarrito();
    }
  }

  async function procesarVenta() {
    if (carrito.length === 0) {
      alert("El carrito está vacío");
      return;
    }

    const idCliente = document.getElementById('clienteSelect').value;
    const total = carrito.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);

    const ventaData = {
      items: carrito.map(p => ({
        id_producto: p.id,
        cantidad: p.cantidad,
        precio: p.precio
      })),
      total: total,
      id_cliente: idCliente || null, // null si está vacío
      metodo_pago: 'efectivo' // Por defecto
    };

    try {
      const response = await fetch('guardar_venta.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(ventaData)
      });

      const result = await response.json();

      if (result.ok) {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
        carrito = [];
        renderCarrito();
      } else {
        alert('Error al procesar la venta: ' + result.message);
      }
    } catch (error) {
      console.error('Error:', error);
      alert('Hubo un error de conexión al procesar la venta.');
    }
  }

  // Buscador
  document.getElementById('buscador').addEventListener('keyup', function () {
    let text = this.value.toLowerCase();
    document.querySelectorAll('.producto-item').forEach(el => {
      let nombre = el.getAttribute('data-nombre');
      el.style.display = nombre.includes(text) ? 'block' : 'none';
    });
  });
</script>

<style>
  .product-card-hover:hover {
    transform: translateY(-5px);
    border: 1px solid var(--primary) !important;
  }

  .cursor-pointer {
    cursor: pointer;
  }
</style>
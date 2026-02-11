<?php
session_start();
if (!isset($_SESSION['rol'])) {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Herramientas</h2>
        <p class="text-muted">Utilidades para tu día a día.</p>
    </div>
</div>

<div class="row g-4 fade-in">
    <div class="col-md-5">
        <div class="card-premium p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fa fa-calculator text-primary me-2"></i>Calculadora</h5>
            <div class="bg-light p-3 rounded-4 mb-3 text-end">
                <h3 class="fw-bold text-dark mb-0" id="calcDisplay">0.00</h3>
            </div>
            <div class="row g-2">
                <div class="col-12"><button class="btn btn-primary-gradient w-100"
                        onclick="alert('Demo calculadora')">Abrir Calculadora Completa</button></div>
            </div>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card-premium p-4 h-100">
            <h5 class="fw-bold mb-4"><i class="fa fa-sticky-note text-warning me-2"></i>Notas</h5>
            <textarea class="form-control form-control-lg bg-light border-0 rounded-4" rows="6"
                placeholder="Escribe aquí recordatorios..."></textarea>
            <div class="text-end mt-3">
                <button class="btn btn-sm btn-dark px-4 rounded-pill">Guardar Nota</button>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
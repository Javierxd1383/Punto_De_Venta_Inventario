<?php
session_start();
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
    header("Location: login.php");
    exit();
}
?>

<?php include 'includes/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4 fade-in">
    <div>
        <h2 class="fw-bold mb-1">Configuración del Sistema</h2>
        <p class="text-muted">Personaliza la experiencia de tu negocio.</p>
    </div>
</div>

<div class="row g-4 fade-in">
    <div class="col-lg-8">
        <div class="card-premium p-4 mb-4">
            <h5 class="fw-bold mb-4">Información General</h5>
            <form>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Nombre del Negocio</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0"
                            value="Dulcería Candy">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted small fw-bold">Teléfono</label>
                        <input type="text" class="form-control form-control-lg bg-light border-0" value="555-1234">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label text-muted small fw-bold">Dirección</label>
                    <input type="text" class="form-control form-control-lg bg-light border-0"
                        value="Calle Principal #123">
                </div>
                <div class="text-end">
                    <button type="button" class="btn btn-primary-gradient px-4">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card-premium p-4 mb-4">
            <h5 class="fw-bold mb-4">Preferencias</h5>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="checkNotif" checked>
                <label class="form-check-label fw-medium" for="checkNotif">Notificaciones por correo</label>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="checkSound" checked>
                <label class="form-check-label fw-medium" for="checkSound">Sonidos del sistema</label>
            </div>
            <div class="form-check form-switch mb-3">
                <input class="form-check-input" type="checkbox" id="checkDark" disabled>
                <label class="form-check-label fw-medium text-muted" for="checkDark">Modo Oscuro (BETA)</label>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
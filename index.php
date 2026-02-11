<?php
session_start();
include 'conexion.php';
$productos_destacados = mysqli_fetch_all(mysqli_query($conn, "SELECT * FROM productos LIMIT 6"), MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dulcería Candy | Energía y Sabor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="assets/css/styles.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #FFFFFF 0%, #E3F2FD 100%);
            padding: 120px 0 100px;
            position: relative;
            overflow: hidden;
        }
        
        .hero-circle {
            position: absolute;
            background: var(--primary);
            opacity: 0.05;
            border-radius: 50%;
            z-index: 0;
            animation: float 6s infinite ease-in-out;
        }
        
        .hero-circle-1 { width: 500px; height: 500px; top: -100px; right: -100px; }
        .hero-circle-2 { width: 300px; height: 300px; bottom: 50px; left: -50px; animation-delay: 1s; }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top bg-white shadow-sm py-3 animate-up">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="Imagenes/logo.png" alt="Logo" height="50" class="me-3 animate-pulse">
                <div class="d-flex flex-column">
                    <h4 class="fw-bold mb-0 text-gradient" style="font-family: 'Montserrat', sans-serif;">Dulcería Candy</h4>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navMain">
                <ul class="navbar-nav ms-auto align-items-center gap-4">
                    <li class="nav-item"><a class="nav-link fw-bold text-dark hover-blue" href="#inicio">Inicio</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-dark hover-blue" href="#catalogo">Catálogo</a></li>
                    <li class="nav-item"><a class="nav-link fw-bold text-dark hover-blue" href="#contacto">Contacto</a></li>
                    <li class="nav-item">
                        <a href="login.php" class="btn btn-primary px-4 shadow-md rounded-pill">
                            <i class="fa fa-rocket me-2"></i> Acceso
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero -->
    <section id="inicio" class="hero-section">
        <div class="hero-circle hero-circle-1"></div>
        <div class="hero-circle hero-circle-2"></div>
        <div class="container position-relative z-1">
            <div class="row align-items-center">
                <div class="col-lg-6 animate-up delay-100">
                    <span class="badge bg-primary-light text-primary px-3 py-2 rounded-pill fw-bold mb-3 shadow-sm">
                        <i class="fa fa-star me-2"></i> Calidad Premium
                    </span>
                    <h1 class="display-3 fw-bold mb-4 text-dark">Explosión de <br><span class="text-gradient">Sabor y Alegría</span></h1>
                    <p class="lead text-muted mb-5 fs-5">
                        Descubre la colección más vibrante de dulces. Tradición y novedad se unen para ofrecerte la mejor experiencia.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="#catalogo" class="btn btn-primary btn-lg px-5 rounded-pill shadow-lg animate-pulse">
                            ¡Quiero Dulces!
                        </a>
                        <a href="#contacto" class="btn btn-outline-primary btn-lg px-4 rounded-pill">
                            Contáctanos
                        </a>
                    </div>
                </div>
                <div class="col-lg-6 text-center mt-5 mt-lg-0 animate-up delay-300">
                    <div class="position-relative">
                        <div class="position-absolute top-50 start-50 translate-middle bg-primary rounded-circle" style="width: 400px; height: 400px; opacity: 0.1; filter: blur(50px);"></div>
                        <img src="Imagenes/mixgomitas.webp" class="img-fluid rounded-circle shadow-lg animate-float position-relative z-2"
                            style="max-height: 480px; border: 8px solid white;">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Catalog -->
    <section id="catalogo" class="py-5 bg-light">
        <div class="container py-5">
            <div class="text-center mb-5 animate-up">
                <h5 class="text-primary fw-bold text-uppercase letter-spacing-2 mb-2">Nuestro Catálogo</h5>
                <h2 class="fw-bold fs-1 mb-3">Favoritos de la Semana</h2>
                <div class="mx-auto bg-primary rounded-pill" style="width: 60px; height: 4px;"></div>
            </div>

            <div class="row g-4">
                <?php
                $placeholders = ['Imagenes/P1.png', 'Imagenes/P2.png', 'Imagenes/P3.jpg', 'Imagenes/P4.jpg', 'Imagenes/P5.jpg', 'Imagenes/P6.png'];
                $i = 0;
                foreach ($productos_destacados as $p):
                    $img = $placeholders[$i % count($placeholders)];
                    $i++;
                    ?>
                    <div class="col-md-4 col-sm-6 animate-up delay-<?= ($i * 100) ?>">
                        <div class="product-card-public bg-white h-100">
                            <div class="p-5 text-center position-relative" style="background: radial-gradient(circle at center, #F5F7FA 0%, transparent 70%);">
                                <span class="position-absolute top-0 end-0 m-3 badge bg-primary shadow-sm text-white">$<?= number_format($p['precio'], 2) ?></span>
                                <img src="<?= $img ?>" class="img-fluid animate-float" style="max-height: 180px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1));">
                            </div>
                            <div class="p-4 pt-0 text-center">
                                <h5 class="fw-bold mb-2 text-dark"><?= htmlspecialchars($p['nombre']) ?></h5>
                                <p class="text-muted small mb-4 text-truncate"><?= htmlspecialchars($p['descripcion']) ?></p>
                                <button class="btn btn-outline-primary rounded-pill w-100 fw-bold">Agregar al Carrito</button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="#" class="btn btn-primary-gradient px-5 py-3 rounded-pill shadow-md">
                    Ver Todo el Inventario <i class="fa fa-arrow-right ms-2"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-white py-5 position-relative overflow-hidden">
        <div class="container position-relative z-1 text-center">
            <img src="Imagenes/logo.png" height="60" class="mb-4 animate-up">
            <h3 class="fw-bold text-dark mb-4">Dulcería Candy</h3>
            
            <div class="d-flex justify-content-center gap-3 mb-5">
                <a href="#" class="btn btn-primary btn-lg rounded-circle shadow-sm text-white"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="btn btn-info btn-lg rounded-circle shadow-sm text-white"><i class="fab fa-twitter"></i></a>
                <a href="#" class="btn btn-danger btn-lg rounded-circle shadow-sm text-white"><i class="fab fa-instagram"></i></a>
            </div>
            
            <p class="small text-muted mb-0">&copy; 2024 Dulcería Candy. Todos los derechos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
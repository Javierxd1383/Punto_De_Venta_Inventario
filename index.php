<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dulcería Candy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff7e6;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #ff6f61;
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        header h1 {
            margin: 0;
        }
        .login-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: white;
            color: #ff6f61;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }
        .login-btn:hover {
            background-color: #ffebe0;
        }
        nav {
            text-align: center;
            background: #ffa08a;
            padding: 10px;
        }
        nav a {
            text-decoration: none;
            color: white;
            margin: 0 15px;
            font-size: 18px;
        }
        nav a:hover {
            color: #ffebe0;
        }
        main {
            flex: 1;
            padding: 20px;
            text-align: center;
        }
        .product {
            display: inline-block;
            margin: 15px;
            padding: 10px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .product img {
            width: 150px;
            height: 150px;
            border-radius: 8px;
        }
        .product h3 {
            color: #ff6f61;
        }
        footer {
            background: #ff6f61;
            color: white;
            text-align: center;
            padding: 10px;
        }
    </style>
</head>
<body>
    <header>
        <a class="login-btn" href="login.php">Iniciar sesión</a>
        <h1>Dulcería Candy</h1>
        <p>¡Los dulces más deliciosos para endulzar tu día!</p>
    </header>
    <nav>
        <a href="#">Inicio</a>
        <a href="#">Productos</a>
        <a href="#">Promociones</a>
        <a href="#">Contacto</a>
    </nav>
    <main>
        <h2>Nuestros Dulces</h2>
        <div class="product">
            <img src="https://via.placeholder.com/150" alt="Caramelos">
            <h3>Caramelos</h3>
            <p>Desde $10.00</p>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/150" alt="Chocolates">
            <h3>Chocolates</h3>
            <p>Desde $15.00</p>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/150" alt="Gomitas">
            <h3>Gomitas</h3>
            <p>Desde $8.00</p>
        </div>
        <div class="product">
            <img src="https://via.placeholder.com/150" alt="Mazapanes">
            <h3>Mazapanes</h3>
            <p>Desde $5.00</p>
        </div>
    </main>
    <footer>
        <p>© 2024 Dulcería Candy. Todos los derechos reservados.</p>
    </footer>
</body>
</html>

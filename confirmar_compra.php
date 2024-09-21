<?php
session_start();
include 'db/connection.php'; // Asegúrate de que la ruta sea correcta

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Debes iniciar sesión para realizar una compra.'); window.location.href = 'login.html';</script>";
    exit();
}

$nombre_usuario = htmlspecialchars($_SESSION['user_name']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Compra</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
         :root {
            --color-fondo: #f9f9f9;
            --color-primario: #231709;
            --color-secundario: #795C34;
            --color-terciario: #65350f;
            --color-cuaternario: #d1b6a8;
            --color-quinto: #80471C;
        } 

        .form-container {
            width: 35%;
            margin: 2rem auto;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: bold;
            padding: 2rem;
            border: 2px solid var(--color-primario);
            background-color: var(--color-fondo);
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .form-group {
            margin-bottom: 0.7rem;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
            color: var(--color-primario);
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--color-terciario);
            border-radius: 0.5rem;
            font-size: 1rem;
            color: var(--color-primario);
        }

        .form-group input:focus {
            border-color: var(--color-secundario);
            outline: none;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        .btn-container {
            display: flex;
            justify-content: center;
            margin-top: 1.5rem;
        }

        .btn-primary {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background-color: var(--color-primario);
            color: #fff;
            text-decoration: none;
            border-radius: 0.5rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: bold;
            font-size: 1rem;
            transition: background-color 0.3s ease, color 0.3s ease;
            text-align: center;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: var(--color-secundario);
            color: var(--color-cuaternario);
        }
    </style>
</head>
<body>
<header class="header">
    <div class="header-top">
        <div class="logo-title"> <!-- Logo y Título -->
            <img src="images/logo.jpg" alt="Logo" class="imagen_logo">
            <h1 id="title">Papelería y más...</h1>
        </div>
        <div class="user-info">
            <span class="page-title">Bienvenido, <?php echo $nombre_usuario; ?></span>
            <a href="PHP/logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <div class="header-bottom">
        <nav class="nav">
            <ul>
                <li class="btnmenu"><a href="index.php">Inicio</a></li>
                <li class="btnmenu"><a href="productos.php">Productos</a></li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <h1 class="page-title">Confirmar Compra</h1>
    <div class="form-container">
        <form action="orden.php" method="post">
            <div class="form-group">
                <label for="identificacion">Identificación:</label>
                <input type="text" name="identificacion" id="identificacion" required>
            </div>
            <div class="form-group">
                <label for="nombres">Nombres:</label>
                <input type="text" name="nombres" id="nombres" required>
            </div>
            <div class="form-group">
                <label for="apellidos">Apellidos:</label>
                <input type="text" name="apellidos" id="apellidos" required>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" name="correo" id="correo" required>
            </div>
            <div class="form-group">
                <label for="ciudad">Ciudad:</label>
                <input type="text" name="ciudad" id="ciudad" required>
            </div>
            <div class="form-group">
                <label for="barrio">Barrio:</label>
                <input type="text" name="barrio" id="barrio" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" name="direccion" id="direccion" required>
            </div>
            <div class="form-group">
                <label for="complemento">Complemento de la Dirección:</label>
                <input type="text" name="complemento" id="complemento">
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" name="telefono" id="telefono" required>
            </div>
            <div class="btn-container">
                <button type="submit" class="btn-primary">Confirmar Compra</button>
            </div>
        </form>
    </div>
</main>

<footer>
    <div class="footer-content" id="footer-content">
        <div class="contact-info">
         <p>Teléfono: +57 3103301067</p>
            <p>Dirección: Carrera 5 N° 5-75 Fresno, Tolima</p>
            <p>Email: coffeenet27@hotmail.com</p>
        </div>
        <div class="social-icons">
            <a href="#" class="fa fa-facebook"></a>
            <a href="#" class="fa fa-twitter"></a>
            <a href="#" class="fa fa-instagram"></a>
            <a href="#" class="fa fa-linkedin"></a>
        </div>
        <div class="location">
            <a href="#" class="fa fa-map-marker"></a>
        </div>
        <div class="copyright">
            &copy; 2024 CoffeNet. Todos los derechos reservados.
        </div>
    </div>
</footer>
</body>
</html>

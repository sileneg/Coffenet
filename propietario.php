<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un propietario
if ($_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}

$nombre_usuario = htmlspecialchars($_SESSION['user_name']);

// Obtener todas las órdenes
$stmt = $pdo->query("SELECT * FROM ordenes");
$ordenes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel del Propietario</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #f1f1f1;}

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .admin-info {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            margin-top: 30px;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            border: 2px solid #795C34;
            border-radius: 10px;
            background-color: #fff;
        }

        .info-box {
            width: 50%;
            padding: 20px;
        }

        .info-box img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .info-box p {
            font-size: 1.1rem;
            color: #333;
        }

        .user-management {
            margin: 2rem 0;
        }

        .user-management table {
            width: 100%;
            border-collapse: collapse;
        }

        .user-management table, th, td {
            border: 1px solid #ddd;
        }

        .user-management th, td {
            padding: 12px;
            text-align: left;
        }

        .user-management th {
            background-color: #f2f2f2;
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
            <a href="php/logout.php">Cerrar Sesión</a>
        </div>
    </div>

    <div class="header-bottom">
        <nav class="nav">
            <ul>
                <li class="btnmenu"><a href="index.php">Inicio</a></li>
                <li class="btnmenu dropdown">
                    <a href="#">Gestionar Productos</a>
                    <div class="dropdown-content">
                        <a href="productos.php">Productos</a>
                        <a href="agregar_producto.php">Agregar Producto</a>
                    </div>
                </li>
                <li class="btnmenu"><a href="gestionar_usuarios.php">Gestionar Usuarios</a></li>
                <li class="btnmenu"><a href="ver_ordenes.php">Órdenes Recibidas</a></li>

            </ul>
        </nav>
    </div>
</header>

<main>
    <h1 class="page-title">Panel del Propietario</h1>
    <div class="admin-info">
        <div class="info-box">
            <p>El propietario tiene la responsabilidad de supervisar todas las operaciones comerciales. Puede agregar, editar y eliminar productos, así como garantizar que la tienda funcione sin problemas. Además, el propietario debe mantener una comunicación efectiva con los proveedores, gestionar el inventario y asegurarse de que los productos estén siempre disponibles para los clientes. Es fundamental que el propietario mantenga un entorno de trabajo positivo y productivo, motivando a los empleados y asegurando un excelente servicio al cliente.</p>
        </div>
        <div class="info-box">
            <img src="images/papeleria.jpg" alt="Propietario">
        </div>
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

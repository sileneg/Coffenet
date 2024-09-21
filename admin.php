<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un administrador
if ($_SESSION['user_level'] != 1) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['user_name']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
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
            padding: 30px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            border: 2px solid #795C34;
            border-radius: 10px;
            background-color: #fff;
            width: 80%;
            margin:auto;
            margin-bottom: 30px;
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
                <li class="btnmenu dropdown">
                    <a href="#">Gestionar Productos</a>
                    <div class="dropdown-content">
                        <a href="productos.php">Productos</a>
                        <a href="agregar_producto.php">Agregar Producto</a>
                    </div>
                </li>
                <li class="btnmenu dropdown">
                    <a href="#">Gestionar Usuarios</a>
                    <div class="dropdown-content">
                        <a href="admin_registro_usuarios.php">Agregar Usuario</a>
                        <a href="consultar_usuarios.php">Consultar Usuarios</a>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <h1 class="page-title">Panel de Administración</h1>
    <div class="admin-info">
        <div class="info-box">
            <p>El administrador es responsable de gestionar los productos y usuarios en el sistema. Puede agregar, editar y eliminar productos, así como gestionar las cuentas de los usuarios. Además, el administrador debe asegurarse de que toda la información esté actualizada y sea precisa. También es responsable de supervisar las actividades de los demás usuarios y garantizar que todas las operaciones se realicen de manera eficiente y segura.</p>
        </div>
        <div class="info-box">
            <img src="images/logo-admin.jpeg" alt="Administrador">
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

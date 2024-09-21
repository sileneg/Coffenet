<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario está autenticado y es un cliente
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 4) {
    header('Location: login.php'); // Redirigir al login si no está autenticado o no es cliente
    exit();
}

$user_id = $_SESSION['user_id'];
$nombre_usuario = htmlspecialchars($_SESSION['user_name']);

// Obtener las calificaciones y comentarios del usuario
$stmt = $pdo->prepare("
    SELECT p.nombre AS producto_nombre, c.calificacion, c.comentario 
    FROM calificaciones c
    JOIN productos p ON c.producto_id = p.id
    WHERE c.usuario_id = ?
");
$stmt->execute([$user_id]);
$calificaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

$numeroCarrito = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Calificaciones</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
         :root {
            --color-fondo: #f9f9f9;
            --color-primario: #231709;
            --color-secundario: #795C34;
            --color-terciario: #65350f;
            --color-cuaternario: #d1b6a8;
            --color-quinto: #80471C;
        }
        .page-title {
            text-align: center;
            color: var(--color-primario);
            font-size: 2rem;
            margin: 1rem 0;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        .cart-icon {
            position: relative;
            display: inline-block;
            margin-left: 1rem;
            width: 70px;
            height: 70px;
            background-color: transparent;
        }
        .cart-icon img {
            width: 100%;
            height: auto;
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
        }

        .calificaciones-container {
            width: 80%;
            margin: 2rem auto;
            background-color: var(--color-cuaternario);
            padding: 1.5rem;
            border-radius: 0.8rem;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
        }

        .calificacion-item {
            background-color: white;
            padding: 1rem;
            border-radius: 0.4rem;
            margin-bottom: 1rem;
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        .calificacion-item h3 {
            margin: 0;
            color: var(--color-primario);
            font-size: 1.5rem;
        }

        .calificacion-item p {
            margin: 0.5rem 0;
            color: var(--color-secundario);
            font-size: 1rem;
        }

        .calificacion-item strong {
            font-weight: bold;
            color: var(--color-terciario);
        }

        .btnmenu {
            background-color: var(--color-primario);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 0.4rem;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btnmenu:hover {
            background-color: var(--color-secundario);
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
            <?php if (isset($_SESSION['user_name'])): ?>
            <div class="user-info">
                <span class="page-title">Bienvenido, <?php echo $nombre_usuario; ?></span>
                <a href="PHP/logout.php" class="btnmenu">Cerrar Sesión</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="header-bottom">
            <nav class="nav">
                <ul>
                    <li class="btnmenu"><a href="index.php">Inicio</a></li>
                    <li class="btnmenu"><a href="productos.php">Productos</a></li>
                    <li class="cart-icon">
                            <a href="carrito.php">
                                <img src="images/carrito2.png" alt="Carrito">
                                <span class="cart-count"><?php echo $numeroCarrito; ?></span>
                            </a>
                    </li>                
                </ul>
            </nav>
        </div>
    </header>

    <main>
        <h1 class="page-title">Mis Calificaciones</h1>
        <div class="calificaciones-container">
            <?php if (count($calificaciones) > 0): ?>
                <?php foreach ($calificaciones as $calificacion): ?>
                    <div class="calificacion-item">
                        <h3><?php echo htmlspecialchars($calificacion['producto_nombre']); ?></h3>
                        <p><strong>Calificación: <?php echo $calificacion['calificacion']; ?>/5</strong></p>
                        <p><?php echo htmlspecialchars($calificacion['comentario']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No has realizado ninguna calificación aún.</p>
            <?php endif; ?>
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

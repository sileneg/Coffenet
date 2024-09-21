<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un propietario
if ($_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}

$orden_id = $_GET['orden_id'];

// Obtener detalles de la orden
$stmt = $pdo->prepare("SELECT * FROM detalles_orden WHERE orden_id = ?");
$stmt->execute([$orden_id]);
$detalles_orden = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener información de envío
$stmt = $pdo->prepare("SELECT * FROM informacion_envio WHERE orden_id = ?");
$stmt->execute([$orden_id]);
$informacion_envio = $stmt->fetch(PDO::FETCH_ASSOC);

$nombre_usuario = htmlspecialchars($_SESSION['user_name']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalles de la Orden</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .order-details, .shipping-info {
            margin: 20px 0;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            border: 2px solid #795C34;
            border-radius: 10px;
            background-color: #fff;
        }
        .order-details h2, .shipping-info h2 {
            margin-bottom: 20px;
        }
        .order-details table, .shipping-info table {
            width: 100%;
            border-collapse: collapse;
        }
        .order-details table, th, td, .shipping-info table, th, td {
            border: 1px solid #ddd;
        }
        .order-details th, td, .shipping-info th, td {
            padding: 12px;
            text-align: left;
        }
        .order-details th, .shipping-info th {
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
                <li class="btnmenu"><a href="propietario.php">Panel Propietario</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <h1 class="page-title">Detalles de la Orden #<?php echo htmlspecialchars($orden_id); ?></h1>

    <div class="shipping-info">
        <h2>Información de Envío</h2>
        <table>
            <tr>
                <th>Identificación</th>
                <td><?php echo htmlspecialchars($informacion_envio['identificacion']); ?></td>
            </tr>
            <tr>
                <th>Nombres</th>
                <td><?php echo htmlspecialchars($informacion_envio['nombres']); ?></td>
            </tr>
            <tr>
                <th>Apellidos</th>
                <td><?php echo htmlspecialchars($informacion_envio['apellidos']); ?></td>
            </tr>
            <tr>
                <th>Correo</th>
                <td><?php echo htmlspecialchars($informacion_envio['correo']); ?></td>
            </tr>
            <tr>
                <th>Ciudad</th>
                <td><?php echo htmlspecialchars($informacion_envio['ciudad']); ?></td>
            </tr>
            <tr>
                <th>Barrio</th>
                <td><?php echo htmlspecialchars($informacion_envio['barrio']); ?></td>
            </tr>
            <tr>
                <th>Dirección</th>
                <td><?php echo htmlspecialchars($informacion_envio['direccion']); ?></td>
            </tr>
            <tr>
                <th>Complemento</th>
                <td><?php echo htmlspecialchars($informacion_envio['complemento']); ?></td>
            </tr>
            <tr>
                <th>Teléfono</th>
                <td><?php echo htmlspecialchars($informacion_envio['telefono']); ?></td>
            </tr>
        </table>
    </div>

    <div class="order-details">
        <h2>Productos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($detalles_orden as $detalle): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($detalle['producto_id']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['cantidad']); ?></td>
                        <td><?php echo htmlspecialchars($detalle['precio']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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

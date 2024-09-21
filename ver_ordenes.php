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
    <title>Órdenes Recibidas</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .user-management {
            width: 90%;
            margin: 20px auto;
        }

        .user-management table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .user-management th, .user-management td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        .user-management th {
            background-color: #f2f2f2;
        }

        .btn-accion {
            display: inline-block;
            padding: 8px 12px;
            background-color: #795C34;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn-accion:hover {
            background-color: #65350f;
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
    <h1 class="page-title">Órdenes Recibidas</h1>
    
    <div class="user-management">
        <table>
            <thead>
                <tr>
                    <th>ID Orden</th>
                    <th>Usuario ID</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ordenes as $orden): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($orden['id']); ?></td>
                        <td><?php echo htmlspecialchars($orden['usuario_id']); ?></td>
                        <td><?php echo htmlspecialchars($orden['fecha']); ?></td>
                        <td><?php echo htmlspecialchars($orden['total']); ?></td>
                        <td>
                            <a href="detalle_orden.php?orden_id=<?php echo $orden['id']; ?>" class="btn-accion">Ver Detalles</a>
                            <a href="eliminar_orden.php?orden_id=<?php echo $orden['id']; ?>" class="btn-accion" onclick="return confirm('¿Estás seguro de que deseas eliminar esta orden?')">Eliminar</a>
                        </td>
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

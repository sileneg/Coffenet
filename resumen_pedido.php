<?php
session_start();
include 'db/connection.php'; // Asegúrate de que la ruta sea correcta

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Debes iniciar sesión para ver el resumen de tu pedido.'); window.location.href = 'login.html';</script>";
    exit();
}

$usuario_id = $_SESSION['user_id'];
$orden_id = $_GET['orden_id'];

// Obtener detalles de la orden
$stmt = $pdo->prepare("SELECT * FROM ordenes WHERE id = ? AND usuario_id = ?");
$stmt->execute([$orden_id, $usuario_id]);
$orden = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    echo "<script>alert('No se encontró la orden.'); window.location.href = 'productos.php';</script>";
    exit();
}

// Obtener detalles de los productos de la orden
$stmt = $pdo->prepare("SELECT p.nombre, p.descripcion, d.cantidad, d.precio
                       FROM detalles_orden d
                       JOIN productos p ON d.producto_id = p.id
                       WHERE d.orden_id = ?");
$stmt->execute([$orden_id]);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener información de envío
$stmt = $pdo->prepare("SELECT * FROM informacion_envio WHERE orden_id = ?");
$stmt->execute([$orden_id]);
$envio = $stmt->fetch(PDO::FETCH_ASSOC);

// Calcular el total de la orden y el IVA
$total_orden = 0;
foreach ($productos as $producto) {
    $total_orden += $producto['cantidad'] * $producto['precio'];
}
$iva = $total_orden * 0.19;
$total_con_iva = $total_orden + $iva;

// Generar número de factura (por simplicidad, aquí se usa el ID de la orden)
$numero_factura = str_pad($orden_id, 8, "0", STR_PAD_LEFT);

// Fecha de la factura (puedes ajustar el formato según tus necesidades)
$fecha_factura = date("d/m/Y");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resumen de Pedido</title>
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

        .factura-container {
            width: 95%;
            margin: 1.5rem auto;
            padding: 1.1rem;
            border: 2px solid var(--color-primario);
            background-color: var(--color-fondo);
            border-radius: 0.5rem;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color: var(--color-primario);
        }

        .factura-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }

        .factura-header img {
            width: 80px;
            height: auto;
        }

        .factura-header h1 {
            font-size: 2rem;
            margin: 0;
            text-align: center;
            flex: 1;
        }

        .factura-info {
            text-align: right;
            font-size: 1rem;
            margin-bottom: 1.2rem;
            color: var(--color-primario);
        }

        .tabla-info-envio, .tabla-productos-solicitados {
            width: 100%;
            border-collapse: collapse;
            margin-top: 1rem;
            background-color: var(--color-fondo);
        }

        .tabla-info-envio td, .tabla-productos-solicitados th, .tabla-productos-solicitados td {
            border: 1px solid var(--color-terciario);
            padding: 0.8rem;
            text-align: left;
            vertical-align: top;
            color: var(--color-primario);
        }

        .tabla-info-envio th {
            width: 30%;
            text-align: left;
        }

        .tabla-productos-solicitados th {
            background-color: var(--color-secundario);
            color: #fff;
            font-weight: bold;
        }

        .tabla-productos-solicitados td {
            background-color: var(--color-cuaternario);
        }

        .total-factura {
            text-align: right;
            font-size: 1.2rem;
            font-weight: bold;
            margin-top: 1rem;
        }

        .footer-info {
            margin-top: 2rem;
            text-align: center;
        }

        .footer-content1 {
            width: 95%;
            margin: auto;
            padding: 1rem 0;
            font-size: 0.9rem;
        }

        .contact-info1 p {
            margin: 0.5rem 0;
            font-size: 1rem;
            color: var(--color-primario);
        }

        /* Ocultar header, footer y head en la impresión */
        @media print {
            .header, footer {
                display: none;
            }

            /* Hack para ocultar el contenido del head */
            @page {
                margin: 0;
            }

            body {
                margin: 0;
                padding: 0;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <div class="header-top">
        <div class="logo-title"> <!-- Logo y Título -->
            <img src="images/logo.jpg" alt="Logo" class="imagen_logo">
            <h1 id="title">Papelería y Más...</h1>
        </div>
        <div class="user-info">
            <span class="page-title">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="PHP/logout.php">Cerrar Sesión</a>
        </div>
    </div>
    <div class="header-bottom">
        <nav class="nav">
            <ul>
                <li class="btnmenu"><a href="index.php">Inicio</a></li>
                <li class="btnmenu"><a href="productos.php">Productos</a></li>
                <li class="btnmenu"><a href="#" onclick="window.print()">Imprimir Factura</a></li>
            </ul>
        </nav>
    </div>
</header>
<main>
    <div class="factura-container">
        <section class="factura-header">
            <img src="images/logo.jpg" alt="CoffeNet Logo" class="logo-img">
            <h1 class="page-title">Factura Electrónica</h1>
        </section>

        <div class="factura-info">
            <p><strong>Número de Factura:</strong> <?php echo $numero_factura; ?></p>
            <p><strong>Fecha:</strong> <?php echo $fecha_factura; ?></p>
        </div>

        <section class="info-envio">
            <h2>Información de Envío</h2>
            <table class="tabla-info-envio">
                <tr>
                    <th>Identificación:</th>
                    <td><?php echo htmlspecialchars($envio['identificacion']); ?></td>
                </tr>
                <tr>
                    <th>Nombre Completo:</th>
                    <td><?php echo htmlspecialchars($envio['nombres'] . ' ' . $envio['apellidos']); ?></td>
                </tr>
                <tr>
                    <th>Correo:</th>
                    <td><?php echo htmlspecialchars($envio['correo']); ?></td>
                </tr>
                <tr>
                    <th>Dirección Completa:</th>
                    <td><?php echo htmlspecialchars($envio['direccion'] . ', ' . $envio['complemento'] . ', ' . $envio['barrio'] . ', ' . $envio['ciudad']); ?></td>
                </tr>
                <tr>
                    <th>Teléfono:</th>
                    <td><?php echo htmlspecialchars($envio['telefono']); ?></td>
                </tr>
            </table>
        </section>

        <section class="productos-solicitados">
            <h2>Productos Solicitados</h2>
            <table class="tabla-productos-solicitados">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Cantidad</th>
                        <th>Valor Unitario</th>
                        <th>Valor Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td>$<?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p class="total-factura">Subtotal: $<?php echo number_format($total_orden, 2); ?></p>
            <p class="total-factura">IVA (19%): $<?php echo number_format($iva, 2); ?></p>
            <p class="total-factura">Total de la Orden: $<?php echo number_format($total_con_iva, 2); ?></p>
        </section>

        <section class="footer-info">
            <div class="footer-content1">
                <div class="contact-info1">
                    <p>Teléfono: +123 456 7890</p>
                    <p>Dirección: Calle Falsa 123, Ciudad, País</p>
                    <p>Email: contacto@empresa.com</p>
                </div>
            </div>
        </section>
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

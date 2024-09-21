<?php
session_start();
include 'db/connection.php'; // Asegúrate de que la ruta sea correcta

// Verificar que el usuario haya iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Debes iniciar sesión para realizar una compra.'); window.location.href = 'login.html';</script>";
    exit();
}

$usuario_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener datos de envío
    $identificacion = $_POST['identificacion'];
    $nombres = $_POST['nombres'];
    $apellidos = $_POST['apellidos'];
    $correo = $_POST['correo'];
    $ciudad = $_POST['ciudad'];
    $barrio = $_POST['barrio'];
    $direccion = $_POST['direccion'];
    $complemento = $_POST['complemento'];
    $telefono = $_POST['telefono'];

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Obtener todos los items del carrito para el usuario actual
        $carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

        if (empty($carrito)) {
            echo "<script>alert('Tu carrito está vacío. No puedes procesar una orden.'); window.location.href = 'carrito.php';</script>";
            exit();
        }

        // Calcular el total de la orden
        $total = 0;
        foreach ($carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // Insertar nueva orden en la tabla de ordenes
        $stmt = $pdo->prepare("INSERT INTO ordenes (usuario_id, fecha, total) VALUES (?, NOW(), ?)");
        $stmt->execute([$usuario_id, $total]);
        $orden_id = $pdo->lastInsertId();

        // Insertar detalles de la orden y actualizar el stock de productos
        $stmt = $pdo->prepare("INSERT INTO detalles_orden (orden_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
        foreach ($carrito as $item) {
            $stmt->execute([$orden_id, $item['id'], $item['cantidad'], $item['precio']]);

            // Actualizar stock del producto
            $stmtStock = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
            $stmtStock->execute([$item['cantidad'], $item['id']]);
        }

        // Insertar información de envío
        $stmt = $pdo->prepare("INSERT INTO informacion_envio (orden_id, identificacion, nombres, apellidos, correo, ciudad, barrio, direccion, complemento, telefono) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$orden_id, $identificacion, $nombres, $apellidos, $correo, $ciudad, $barrio, $direccion, $complemento, $telefono]);

        // Vaciar el carrito del usuario
        $_SESSION['carrito'] = [];

        // Confirmar la transacción
        $pdo->commit();

        // Redirigir al usuario a la página de resumen de pedido
        header("Location: resumen_pedido.php?orden_id=$orden_id");
        exit();

    } catch (Exception $e) {
        // Si ocurre un error, revertir la transacción
        $pdo->rollBack();
        error_log("Error al procesar la orden: " . $e->getMessage());
        echo "<script>alert('Error al procesar la orden. Intente de nuevo más tarde.'); window.location.href = 'carrito.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesando Orden</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="logo-title"> <!-- Logo y Título -->
                <img src="images/logo.jpg" alt="Logo" class="imagen_logo">
                <h1 id="title">Papelería y más...</h1>
            </div>
        </div>
    </header>
    <main>
        <h1 class="page-title">Procesando Orden</h1>
        <p>Por favor, espera mientras procesamos tu orden...</p>
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

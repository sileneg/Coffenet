<?php
session_start();
include 'PHP/connection.php'; // Ruta correcta para incluir connection.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['user_id']; // Asegúrate de que el usuario haya iniciado sesión

    try {
        // Iniciar transacción
        $pdo->beginTransaction();

        // Obtener todos los items del carrito para el usuario actual
        $stmt = $pdo->prepare("SELECT c.id, p.id AS producto_id, p.precio, c.cantidad FROM carrito c JOIN productos p ON c.producto_id = p.id WHERE c.usuario_id = ?");
        $stmt->execute([$usuario_id]);
        $items_carrito = $stmt->fetchAll();

        if (empty($items_carrito)) {
            echo "<script>alert('Tu carrito está vacío. No puedes procesar una orden.'); window.location.href = '/CoffeNet.com/carrito.php';</script>";
            exit();
        }

        // Calcular el total de la orden
        $total = 0;
        foreach ($items_carrito as $item) {
            $total += $item['precio'] * $item['cantidad'];
        }

        // Insertar nueva orden en la tabla de ordenes
        $stmt = $pdo->prepare("INSERT INTO ordenes (usuario_id, fecha, total) VALUES (?, NOW(), ?)");
        $stmt->execute([$usuario_id, $total]);
        $orden_id = $pdo->lastInsertId();

        // Insertar detalles de la orden
        $stmt = $pdo->prepare("INSERT INTO detalles_orden (orden_id, producto_id, cantidad, precio) VALUES (?, ?, ?, ?)");
        foreach ($items_carrito as $item) {
            $stmt->execute([$orden_id, $item['producto_id'], $item['cantidad'], $item['precio']]);
        }

        // Vaciar el carrito del usuario
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE usuario_id = ?");
        $stmt->execute([$usuario_id]);

        // Confirmar la transacción
        $pdo->commit();

        echo "<script>alert('Orden procesada exitosamente. Gracias por tu compra.'); window.location.href = '/CoffeNet.com/productos.php';</script>";

    } catch (PDOException $e) {
        // Si ocurre un error, revertir la transacción
        $pdo->rollBack();
        echo "<script>alert('Error al procesar la orden. Intente de nuevo más tarde.'); window.location.href = '/CoffeNet.com/carrito.php';</script>";
    }
}
?>

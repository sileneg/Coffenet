<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar que el usuario tiene permiso para eliminar productos
if ($_SESSION['user_level'] != 1 && $_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para eliminar productos.'); window.location.href = 'productos.php';</script>";
    exit();
}

// Verificar que el ID del producto fue proporcionado
if (!isset($_GET['id'])) {
    echo "<script>alert('Producto no encontrado.'); window.location.href = 'productos.php';</script>";
    exit();
}

$product_id = $_GET['id'];

try {
    // Eliminar el producto de la base de datos
    $stmt = $pdo->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->execute([$product_id]);

    echo "<script>alert('Producto eliminado exitosamente.'); window.location.href = 'productos.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error al eliminar el producto: {$e->getMessage()}'); window.history.back();</script>";
}
?>

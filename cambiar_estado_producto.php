<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar que el usuario tiene permiso para cambiar el estado del producto
if ($_SESSION['user_level'] != 1 && $_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para realizar esta acción.'); window.location.href = 'productos.php';</script>";
    exit();
}

// Verificar que el ID del producto y el nuevo estado fueron proporcionados
if (!isset($_GET['id']) || !isset($_GET['estado'])) {
    echo "<script>alert('Producto no encontrado o estado no especificado.'); window.location.href = 'productos.php';</script>";
    exit();
}

$product_id = $_GET['id'];
$estado = $_GET['estado'];

try {
    // Actualizar el estado del producto en la base de datos
    $stmt = $pdo->prepare("UPDATE productos SET activo = ? WHERE id = ?");
    $stmt->execute([$estado, $product_id]);

    $mensaje = $estado ? 'Producto activado exitosamente.' : 'Producto desactivado exitosamente.';
    echo "<script>alert('$mensaje'); window.location.href = 'productos.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error al cambiar el estado del producto: {$e->getMessage()}'); window.history.back();</script>";
}
?>

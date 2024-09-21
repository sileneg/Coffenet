<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un propietario
if ($_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para realizar esta acción.'); window.location.href = 'ver_ordenes.php';</script>";
    exit();
}

// Verificar que el ID de la orden fue proporcionado
if (!isset($_GET['orden_id'])) {
    echo "<script>alert('Orden no encontrada.'); window.location.href = 'ver_ordenes.php';</script>";
    exit();
}

$orden_id = $_GET['orden_id'];

try {
    // Eliminar primero la información de envío relacionada con la orden
    $stmtEnvio = $pdo->prepare("DELETE FROM informacion_envio WHERE orden_id = ?");
    $stmtEnvio->execute([$orden_id]);

    // Eliminar los detalles de la orden para evitar restricciones de clave foránea
    $stmtDetalles = $pdo->prepare("DELETE FROM detalles_orden WHERE orden_id = ?");
    $stmtDetalles->execute([$orden_id]);

    // Eliminar la orden de la base de datos
    $stmtOrden = $pdo->prepare("DELETE FROM ordenes WHERE id = ?");
    $stmtOrden->execute([$orden_id]);

    echo "<script>alert('Orden eliminada exitosamente.'); window.location.href = 'ver_ordenes.php';</script>";
} catch (PDOException $e) {
    echo "<script>alert('Error al eliminar la orden: {$e->getMessage()}'); window.history.back();</script>";
}
?>

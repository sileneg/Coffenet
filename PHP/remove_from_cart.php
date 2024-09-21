<?php
session_start();
include 'PHP/connection.php'; // Ruta correcta para incluir connection.php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $carrito_id = $_POST['carrito_id'];
    $usuario_id = $_SESSION['user_id']; // Asegúrate de que el usuario haya iniciado sesión

    try {
        // Verificar que el producto pertenece al usuario
        $stmt = $pdo->prepare("DELETE FROM carrito WHERE id = ? AND usuario_id = ?");
        $stmt->execute([$carrito_id, $usuario_id]);

        header("Location: /CoffeNet.com/carrito.php");
    } catch (PDOException $e) {
        echo "<script>alert('Error al eliminar el producto del carrito. Intente de nuevo.'); window.history.back();</script>";
    }
}
?>

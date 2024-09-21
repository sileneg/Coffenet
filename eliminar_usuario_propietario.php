<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un propietario
if ($_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para realizar esta acción.'); window.location.href = 'gestionar_usuarios.php';</script>";
    exit();
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        $query = "DELETE FROM usuarios WHERE id = :id AND user_level = 4";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id);

        if ($stmt->execute()) {
            echo "<script>alert('Usuario eliminado exitosamente'); window.location.href = 'gestionar_usuarios.php';</script>";
        } else {
            echo "<script>alert('Error al eliminar usuario'); window.location.href = 'gestionar_usuarios.php';</script>";
        }
    } catch (PDOException $e) {
        die("Error al eliminar usuario: " . $e->getMessage());
    }
} else {
    echo "<script>alert('ID de usuario no proporcionado'); window.location.href = 'gestionar_usuarios.php';</script>";
    exit();
}
?>

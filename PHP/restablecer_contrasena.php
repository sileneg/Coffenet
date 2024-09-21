<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../db/connection.php");
session_start();

if (isset($_POST["nueva_contrasena"]) && isset($_POST["confirmar_contrasena"])) {
    $nueva_contrasena = $_POST['nueva_contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    if ($nueva_contrasena == "" || $confirmar_contrasena == "") {
        echo '<script>alert("Datos vacíos. Por favor, ingrese la nueva contraseña.");</script>';
        echo '<script>window.location="../restablecer_contrasena.html";</script>';
    } elseif ($nueva_contrasena !== $confirmar_contrasena) {
        echo '<script>alert("Las contraseñas no coinciden. Inténtelo de nuevo.");</script>';
        echo '<script>window.location="../restablecer_contrasena.html";</script>';
    } else {
        $user_id = $_SESSION['user_id'];
        $hashed_password = password_hash($nueva_contrasena, PASSWORD_BCRYPT);

        try {
            $stmt = $pdo->prepare("UPDATE usuarios SET password = ? WHERE id = ?");
            $stmt->execute([$hashed_password, $user_id]);

            echo '<script>alert("Cambio de contraseña exitoso.");</script>';
            echo '<script>window.location="../index.php";</script>';
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo '<script>alert("Por favor, complete todos los campos.");</script>';
    echo '<script>window.location="../restablecer_contrasena.html";</script>';
}
$pdo = null; // Esto cierra la conexión
?>

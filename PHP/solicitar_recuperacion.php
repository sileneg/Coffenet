<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../db/connection.php");
session_start();

if (isset($_POST["user_or_email"])) {
    $input = $_POST["user_or_email"];

    if (empty($input)) {
        echo '<script>alert("Por favor, ingrese un nombre de usuario o correo electrónico.");</script>';
        echo '<script>window.location="../recuperar_contrasena.html";</script>';
        exit();
    }

    try {
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ? OR email = ?");
        $stmt->execute([$input, $input]);

        $user = $stmt->fetch();

        if ($user) {
            $_SESSION['user_id'] = $user['id'];
            echo '<script>window.location="../restablecer_contrasena.html";</script>';
        } else {
            echo '<script>alert("El usuario o correo no existe en la base de datos.");</script>';
            echo '<script>window.location="../recuperar_contrasena.html";</script>';
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
$pdo = null; // Esto cierra la conexión
?>

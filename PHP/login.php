<?php
session_start();
include '../db/connection.php'; // Incluir archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar los datos del formulario
    $login_username = $_POST['username'];
    $login_password = $_POST['password'];

    try {
        // Preparar y ejecutar una consulta
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$login_username]);

        $user = $stmt->fetch();

        if ($user && password_verify($login_password, $user['password'])) {
            // Contraseña correcta
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['username'];
            $_SESSION['user_level'] = $user['user_level'];

            // Redirigir según el nivel de usuario
            switch ($user['user_level']) {
                case 1: // Administrador
                    header("Location: ../admin.php"); // Redirigir a la página de administración
                    break;
                case 2: // Propietario
                    header("Location: ../propietario.php"); // Redirigir a la página del propietario
                    break;
                case 3: // Vendedor
                    header("Location: ../consulta_productos.php"); // Redirigir a la página de productos (solo vista)
                    break;
                case 4: // Cliente
                    header("Location: ../productos.php"); // Redirigir a la página de productos
                    break;
                default:
                    header("Location: ../index.php"); // Redirigir a productos por defecto
                    break;
            }
            exit(); // Asegúrate de terminar el script después de la redirección
        } else {
            echo "<script>alert('Usuario o contraseña incorrectos.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$pdo = null; // Esto cierra la conexión
?>

<?php
session_start();

if (!isset($_SESSION['user_name'])) {
    // Redirige al usuario a la página de inicio de sesión si no está autenticado
    header("Location: login.html");
    exit();
}
?>
<?php
// Define la contraseña que deseas encriptar
$password = "sile1234"; // Reemplaza con tu contraseña real

// Generar el hash de la contraseña
$password_hashed = password_hash($password, PASSWORD_BCRYPT);

// Imprimir el hash para usar en la consulta SQL
echo "Hash de la contraseña: " . $password_hashed;
?>


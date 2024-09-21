<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "coffenet";

try {
    // Cambia $host por $servername
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}
?>


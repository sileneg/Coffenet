<?php
session_start();
include '../db/connection.php'; // Incluir archivo de conexión

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir y sanitizar los datos del formulario
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmar_contraseña'];
    $user_level = $_POST['user_level'];

    // Verificar que la contraseña y la confirmación coincidan
    if ($password !== $confirm_password) {
        echo "<script>alert('Las contraseñas no coinciden.'); window.history.back();</script>";
        exit();
    }

    // Verificar que la contraseña sea alfanumérica y tenga entre 8 y 12 caracteres
    if (!preg_match('/^(?=.*[a-zA-Z])(?=.*\d)[a-zA-Z\d]{8,12}$/', $password)) {
        echo "<script>alert('La contraseña debe ser alfanumérica y tener entre 8 y 12 caracteres.'); window.history.back();</script>";
        exit();
    }

    // Validar el nivel de usuario
    if ($user_level == 1 || $user_level == 2) {
        echo "<script>alert('No tienes permiso para crear este tipo de usuario.'); window.history.back();</script>";
        exit();
    }

    try {
        // Verificar si el nombre de usuario ya existe
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('El nombre de usuario ya está registrado. Por favor digite una diferente'); window.history.back();</script>";
            exit();
        }

        // Encriptar la contraseña
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Insertar datos en la base de datos
        $stmt = $pdo->prepare("INSERT INTO usuarios (firstname, lastname, address, phone, email, username, password, user_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$firstname, $lastname, $address, $phone, $email, $username, $password_hashed, $user_level]);

        echo "<script>alert('Registro exitoso.'); window.location.href = '/CoffeNet.com/index.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Cerrar la conexión
$pdo = null;
?>

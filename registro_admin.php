<?php
session_start();

// Verificar si el usuario está autenticado como administrador
if (!isset($_SESSION['user_level']) || $_SESSION['user_level'] != 1) {
    echo "<script>alert('Acceso denegado.'); window.location.href = 'index.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link rel="stylesheet" href="CSS/styles_registro.css">
    <script>
        function focusNextField(currentField, nextFieldId) {
            if (currentField.value.length === currentField.maxLength) {
                document.getElementById(nextFieldId).focus();
            }
        }

        function clearForm() {
            document.getElementById("registration-form").reset();
        }

        window.onload = function() {
            clearForm();
        }
    </script>
</head>
<body>
    <header class="header">
        <img class="imagen_logo" src="images/logo.jpg" alt="Icono empresa">
        <h1 id="title">Papelería y más...</h1>
        <nav class="nav">
            <ul>
                <li class="btnmenu"><a href="admin.php">Inicio Admin</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <div class="form-container">
            <div class="form-image">
                <img src="images/logo.jpg" alt="Icono de registro">
            </div>
            <div class="registration-form">
                <h2>Registro de Usuarios</h2>
                <form id="registration-form" action="PHP/registro.php" method="POST" autocomplete="off">
                    <label for="firstname">Nombres:</label>
                    <input type="text" id="firstname" name="firstname" required autocomplete="off" maxlength="50" placeholder="Escriba sus nombres">
                    <label for="lastname">Apellidos:</label>
                    <input type="text" id="lastname" name="lastname" required autocomplete="off" maxlength="50" placeholder="Escriba sus apellidos">
                    <label for="phone">Teléfono:</label>
                    <input type="tel" id="phone" name="phone" required autocomplete="off" maxlength="15" placeholder="Número de Teléfono" oninput="focusNextField(this, 'address')">
                    <label for="address">Dirección:</label>
                    <input type="text" id="address" name="address" required autocomplete="off" maxlength="100" placeholder="Dirección Residencia">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required autocomplete="off" placeholder="Correo electrónico">
                    <label for="username">Nombre de usuario:</label>
                    <input type="text" id="username" name="username" required autocomplete="off" placeholder="Usuario">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" maxlength="20" placeholder="Contraseña alfanumérica 8 - 12 caracteres" >
                    <label for="confirmar_contraseña">Repetir Contraseña:</label>
                    <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required autocomplete="new-password" maxlength="20" placeholder="Repita su contraseña">
                    <label for="user_level">Nivel de usuario:</label>
                    <select id="user_level" name="user_level" required>
                        <option value="2">Propietario</option>
                        <option value="3">Vendedor</option>
                    </select>
                    <button type="submit">Registrar Usuario</button>
                </form>
            </div>
        </div>
    </main>
    <footer>
        <div class="footer-content" id="footer-content">
            <div class="contact-info">
                <p>Teléfono: +57 3103301067</p>
                <p>Dirección: Carrera 5 N° 5-75 Fresno, Tolima</p>
                <p>Email: coffeenet27@hotmail.com</p>
            </div>
            <div class="social-icons">
                <a href="#" class="fa fa-facebook"></a>
                <a href="#" class="fa fa-twitter"></a>
                <a href="#" class="fa fa-instagram"></a>
                <a href="#" class="fa fa-linkedin"></a>
            </div>
            <div class="location">
                <a href="#" class="fa fa-map-marker"></a>
            </div>
            <div class="copyright">
                &copy; 2024 CoffeNet. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>

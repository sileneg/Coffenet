<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un administrador
if ($_SESSION['user_level'] != 1) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}

$nombre_usuario = htmlspecialchars($_SESSION['user_name']);

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
    if (!in_array($user_level, [1, 2, 3])) {
        die("Error: Nivel de usuario no válido.");
    }

    try {
        // Verificar si el nombre de usuario ya existe
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);

        if ($stmt->rowCount() > 0) {
            echo "<script>alert('El nombre de usuario ya está registrado. Por favor digite una diferente'); window.history.back();</script>";
            exit();
        }

        // Verificar si ya existe un administrador
        if ($user_level == 1) {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user_level = ?");
            $stmt->execute([1]);

            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Ya existe un administrador registrado.'); window.history.back();</script>";
                exit();
            }
        }

        // Verificar si ya existe un propietario
        if ($user_level == 2) {
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE user_level = ?");
            $stmt->execute([2]);

            if ($stmt->rowCount() > 0) {
                echo "<script>alert('Ya existe un propietario registrado.'); window.history.back();</script>";
                exit();
            }
        }

        // Encriptar la contraseña
        $password_hashed = password_hash($password, PASSWORD_BCRYPT);

        // Insertar datos en la base de datos
        $stmt = $pdo->prepare("INSERT INTO usuarios (firstname, lastname, address, phone, email, username, password, user_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$firstname, $lastname, $address, $phone, $email, $username, $password_hashed, $user_level]);

        echo "<script>alert('Registro exitoso.'); window.location.href = 'admin.php';</script>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

// Cerrar la conexión
$pdo = null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuarios</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        /* Colores base */
        :root {
            --color-fondo: #f9f9f9;
            --color-primario: #231709;
            --color-secundario: #795C34;
            --color-terciario: #65350f;
            --color-cuaternario: #9A7B4f;
            --color-quinto: #80471C;
        }
                /* styles.css */
        body {
            background-size: cover;
            background-color: #e4d6c2;
            margin: 0;
            padding: 0;
        }

        .header {
            top: 0;
            left: 0;
            width: 100%;
            background: rgb(35, 23, 9);
            background: linear-gradient(90deg, rgba(35, 23, 9, 1) 23%, rgba(35, 23, 9, 1) 100%);
            display:grid;
            flex-direction: column; /* Cambiado a columna para organizar los divs en fila */
            z-index: 1000;
            grid-template-rows: auto auto; /* Dos filas: superior e inferior */
            grid-template-columns: 1fr; /* Una columna */
            box-sizing: border-box;
            padding: 0; /* Sin padding */
            z-index: 1000;
        }

        .header .top {
            display:flex;
            justify-content: space-between; /* Espacio entre los dos divs */
            align-items: center; /* Alineación vertical central */
            height: 6rem; /* Ajusta la altura del header según sea necesario */
            padding: 0 1rem; /* Espacio horizontal para los divs */
        }

        .logo-title {
            display: flex;
            align-items: center;
        /* justify-content: flex-start;*/
            padding: 1.4rem 1rem; /* Espacio horizontal */
            width: 50%; /* Ocupa el 50% del ancho del header */
        }

        .imagen_logo {
            width: 5rem; /* Ajusta el tamaño según sea necesario */
            height: auto; /* Mantiene la proporción de la imagen */
        }

        #title {
            font-size: 2rem;
            font-family: cursive;
            font-weight: bold;
            color: #9A7B4f;
            padding-left: 1rem; /* Espacio entre el logo y el título */
        }

        .user-info {
            justify-content: flex-end;
            padding: 0 1rem; /* Espacio horizontal */
            text-align: right;
            margin-top: -6rem;
        }

        .user-info span {
            display: block;
            margin-bottom: 0.5rem;
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: bold;
        }

        .user-info a {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: hsla(47, 32%, 75%, 0.2);
            color: #fff; /* Color del texto del botón */
            text-decoration: none;
            border-radius: 50px 15px 50px;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1rem;
        }

        .user-info a:hover {
            color: #f3dbc3; /* Color de fondo del botón al pasar el mouse */
        }

        .nav {
            display: flex;
            width: 100%;
            background: rgb(35, 23, 9);
            background: linear-gradient(90deg, rgba(35, 23, 9, 1) 23%, rgba(35, 23, 9, 1) 100%);
            box-sizing: border-box;
            grid-column: 1 / 2; /* Ocupa toda la columna */
            padding:1.3rem;
            justify-content: space-between;
        }

        ul {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            margin: 0;
            list-style: none;
            width: 100%;
            gap: 3.5rem;
        }

        li {
            background-color: hsla(47, 32%, 75%, 0.2);
            display: flex;
            align-items: center;
            padding: 0.625rem;
            margin-left: 0.125rem;
            border-radius: 50px 15px 50px;
        }

        a {
            text-decoration: none;
            color: #fff;
            font-size: 1rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: bold;
        }

        a:hover {
            color: #f3dbc3;
            font-size: 1.25rem;
        }

        /* Asegúrate de que haya suficiente margen en la parte superior para las secciones */
        main {
            padding: 0;
            margin-top: 1rem; /* Ajusta el margen para que haya suficiente espacio para el slider */
        }

        /* Asegura que el contenedor de formulario e imagen se ubiquen lado a lado */
        .form-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 4rem;
        }

        .form-image {
            flex: 1;
            max-width: 40%; /* Reducido el ancho máximo de la imagen */
            text-align: center;
            margin-right: 1.2rem; /* Espacio entre la imagen y el formulario */
        }

        .form-image img {
            max-height: 24rem; /* Ajusta la altura máxima de la imagen */
            width: auto; /* Mantiene la relación de aspecto */
            border: 2px solid var(--color-secundario);
            border-radius: 8px; /* Bordes redondeados */
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .registration-form {
            flex: 1;
            max-width: 35%; /* Reducido el ancho máximo del formulario */
            background-color: var(--color-cuaternario);
            border: 2px solid var(--color-secundario);
            margin-left: 8rem; /* Espacio entre la imagen y el formulario */
            padding: 2rem; /* Reducido el padding */
            border-radius: 8px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
        }

        .registration-form h2 {
            text-align: center;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 2rem; /* Reducido el tamaño del título */
            font-weight: bold;
            color: var(--color-primario);
        }

        .registration-form label {
            display: block;
            margin-top: 1rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1.1rem; /* Reducido el tamaño del texto de los labels */
            font-weight: bold;
            color: var(--color-primario);
        }

        .registration-form input, 
        .registration-form select {
            width: 100%;
            padding: 0.3rem;
            margin-top: 0.5rem;
            border: 1px solid var(--color-secundario);
            border-radius: 4px;
        }

        .registration-form button {
            background-color: var(--color-primario);
            color: white;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: bold;
            font-size: 1.2rem;
            border: none;
            padding: 0.75rem; /* Reducido el padding del botón */
            margin-top: 1rem;
            cursor: pointer;
            border-radius: 4px;
            width: 100%;
        }

        .registration-form button:hover {
            background-color: var(--color-secundario);
        }
    </style>

</head>
<body>
    <header class="header">
        <div class="header-top">
            <div class="logo-title"> <!-- Logo y Título -->
                <img src="images/logo.jpg" alt="Logo" class="imagen_logo">
                <h1 id="title">Papelería y más...</h1>
            </div>
            <div class="user-info">
                <span class="page-title">Bienvenido, <?php echo $nombre_usuario; ?></span>
                <a href="PHP/logout.php">Cerrar Sesión</a>
            </div>
        </div>

        <div class="header-bottom">
            <nav class="nav">
                <ul>
                    <li class="btnmenu"><a href="index.php">Inicio</a></li>
                    <li class="btnmenu"><a href="admin.php">Panel Admin</a></li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <div class="form-container">
            <div class="form-image">
                <img src="images/logo.jpg" alt="Icono de registro">
            </div>
            <div class="registration-form">
                <h2>Registro de Usuarios</h2>
                <form id="registration-form" action="admin_registro_usuarios.php" method="POST" autocomplete="off">
                    <label for="firstname">Nombres:</label>
                    <input type="text" id="firstname" name="firstname" required autocomplete="off" maxlength="50" placeholder="Escriba sus nombres">
                    <label for="lastname">Apellidos:</label>
                    <input type="text" id="lastname" name="lastname" required autocomplete="off" maxlength="50" placeholder="Escriba sus apellidos">
                    <label for="phone">Teléfono:</label>
                    <input type="tel" id="phone" name="phone" required autocomplete="off" maxlength="15" placeholder="Número de Teléfono">
                    <label for="address">Dirección:</label>
                    <input type="text" id="address" name="address" required autocomplete="off" maxlength="100" placeholder="Dirección Residencia">
                    <label for="email">Correo electrónico:</label>
                    <input type="email" id="email" name="email" required autocomplete="off" placeholder="Correo electrónico">
                    <label for="username">Nombre de usuario:</label>
                    <input type="text" id="username" name="username" required autocomplete="off" placeholder="Usuario">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password" maxlength="20" placeholder="Contraseña alfanumérica 8 - 12 caracteres">
                    <label for="confirmar_contraseña">Repetir Contraseña:</label>
                    <input type="password" id="confirmar_contraseña" name="confirmar_contraseña" required autocomplete="new-password" maxlength="20" placeholder="Repita su contraseña">
                    <label for="user_level">Nivel de usuario:</label>
                    <select id="user_level" name="user_level" required>
                        <option value="2">Propietario</option>
                        <option value="3">Vendedor</option>
                    </select>
                    <button type="submit" class="btn btn-primary">Registrar Usuario</button>
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
    </footer>
</body>
</html>

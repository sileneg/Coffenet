<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un propietario
if ($_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['user_name']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $username = $_POST['username'];
    $user_level = 4; // Solo clientes

    $query = "UPDATE usuarios SET firstname = ?, lastname = ?, address = ?, phone = ?, email = ?, username = ? WHERE id = ? AND user_level = 4";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$firstname, $lastname, $address, $phone, $email, $username, $id])) {
        echo "<script>alert('Usuario actualizado correctamente.'); window.location.href = 'gestionar_usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al actualizar el usuario.'); window.location.href = 'gestionar_usuarios.php';</script>";
    }
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No se ha especificado un ID de usuario.'); window.location.href = 'gestionar_usuarios.php';</script>";
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM usuarios WHERE id = ? AND user_level = 4";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "<script>alert('Usuario no encontrado o no es un cliente.'); window.location.href = 'gestionar_usuarios.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        .admin-info {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            width: 40%;
            margin: auto;
            margin-bottom: 2rem;
            margin-top: 30px;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            border: 2px solid #795C34;
            border-radius: 10px;
            background-color: #fff;
        }

        .info-box {
            width: 100%;
            padding: 20px;
        }

        .info-box img {
            width: 100%;
            height: auto;
            border-radius: 10px;
        }

        .info-box p {
            font-size: 1.1rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1rem;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1.2rem;
            font-weight: bold;
            font-style: normal;
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-group input {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            color: white;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn-primary {
            background-color: #795C34;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1rem;
            font-weight: bold;
            font-style: normal;
            border: 4px solid #9a7b47;
            color:#fff;

        }

        .btn-primary:hover {
            background-color: #65350f;
            color:#fff;
        }
    </style>
</head>
<body>
<header class="header">
    <div class="header-top">
        <div class="logo-title">
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
                <li class="btnmenu"><a href="propietario.php">Panel Propietario</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <h1 class="page-title">Editar Cliente</h1>
    <div class="admin-info">
        <div class="info-box">
            <form action="editar_usuario_propietario.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                <div class="form-group">
                    <label for="firstname">Nombre:</label>
                    <input type="text" id="firstname" name="firstname" class="form-control" value="<?php echo htmlspecialchars($user['firstname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lastname">Apellido:</label>
                    <input type="text" id="lastname" name="lastname" class="form-control" value="<?php echo htmlspecialchars($user['lastname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="address">Dirección:</label>
                    <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="phone">Teléfono:</label>
                    <input type="text" id="phone" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="username">Nombre de Usuario:</label>
                    <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
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

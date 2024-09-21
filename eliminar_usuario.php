<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un administrador
if ($_SESSION['user_level'] != 1) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}
$nombre_usuario = htmlspecialchars($_SESSION['user_name']);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];

    // Eliminar el usuario de la base de datos usando PDO
    $query = "DELETE FROM usuarios WHERE id = ?";
    $stmt = $pdo->prepare($query);
    if ($stmt->execute([$id])) {
        echo "<script>alert('Usuario eliminado correctamente.'); window.location.href = 'consultar_usuarios.php';</script>";
    } else {
        echo "<script>alert('Error al eliminar el usuario.'); window.location.href = 'consultar_usuarios.php';</script>";
    }
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<script>alert('No se ha especificado un ID de usuario.'); window.location.href = 'consultar_usuarios.php';</script>";
    exit();
}

$id = $_GET['id'];
$query = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    echo "<script>alert('Usuario no encontrado.'); window.location.href = 'consultar_usuarios.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eliminar Usuario</title>
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
            width: 70%;
            margin:auto;
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

        .btn {
            color: white;
            padding: 10px 15px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
            margin-top: 10px;
        }

        .btn-danger, .btn-secondary {
            background-color: #795C34;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1rem;
            font-weight: bold;
            font-style: normal;
            border: 4px solid #9a7b4f;
        }

        .btn-danger:hover, .btn-secondary:hover {
            background-color: #65350f;
            border: 4px solid #9a7b4f;
            color:#fff;
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
    <h1 class="page-title">Eliminar Usuario</h1>
    <div class="admin-info">
        <div class="info-box">
            <form action="eliminar_usuario.php" method="post">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($user['id']); ?>">
                <p>¿Estás seguro de que deseas eliminar al usuario <strong><?php echo htmlspecialchars($user['firstname'] . ' ' . $user['lastname']); ?></strong>?</p>
                <button type="submit" class="btn btn-danger">Eliminar Usuario</button>
                <a href="consultar_usuarios.php" class="btn btn-secondary">Cancelar</a>
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

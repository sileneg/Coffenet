<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un propietario
if ($_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'productos.php';</script>";
    exit();
}

try {
    $query = "SELECT * FROM usuarios WHERE user_level = 4";
    $stmt = $pdo->query($query);
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al consultar usuarios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestionar Usuarios</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-info {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 2rem;
            margin-top: 30px;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            border: 2px solid #795C34;
            border-radius: 10px;
            background-color: #fff;
        }

        .admin-info{
            width: 98%;
            margin:auto;
            margin-bottom: 30px;
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

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #795C34;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1.2rem;
            font-weight: bold;
            font-style: normal;
            color: white;
        }

        table td{
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1rem;
            font-style: normal;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        table tr:hover {
            background-color: #ddd;
        }

        .btn {
            color: white;
            padding: 6px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            border-radius: 5px;
        }

        .btn-edit, .btn-delete {
            background-color: #795C34;
            font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1rem;
            font-weight: bold;
            font-style: normal;
            border: 4px solid #9a7b47;
            color:#fff;
        }

        .btn-edit:hover, .btn-delete:hover {
            background-color: #65350f;
            color:#fff;
        }

        .btn-admin {
            background-color: #795C34;
            margin-top: 20px;
        }

        .btn-admin:hover {
            background-color: #65350f;
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
            <span class="page-title">Bienvenido, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
            <a href="php/logout.php">Cerrar Sesión</a>
        </div>
    </div>
    <div class="header-bottom">
        <nav class="nav">
            <ul>
                <li class="btnmenu"><a href="index.php">Inicio</a></li>
                <li class="btnmenu"><a href="propietario.php">Panel propietario</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <h1 class="page-title">Gestionar Usuarios</h1>
    <div class="admin-info">
        <div class="info-box">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Apellido</th>
                        <th>Dirección</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Nombre de Usuario</th>
                        <th>Nivel de Usuario</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $row) { ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php echo htmlspecialchars($row['firstname']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastname']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['phone']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['user_level']); ?></td>
                        <td>
                            <a href="editar_usuario_propietario.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-edit">Editar</a>
                            <a href="eliminar_usuario_propietario.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-delete">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
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

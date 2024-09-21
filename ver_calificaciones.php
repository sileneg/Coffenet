
<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Definir si el usuario está autenticado
$autenticado = isset($_SESSION['user_level']);
$user_level = $autenticado ? $_SESSION['user_level'] : 0; // 0 para no autenticado
$nombre_usuario = $autenticado ? htmlspecialchars($_SESSION['user_name']) : '';
$user_id = $autenticado ? $_SESSION['user_id'] : null; // ID del usuario autenticado

// Obtener el ID del producto
$producto_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener las calificaciones y comentarios del producto
$stmtCalificaciones = $pdo->prepare("
    SELECT c.calificacion, c.comentario, u.username
    FROM calificaciones c
    JOIN usuarios u ON c.usuario_id = u.id
    WHERE c.producto_id = ?
");
$stmtCalificaciones->execute([$producto_id]);
$calificaciones = $stmtCalificaciones->fetchAll(PDO::FETCH_ASSOC);

// Obtener información del producto
$stmtProducto = $pdo->prepare("SELECT nombre FROM productos WHERE id = ?");
$stmtProducto->execute([$producto_id]);
$producto = $stmtProducto->fetch(PDO::FETCH_ASSOC);

// Obtener categorías de la base de datos (mostrar todas las categorías incluso si no tienen productos activos)
$stmtCategorias = $pdo->query("SELECT DISTINCT categoria FROM productos");
$categorias = $stmtCategorias ? $stmtCategorias->fetchAll(PDO::FETCH_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calificaciones de <?php echo htmlspecialchars($producto['nombre']); ?></title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        :root {
            --color-fondo: #f9f9f9;
            --color-primario: #231709;
            --color-secundario: #795C34;
            --color-terciario: #65350f;
            --color-cuaternario: #d1b6a8;
            --color-quinto: #80471C;
        }

        h2{
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1.5rem;
            font-weight: bold;
            color: var(--color-primario);
        }
        
        .calificacion-container {
            width: 95%;
            margin: auto;
            margin-top: 20px;
        }

        .calificacion-item {
            background-color: #f9f9f9;
            padding: 1rem;
            border-radius: 0.4rem;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
        }

        .calificacion-item p {
            margin: 0;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        .calificacion-item strong {
            color: var(--color-primario);
            font-size: 1rem;
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
            <?php if ($autenticado): ?>
            <div class="user-info">
                <span class="page-title">Bienvenido, <?php echo $nombre_usuario; ?></span>
                <a href="PHP/logout.php">Cerrar Sesión</a>
            </div>
            <?php endif; ?>
        </div>
        <div class="header-bottom">
            <nav class="nav">
                <ul>
                    <li class="btnmenu"><a href="index.php">Inicio</a></li>
                    <?php if ($user_level == 1): ?>
                        <li class="btnmenu"><a href="admin.php">Panel Admin</a></li>
                    <?php elseif ($user_level == 2): ?>
                        <li class="btnmenu"><a href="propietario.php">Panel Propietario</a></li>
                    <?php else: ?>
                        <li class="btnmenu"><a href="<?php echo $autenticado ? 'mis_compras.php' : 'login.php'; ?>">Mis Compras</a></li>
                        <li class="cart-icon">
                            <a href="carrito.php">
                                <img src="images/carrito2.png" alt="Carrito">
                                <span class="cart-count"><?php echo $numeroCarrito; ?></span>
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
            <?php if (!$autenticado || $user_level == 3 || $user_level == 4): ?>
            <div class="search-container">
                <form action="buscar_productos.php" method="get">
                    <input type="text" name="buscar" placeholder="Buscar productos">
                    <select name="categoria">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo htmlspecialchars($categoria['categoria']); ?>"><?php echo htmlspecialchars($categoria['categoria']); ?></option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btnmenu">Buscar</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <main>
        <section class="calificacion-container">
            <h2>Calificaciones de <?php echo htmlspecialchars($producto['nombre']); ?></h2>
            <?php if (count($calificaciones) > 0): ?>
                <?php foreach ($calificaciones as $calificacion): ?>
                    <div class="calificacion-item">
                        <p><strong>Usuario: <?php echo htmlspecialchars($calificacion['username']); ?></strong></p>
                        <p><strong>Calificación: <?php echo $calificacion['calificacion']; ?>/5</strong></p>
                        <p><?php echo htmlspecialchars($calificacion['comentario']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No hay calificaciones para este producto.</p>
            <?php endif; ?>
        </section>
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

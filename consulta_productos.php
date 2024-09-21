<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar si el usuario es un vendedor
if ($_SESSION['user_level'] != 3) {
    echo "<script>alert('No tienes permisos para acceder a esta página.'); window.location.href = 'index.html';</script>";
    exit();
}

// Filtrar productos según búsqueda y categoría
$buscar = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

$sql = "SELECT * FROM productos WHERE 1=1";
$params = [];

if ($buscar) {
    $sql .= " AND (nombre LIKE ? OR descripcion LIKE ?)";
    $params[] = '%' . $buscar . '%';
    $params[] = '%' . $buscar . '%';
}

if ($categoria) {
    $sql .= " AND categoria = ?";
    $params[] = $categoria;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

$nombre_usuario = htmlspecialchars($_SESSION['user_name']);

// Obtener categorías para el filtro
$stmtCategorias = $pdo->query("SELECT DISTINCT categoria FROM productos");
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Productos</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon" >
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
        }
      
        .search-container form {
            display: flex;
            align-items: center;
        }
        .search-container input,
        .search-container select,
        .search-container button {
            margin-right: 0.5rem;
            padding: 0.5rem;
            border-radius: 0.4rem;
            border: 1px solid #ddd;
        }
        .search-container button {
            background-color: #795C34; /* Mismo color que los botones del menú */
            color: white;
            font-weight: bold;
            cursor: pointer;
        }
        .search-container button:hover {
            background-color: #65350f; /* Color al pasar el mouse */
        }

        .productos{
            width: 95%;
            margin:auto;
            margin-bottom: 30px;
        }
        .cart-icon {
            position: relative;
            display: inline-block;
            margin-left: 1rem;
            width: 70px; /* Ajustar el tamaño a 70px */
            height: 70px; /* Ajustar el tamaño a 70px */
            background-color: transparent; /* Quitar el color de fondo */
        }
        .cart-icon img {
            width: 100%;
            height: auto;
        }
        .cart-count {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: red;
            color: white;
            border-radius: 50%;
            padding: 0.2rem 0.5rem;
            font-size: 0.8rem;
        }

        /* Estilo para productos agotados */
        .agotado {
            color: red;
            font-weight: bold;
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
                </ul>
            </nav>
            <div class="search-container">
                <form action="consulta_productos.php" method="get">
                    <input type="text" name="buscar" placeholder="Buscar productos" value="<?php echo htmlspecialchars($buscar); ?>">
                    <select name="categoria">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat['categoria']); ?>" <?php echo $categoria == $cat['categoria'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($cat['categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btnmenu">Buscar</button>
                </form>
            </div>
        </div>
    </header>
    
    <main>
        <h1 class="page-title">Consulta de Productos</h1>
        <section class="productos">
            <div class="products-container">
                <?php if (empty($productos)): ?>
                    <p>No se encontraron productos con los criterios de búsqueda.</p>
                <?php else: ?>
                    <?php foreach ($productos as $producto): ?>
                        <div class="product">
                            <img src="images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p>Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <p>Valor: $<?php echo number_format($producto['precio'], 2); ?></p>
                                <p>Stock: <?php echo htmlspecialchars($producto['stock']); ?></p>
                                <p>Estado: <?php echo $producto['activo'] ? 'Activo' : '<span class="agotado">Agotado</span>'; ?></p> <!-- Cambio aquí -->
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
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

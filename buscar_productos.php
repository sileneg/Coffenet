<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Recibir los parámetros de búsqueda
$terminoBusqueda = isset($_GET['buscar']) ? $_GET['buscar'] : '';
$categoriaSeleccionada = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Consulta para buscar productos
$sql = "SELECT * FROM productos WHERE 1=1";

// Parámetros de búsqueda
$params = [];

if (!empty($terminoBusqueda)) {
    // Normalización del término de búsqueda (insensible a mayúsculas, minúsculas y tildes)
    $normalizedTermino = "LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(:termino, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'))";
    $sql .= " AND (LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(nombre, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) LIKE $normalizedTermino OR LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(categoria, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) LIKE $normalizedTermino)";
    $params[':termino'] = '%' . $terminoBusqueda . '%';
}

if (!empty($categoriaSeleccionada)) {
    // Normalización de la categoría seleccionada (insensible a mayúsculas, minúsculas y tildes)
    $sql .= " AND LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(categoria, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u')) = LOWER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(:categoria, 'á', 'a'), 'é', 'e'), 'í', 'i'), 'ó', 'o'), 'ú', 'u'))";
    $params[':categoria'] = $categoriaSeleccionada;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías de la base de datos para el formulario de búsqueda
$stmtCategorias = $pdo->query("SELECT DISTINCT categoria FROM productos");
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Definir si el usuario está autenticado
$autenticado = isset($_SESSION['user_level']);
$user_level = $autenticado ? $_SESSION['user_level'] : 0; // 0 para no autenticado
$nombre_usuario = $autenticado ? htmlspecialchars($_SESSION['user_name']) : '';

// Obtener el número de productos en el carrito
$numeroCarrito = isset($_SESSION['carrito']) ? count($_SESSION['carrito']) : 0;

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Productos</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
    .search-container {
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 1rem;
            margin-top: -1rem;
        }
        .header-bottom{
            margin-top: -3rem;
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
        .cart-icon {
            position: relative;
            display: inline-block;
            margin-left: 1rem;
            width: 70px; /* Ajustar el tamaño a 60px */
            height: 70px; /* Ajustar el tamaño a 60px */
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
        
        .back-button {
            margin-top: 1rem;
            margin-bottom: 1rem;
            padding: 0.5rem 1rem;
            background-color: #795C34; /* Mismo color que los botones del menú */
            color: white;
            font-weight: bold;
            border-radius: 0.4rem;
            text-decoration: none;
            text-align: center;
            display: inline-block;
            cursor: pointer;
        }
        .back-button:hover {
            background-color: #65350f; /* Color al pasar el mouse */
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
                    <li class="btnmenu"><a href="productos.php">Productos</a></li>
                    <?php if ($autenticado && ($user_level == 1 || $user_level == 2)): ?>
                        <li class="btnmenu"><a href="carrito.php">Carrito</a></li>
                        <li class="btnmenu"><a href="agregar_producto.php">Agregar Producto</a></li>
                    <?php endif; ?>
                    <li class="btnmenu"><a href="<?php echo $autenticado ? 'mis_compras.php' : 'login.php'; ?>">Mis Compras</a></li>
                    <?php if (!$autenticado || ($user_level != 1 && $user_level != 2)): ?>
                    <li class="cart-icon">
                        <a href="carrito.php">
                            <img src="images/carrito2.png" alt="Carrito">
                            <span class="cart-count"><?php echo $numeroCarrito; ?></span>
                        </a>
                    </li>
                </ul>
            </nav>
            <div class="search-container">
                <form action="buscar_productos.php" method="get">
                    <input type="text" name="buscar" placeholder="Buscar productos" value="<?php echo htmlspecialchars($terminoBusqueda); ?>">
                    <select name="categoria">
                        <option value="">Todas las Categorías</option>
                        <?php foreach ($categorias as $categoria): ?>
                            <option value="<?php echo htmlspecialchars($categoria['categoria']); ?>" <?php if ($categoriaSeleccionada == $categoria['categoria']) echo 'selected'; ?>>
                                <?php echo htmlspecialchars($categoria['categoria']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button type="submit" class="btnmenu">Buscar</button>
                </form>
            </div>
            <?php endif; ?>
        </div>
    </header>
    <main>
        <h1 class="page-title">Resultados de Búsqueda</h1>
        <section class="productos">
            <div class="products-container">
                <?php if (count($resultados) > 0): ?>
                    <?php foreach ($resultados as $producto): ?>
                        <div class="product">
                            <img src="images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                            <div class="product-info">
                                <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                                <p>Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                                <p>Valor: $<?php echo number_format($producto['precio'], 2); ?></p>
                                <?php if ($producto['activo']): ?>
                                    <?php if ($autenticado && ($user_level == 1 || $user_level == 2)): ?>
                                        <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="action-button">Editar</a>
                                        <a href="eliminar_producto.php?id=<?php echo $producto['id']; ?>" class="action-button" onclick="return confirm('¿Estás seguro de que deseas eliminar este producto?')">Eliminar</a>
                                    <?php else: ?>
                                        <form action="PHP/add_to_cart.php" method="post">
                                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                            <button type="submit" class="action-button">Agregar al carrito</button>
                                        </form>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <p style="color: red;">Producto Agotado</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No se encontraron productos con los criterios de búsqueda proporcionados.</p>
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

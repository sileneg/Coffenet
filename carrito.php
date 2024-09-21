<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Definir si el usuario está autenticado
$autenticado = isset($_SESSION['user_level']);
$nombre_usuario = $autenticado ? htmlspecialchars($_SESSION['user_name']) : '';

// Acción de agregar un producto al carrito
if (isset($_GET['id']) && isset($_GET['action']) && $_GET['action'] == 'add') {
    $product_id = $_GET['id'];

    // Obtener los detalles del producto desde la base de datos usando PDO
    $query = "SELECT nombre, imagen, precio FROM productos WHERE id = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $product_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);
        $nombre_del_producto = $producto['nombre'];
        $imagen_del_producto = $producto['imagen'];
        $precio_del_producto = $producto['precio'];
    } else {
        die("Error: Producto no encontrado.");
    }

    // Definir la cantidad a agregar
    $cantidad_a_agregar = 1;

    // Verificar si el carrito ya está en la sesión
    if (isset($_SESSION['carrito'])) {
        $carrito = $_SESSION['carrito'];
        $producto_encontrado = false;

        foreach ($carrito as &$item) {
            if ($item['id'] == $product_id) {
                // Si el producto ya existe en el carrito, aumentar la cantidad
                $item['cantidad'] += $cantidad_a_agregar;
                $producto_encontrado = true;
                break;
            }
        }

        if (!$producto_encontrado) {
            // Si el producto no se encontró en el carrito, agregarlo
            $carrito[] = [
                'id' => $product_id,
                'nombre' => $nombre_del_producto,
                'imagen' => $imagen_del_producto,
                'precio' => $precio_del_producto,
                'cantidad' => $cantidad_a_agregar,
            ];
        }
    } else {
        // Si el carrito está vacío, agregar el primer producto
        $carrito = [
            [
                'id' => $product_id,
                'nombre' => $nombre_del_producto,
                'imagen' => $imagen_del_producto,
                'precio' => $precio_del_producto,
                'cantidad' => $cantidad_a_agregar,
            ]
        ];
    }

    // Actualizar el carrito en la sesión
    $_SESSION['carrito'] = $carrito;
}

// Eliminar producto del carrito
if (isset($_GET['action']) && $_GET['action'] == 'remove' && isset($_GET['id'])) {
    $product_id = $_GET['id'];
    if (isset($_SESSION['carrito'])) {
        $carrito = $_SESSION['carrito'];
        foreach ($carrito as $key => $item) {
            if ($item['id'] == $product_id) {
                unset($carrito[$key]);
                $_SESSION['carrito'] = array_values($carrito); // Reindexar el carrito
                break;
            }
        }
    }
}

// Mostrar productos en el carrito
$carrito = isset($_SESSION['carrito']) ? $_SESSION['carrito'] : [];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <style>
        .productos {
            width: 95%;
            margin: auto;
            margin-bottom: 30px;
        }

        .products-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); /* Ajusta la cantidad de columnas a un máximo de 5 */
            gap: 1rem;
        }

        .product {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            background-color: #f9f9f9;
            max-width: 200px; /* Limita el ancho de cada producto */
            margin: 0 auto; /* Centra los productos */
        }

        .product img {
            width: 80px; /* Ajusta el tamaño de la imagen */
            height: auto;
            object-fit: contain;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }

        .product-info {
            text-align: center;
            font-size: 0.8rem; /* Texto más pequeño */
        }

        .product-info h3 {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .product-info p {
            margin: 0.25rem 0;
        }

        .buttons-container {
            text-align: center;
            margin-top: 2rem;
        }

        .btn-primary {
            display: inline-block;
            padding: 0.5rem 1rem;
            background-color: #231709; /* Color del encabezado */
            color: #fff;
            text-decoration: none;
            border: none;
            border-radius: 0.5rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-weight: bold;
            font-size: 0.9rem;
            transition: background-color 0.3s ease, color 0.3s ease;
            margin-top: 2rem;
        }

        .btn-primary:hover {
            background-color: #7a5c40; /* Color más oscuro para el hover */
            color: #f3dbc3; /* Color del texto para el hover */
        }

        .btn-danger {
            background-color: #dc3545;
            color: #fff;
            padding: 0.4rem 0.8rem;
            border-radius: 0.5rem;
            text-decoration: none;
            font-size: 0.8rem;
        }

        .btn-danger:hover {
            background-color: #c82333;
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
            </ul>
        </nav>
    </div>
</header>
<main>
    <h1 class="page-title">Carrito de Compras</h1>
    <section class="productos">
        <div class="products-container">
            <?php if (empty($carrito)): ?>
                <p>El carrito está vacío.</p>
            <?php else: ?>
                <?php foreach ($carrito as $id => $item): ?>
                    <div class="product">
                        <img src="images/<?php echo htmlspecialchars($item['imagen']); ?>" alt="<?php echo htmlspecialchars($item['nombre']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($item['nombre']); ?></h3>
                            <p><?php echo htmlspecialchars($item['descripcion']); ?></p>
                            <p>Cantidad: <?php echo htmlspecialchars($item['cantidad']); ?></p>
                            <p>Valor: $<?php echo number_format($item['precio'], 2); ?></p>
                            <p>Total: $<?php echo number_format($item['precio'] * $item['cantidad'], 2); ?></p>
                            <a href="carrito.php?action=remove&id=<?php echo $item['id']; ?>" class="btn btn-danger">Quitar</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <div class="buttons-container">
            <a href="confirmar_compra.php" class="btn btn-primary">Continuar Compra</a>
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

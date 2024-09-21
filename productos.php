<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Definir si el usuario está autenticado
$autenticado = isset($_SESSION['user_level']);
$user_level = $autenticado ? $_SESSION['user_level'] : 0; // 0 para no autenticado
$nombre_usuario = $autenticado ? htmlspecialchars($_SESSION['user_name']) : '';
$user_id = $autenticado ? $_SESSION['user_id'] : null; // ID del usuario autenticado

// Manejar la inserción de calificaciones
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['calificacion'])) {
    $producto_id = intval($_POST['producto_id']);
    $calificacion = intval($_POST['calificacion']);
    $comentario = htmlspecialchars($_POST['comentario']);

    if ($calificacion >= 1 && $calificacion <= 5 && $user_id) {
        // Verifica si el usuario ya ha calificado este producto
        $stmt = $pdo->prepare("SELECT * FROM calificaciones WHERE producto_id = ? AND usuario_id = ?");
        $stmt->execute([$producto_id, $user_id]);
        $calificacion_existente = $stmt->fetch();

        if ($calificacion_existente) {
            // Actualiza la calificación existente
            $stmt = $pdo->prepare("UPDATE calificaciones SET calificacion = ?, comentario = ? WHERE producto_id = ? AND usuario_id = ?");
            $stmt->execute([$calificacion, $comentario, $producto_id, $user_id]);
        } else {
            // Inserta una nueva calificación
            $stmt = $pdo->prepare("INSERT INTO calificaciones (producto_id, usuario_id, calificacion, comentario) VALUES (?, ?, ?, ?)");
            $stmt->execute([$producto_id, $user_id, $calificacion, $comentario]);
        }

        // Redirigir para evitar duplicación al recargar la página
        header("Location: productos.php#producto-$producto_id");
        exit();
    }
}

// Obtener todos los productos, ordenados por la cantidad de stock y estado
$stmt = $pdo->query("
    SELECT p.*, COALESCE(AVG(c.calificacion), 0) AS calificacion_promedio
    FROM productos p
    LEFT JOIN calificaciones c ON p.id = c.producto_id
    GROUP BY p.id
    ORDER BY p.activo DESC, p.stock DESC
");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías de la base de datos (mostrar todas las categorías incluso si no tienen productos activos)
$stmtCategorias = $pdo->query("SELECT DISTINCT categoria FROM productos");
$categorias = $stmtCategorias->fetchAll(PDO::FETCH_ASSOC);

// Obtener el número total de productos en el carrito
$numeroCarrito = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $numeroCarrito += $item['cantidad'];
    }
}

// Validación para agregar al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['producto_id'])) {
    $producto_id = intval($_POST['producto_id']);
    $stmt = $pdo->prepare("SELECT stock FROM productos WHERE id = ? AND activo = 1");
    $stmt->execute([$producto_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($producto && $producto['stock'] > 0) {
        // Proceder con la adición al carrito
        // Aquí iría el código que maneja la adición del producto al carrito
    } else {
        // Redirigir con un mensaje de error indicando que el producto está agotado
        header("Location: productos.php?error=producto_agotado");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
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

        .productos {
            width: 95%;
            margin: auto;
            margin-bottom: 30px;
        }
        .cart-icon {
            position: relative;
            display: inline-block;
            margin-left: 1rem;
            width: 70px;
            height: 70px;
            background-color: transparent;
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

        .agotado-text {
            color: red;
            font-weight: bold;
            margin-top: 10px;
        }

        .inactive-product .action-button.red-button {
            background-color: #dc3545; /* Botón rojo para desactivados */
            color: white;
        }

        .inactive-product img {
            opacity: 0.5; /* Imagen con menor opacidad para indicar que está inactivo */
        }

        /* Estilo para calificaciones */
        .product select {
            padding: 0.5rem;
            border: 0.1rem solid var(--color-secundario);
            border-radius: 0.4rem;
            margin-top: 0.5rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color: var(--color-primario);
            background-color: #fff;
            box-shadow: 0 0 0.8rem rgba(0, 0, 0, 0.1);
        }

        .product .action-button {
            margin-top: 0.5rem;
            display: inline-block;
            padding: 0.5rem 1rem;
            font-size: 1rem;
            background-color: var(--color-primario);
            color: #fff;
            border: none;
            border-radius: 0.4rem;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .product .action-button:hover {
            background-color: var(--color-secundario);
        }

        .product textarea {
            width: 100%;
            padding: 0.5rem;
            border-radius: 0.4rem;
            border: 0.1rem solid var(--color-secundario);
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color: var(--color-primario);
            background-color: #fff;
            box-shadow: 0 0 0.8rem rgba(0, 0, 0, 0.1);
            margin-top: 0.5rem;
            resize: vertical;
        }

        .calificacion-comentario {
            background-color: #f9f9f9;
            padding: 0.5rem;
            border-radius: 0.4rem;
            border: 1px solid #ddd;
            margin-top: 0.5rem;
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
        }
        .calificacion-comentario p {
            margin: 0;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }
        .calificacion-comentario strong {
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
                    <li class="btnmenu"><a href="agregar_producto.php">Agregar Producto</a></li>
                <?php elseif ($user_level == 2): ?>
                    <li class="btnmenu"><a href="propietario.php">Panel Propietario</a></li>
                    <li class="btnmenu"><a href="agregar_producto.php">Agregar Producto</a></li>
                <?php else: ?>
                    <li class="btnmenu"><a href="<?php echo $autenticado ? 'mis_compras.php' : 'login.php'; ?>">Mis Compras</a></li>
                    <li class="btnmenu"><a href="mis_calificaciones.php">Mis Calificaciones</a></li> <!-- Enlace para clientes -->
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
        <h1 class="page-title">Productos</h1>
        <section class="productos">
            <div class="products-container">
                <?php foreach ($productos as $producto): ?>
                    <?php 
                    // Actualizar el estado del producto a inactivo si el stock es 0
                    if ($producto['stock'] <= 0 && $producto['activo'] == 1) {
                        $stmt = $pdo->prepare("UPDATE productos SET activo = 0 WHERE id = ?");
                        $stmt->execute([$producto['id']]);
                        $producto['activo'] = 0;
                    }
                    ?>
                    <div class="product <?php echo !$producto['activo'] ? 'inactive-product' : ''; ?>">
                        <img src="images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                        
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                            <p>Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                            <p>Valor: $<?php echo number_format($producto['precio'], 2); ?></p>
                            <p>Calificación Promedio: <?php echo number_format($producto['calificacion_promedio'], 1); ?> / 5</p>
                            
                            <?php if ($user_level == 1 || $user_level == 2): ?>
                                <p>Stock: <?php echo htmlspecialchars($producto['stock']); ?></p>
                                <p>Estado: <?php echo $producto['activo'] ? 'Activo' : 'Inactivo'; ?></p>
                                <a href="editar_producto.php?id=<?php echo $producto['id']; ?>" class="action-button">Editar</a>
                                <a href="cambiar_estado_producto.php?id=<?php echo $producto['id']; ?>&estado=<?php echo $producto['activo'] ? '0' : '1'; ?>" class="action-button <?php echo !$producto['activo'] ? 'red-button' : ''; ?>">
                                    <?php echo $producto['activo'] ? 'Desactivar' : 'Activar'; ?>
                                </a>
                                <a href="ver_calificaciones.php?id=<?php echo $producto['id']; ?>" class="action-button">Ver Calificaciones</a>
                            <?php else: ?>
                                <?php if ($producto['activo'] && $producto['stock'] > 0): ?>
                                    <form action="PHP/add_to_cart.php" method="post">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                        <button type="submit" class="action-button">Agregar al carrito</button>
                                    </form>
                                    
                                    <!-- Formulario de calificación -->
                                    <form action="productos.php" method="POST">
                                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                                        <label for="calificacion">Califica este producto:</label>
                                        <select name="calificacion" required>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                        </select>
                                        <br>
                                        <label for="comentario">Escribe un comentario:</label>
                                        <textarea name="comentario" rows="4" placeholder="Escribe tu opinión sobre este producto..." required></textarea>
                                        <button type="submit" class="action-button">Enviar Calificación</button>
                                    </form>

                                <?php elseif ($producto['activo'] && $producto['stock'] <= 0): ?>
                                    <p class="agotado-text">Producto Agotado</p>
                                <?php else: ?>
                                    <p class="agotado-text">Producto No Disponible</p>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
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

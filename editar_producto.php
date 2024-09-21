<?php
session_start();
include 'db/connection.php'; // Conexión a la base de datos

// Verificar que el usuario tiene permiso para editar productos
if ($_SESSION['user_level'] != 1 && $_SESSION['user_level'] != 2) {
    echo "<script>alert('No tienes permisos para editar productos.'); window.location.href = 'productos.php';</script>";
    exit();
}

// Verificar que el ID del producto fue proporcionado
if (!isset($_GET['id'])) {
    echo "<script>alert('Producto no encontrado.'); window.location.href = 'productos.php';</script>";
    exit();
}

$product_id = $_GET['id'];

try {
    // Obtener el producto de la base de datos
    $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
    $stmt->execute([$product_id]);
    $producto = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$producto) {
        echo "<script>alert('Producto no encontrado.'); window.location.href = 'productos.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $categoria = $_POST['categoria'];
    $stock = $_POST['stock'];

    // Manejo de imagen
    $imagen = $producto['imagen']; // Mantener la imagen actual por defecto
    if (!empty($_FILES['imagen']['name'])) {
        $imagen = $_FILES['imagen']['name'];
        $target_dir = "images/";
        $target_file = $target_dir . basename($imagen);

        if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
            echo "<script>alert('Error al subir la imagen.'); window.history.back();</script>";
            exit();
        }
    }

    // Actualizar el producto en la base de datos
    try {
        $stmt = $pdo->prepare("UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, categoria = ?, imagen = ?, stock = ? WHERE id = ?");
        $stmt->execute([$nombre, $descripcion, $precio, $categoria, $imagen, $stock, $product_id]);
        echo "<script>alert('Producto actualizado exitosamente.'); window.location.href = 'productos.php';</script>";
    } catch (PDOException $e) {
        echo "<script>alert('Error al actualizar el producto: {$e->getMessage()}'); window.history.back();</script>";
    }
}

$nombre_usuario = htmlspecialchars($_SESSION['user_name']);
$user_level = $_SESSION['user_level'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Producto</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .admin-info {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            margin-bottom: 2rem;
            margin-top: 30px;
            padding: 20px;
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
            border: 2px solid #795C34;
            border-radius: 10px;
            background-color: #fff;
            width: 50%;
            margin: 0 auto;
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
        }

        .form-group label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
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
        }

        .btn-primary:hover {
            background-color: #65350f;
            color:#fff;
            border: 4px solid #9a7b47;
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
                <a href="php/logout.php">Cerrar Sesión</a>
            </div>
        </div>

        <div class="header-bottom">
            <nav class="nav">
                <ul>
                    <li class="btnmenu"><a href="index.php">Inicio</a></li>
                    <li class="btnmenu"><a href="carrito.php">Carrito</a></li>
                    <li class="btnmenu">
                        <a href="<?php echo ($user_level == 1) ? 'admin.php' : 'propietario.php'; ?>">
                            <?php echo ($user_level == 1) ? 'Panel Admin' : 'Panel Propietario'; ?>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main>
        <h1 class="page-title">Editar Producto</h1>
        <div class="admin-info">
            <div class="info-box">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombre">Nombre del Producto:</label>
                        <input type="text" name="nombre" id="nombre" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción:</label>
                        <textarea name="descripcion" id="descripcion" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="precio">Precio:</label>
                        <input type="number" name="precio" id="precio" step="0.01" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="categoria">Categoría:</label>
                        <input type="text" name="categoria" id="categoria" value="<?php echo htmlspecialchars($producto['categoria']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="imagen">Imagen del Producto:</label>
                        <input type="file" name="imagen" id="imagen" accept="image/*">
                        <p>Imagen actual: <?php echo htmlspecialchars($producto['imagen']); ?></p>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock:</label>
                        <input type="number" name="stock" id="stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Producto</button>
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

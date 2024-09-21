<?php
session_start();
include 'db/connection.php'; // Asegúrate de que la ruta al archivo de conexión es correcta

// Definir si el usuario está autenticado
$autenticado = isset($_SESSION['user_name']);
$nombre_usuario = $autenticado ? htmlspecialchars($_SESSION['user_name']) : '';

// Manejar la eliminación de comentarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar_comentario'])) {
    $comentario_id = intval($_POST['comentario_id']);
    
    // Verifica que el usuario tenga el nivel de acceso adecuado
    if (isset($_SESSION['user_level']) && ($_SESSION['user_level'] == 1 || $_SESSION['user_level'] == 2)) {
        $stmt = $pdo->prepare("DELETE FROM comentarios WHERE id = ?");
        $stmt->execute([$comentario_id]);

        // Redirigir para evitar que el formulario se reenvíe al recargar la página
        header("Location: index.php#clientes");
        exit();
    }
}

// Manejar la inserción de respuestas a comentarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['respuesta'])) {
    $comentario_id = intval($_POST['comentario_id']);
    $respuesta = htmlspecialchars($_POST['respuesta']);
    $nombre_usuario = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Administrador';
    
    $stmt = $pdo->prepare("INSERT INTO comentarios (nombre_usuario, comentario, parent_id) VALUES (?, ?, ?)");
    $stmt->execute([$nombre_usuario, $respuesta, $comentario_id]);

    // Redirigir para evitar duplicación al recargar la página
    header("Location: index.php#clientes");
    exit();
}

// Manejar la inserción de comentarios
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comentario'])) {
    if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 4) { // Clientes tienen el nivel 4
        $nombre_usuario = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Anónimo';
        $comentario = htmlspecialchars($_POST['comentario']);

        $stmt = $pdo->prepare("INSERT INTO comentarios (nombre_usuario, comentario) VALUES (?, ?)");
        $stmt->execute([$nombre_usuario, $comentario]);

        // Redirigir para evitar duplicación al recargar la página
        header("Location: index.php#clientes");
        exit();
    }
}

// Obtener los últimos 5 productos
$stmt = $pdo->query("SELECT * FROM productos LIMIT 5");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener los comentarios
$stmtComentarios = $pdo->query("SELECT * FROM comentarios ORDER BY fecha DESC");
$comentarios = $stmtComentarios->fetchAll(PDO::FETCH_ASSOC);

// Obtener los últimos 5 productos con su calificación promedio y el número de calificaciones
$stmt = $pdo->query("
    SELECT p.*, 
           COALESCE(AVG(c.calificacion), 0) AS calificacion_promedio,
           COUNT(c.calificacion) AS numero_calificaciones
    FROM productos p
    LEFT JOIN calificaciones c ON p.id = c.producto_id
    GROUP BY p.id
    LIMIT 5
");
$productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CoffeNet</title>
    <link rel="shortcut icon" href="images/logo.jpg" type="image/x-icon">
    <link href="CSS/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="CSS/reset.css">
    <link rel="stylesheet" href="CSS/styles.css">
    <script src="JS/script.js" defer></script>
    <style>
        :root {
            --color-fondo: #f9f9f9;
            --color-primario: #231709;
            --color-secundario: #795C34;
            --color-terciario: #65350f;
            --color-cuaternario: #d1b6a8;
            --color-quinto: #80471C;
        }
        /* Estilos adicionales para el menú desplegable */
        .dropdown {
            position: relative;
            display: inline-block;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {background-color: #f1f1f1}

        .dropdown:hover .dropdown-content {
            display: block;
        }

        .dropdown:hover .dropbtn {
            background-color: #3e8e41;
        }

        .clients-container {
            background-color: var(--color-cuaternario);
            padding: 1rem;
            border: 3px solid var(--color-primario);
            border-radius: 0.8rem;
            box-shadow: 0 0 0.8rem rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .client {
            background-color: white;
            padding: 1rem;
            border-radius: 0.4rem;
            margin-bottom: 1rem;
            box-shadow: 0 0 0.5rem rgba(0, 0, 0, 0.1);
        }

        .client p {
            margin: 0.5rem 0;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
        }

        .client strong {
            font-weight: bold;
            font-size: 1.1rem;
            color: var(--color-primario);
        }

        .client small {
            color: var(--color-secundario);
            font-size: 0.9rem;
        }

        .comment-form {
            margin-top: 2rem;
            padding: 1rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            border: 3px solid var(--color-primario);
            color: var(--color-primario);
            background-color: var(--color-cuaternario);
            border-radius: 0.8rem;
            box-shadow: 0 0 0.8rem rgba(0, 0, 0, 0.1);
        }

        .comment-form label {
            font-weight: bold;
            color: var(--color-primario);
            font-size: 1.2rem;
        }

        .comment-form textarea {
            width: 100%;
            padding: 0.5rem;
            border: 0.1rem solid var(--color-secundario);
            border-radius: 0.4rem;
            margin-bottom: 1rem;
        }

        .comment-form button {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--color-primario);
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 0.4rem;
            cursor: pointer;
        }

        .comment-form button:hover {
            background-color: var(--color-secundario);
        }

        .action-button {
            background-color: var(--color-primario);
            color: white;
            font-weight: bold;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 0.4rem;
            cursor: pointer;
            margin-top: 0.5rem;
        }

        .action-button:hover {
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            background-color: var(--color-secundario);
        }

        .comment-form button {
            width: auto;
            padding: 0.75rem 2rem;
            background-color: var(--color-primario);
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            color: white;
            font-weight: bold;
            border: none;
            border-radius: 0.4rem;
            cursor: pointer;
            display: block;
            margin: 1rem auto 0 auto; /* Centra el botón */
        }

        /* Estilo para el textarea de respuestas */
        .client textarea {
            width: 100%;
            padding: 0.5rem;
            border: 0.1rem solid var(--color-secundario);
            border-radius: 0.4rem;
            margin-top: 0.5rem;
            font-family: 'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
            font-size: 1rem;
            color: var(--color-primario);
            background-color: #fff;
            box-shadow: 0 0 0.8rem rgba(0, 0, 0, 0.1);
            transition: border-color 0.3s ease;
        }

        .client textarea:focus {
            border-color: var(--color-primario);
            outline: none;
        }

        .client .action-button {
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

        .client .action-button:hover {
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
                    <li class="btnmenu"><a href="#sobre-nosotros">Sobre Nosotros</a></li>
                    <li class="btnmenu"><a href="#productos">Productos</a></li>
                    <li class="btnmenu"><a href="#clientes">Clientes</a></li>
                    <li class="btnmenu"><a href="#nuestro_equipo">Nuestro Equipo</a></li>
                    <li class="btnmenu"><a href="#footer-content">Contáctenos</a></li>
                    
                   
                        <li class="btnmenu dropdown">
                            <a href="javascript:void(0)" class="dropbtn">Acceso</a>
                            <div class="dropdown-content">
                                <a href="login.html">Iniciar sesión</a>
                                <a href="registro_usuarios.html">Registrarse</a>
                                <a href="recuperar_contrasena.html">Olvidé mi contraseña</a>
                            </div>
                        </li>
                </ul>
            </nav>
        </div>

    </header>

    <main>
        <div class="carousel-container">
            <div class="carousel">
                <div class="carousel-slide">
                    <img src="images/localfuera.jpg" alt="Imagen 1">
                    <div class="carousel-caption">
                        <h3>Ubicación</h3>
                        <p>La Papelería se encuentra ubicada en Fresno, Tolima, Colombia</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="images/localdentro2.jpg" alt="Imagen 2">
                    <div class="carousel-caption">
                        <h3>Impresiones</h3>
                        <p>Contamos con servicio de impresion</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="images/localdentro3.jpg" alt="Imagen 3">
                    <div class="carousel-caption">
                        <h3>Papelería - Dispositivos Tecnológicos</h3>
                        <p>Tenemos todo lo relacionado con la papelería y algunos dispositivos tecnológicos</p>
                    </div>
                </div>
                <div class="carousel-slide">
                    <img src="images/localdentro1.jpg" alt="Imagen 4">
                    <div class="carousel-caption">
                        <h3>Internet</h3>
                        <p>Contamos con servicio de internet</p>
                    </div>
                </div>
            </div>
             <button class="prev" onclick="moveSlide(-1)">&#10094;</button>
            <button class="next" onclick="moveSlide(1)">&#10095;</button>
        </div>

        <section class="sobre-nosotros" id="sobre-nosotros">
            <h2>Sobre Nosotros</h2>
            <div class="content">
                <div class="image-container">
                    <img src="images/localfuera.jpg" alt="Imagen sobre nosotros">
                </div>
                <div class="text-container">
                    <p>
                        Nosotros nos caracterizamos por ser una empresa de papelería y de café internet para todo 
                        público, gracias a nuestro sistemas claro de organización de productos y por la forma de 
                        ofrecer nuestros servicios que van desde la venta de implementos académicos hasta elementos de 
                        computadoras y nuestro servicio de internet en nuestras computadores de nuestro establecimiento 
                        físico, esperamos que estés teniendo un buen día, y si no te olvides de comprar a los mejores 
                        precios y calidad en nuestras instalaciones físicas y en línea,¡BUENDÍA!.                    
                    </p>
                </div>
            </div>
           
        </section>      

        <section class="productos" id="productos">
    <h2>Productos</h2>
    <div class="products-container">
        <?php foreach ($productos as $producto): ?>
            <div class="product">
                <img src="images/<?php echo htmlspecialchars($producto['imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre']); ?>">
                <div class="product-info">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p>Descripción: <?php echo htmlspecialchars($producto['descripcion']); ?></p>
                    <p>Valor: $<?php echo number_format($producto['precio'], 2); ?></p>
                    <?php if ($producto['numero_calificaciones'] > 0): ?>
                        <p>Calificación: <?php echo number_format($producto['calificacion_promedio'], 1); ?> / 5</p>
                        <p>(<?php echo $producto['numero_calificaciones']; ?> personas han calificado)</p>
                    <?php endif; ?>
                    <?php if ($producto['activo']): ?>
                        <form action="PHP/add_to_cart.php" method="post">
                            <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                            <button type="submit" class="action-button">Agregar al carrito</button>
                        </form>
                    <?php else: ?>
                        <p style="color: red;">Producto Agotado</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <div class="more-images">
        <a href="productos.php">Más Productos</a>
    </div>
</section>

        <!-- Sección Clientes -->
        <section class="clientes" id="clientes">
            <h2>Clientes</h2>

            <!-- Mostrar comentarios existentes -->
            <div class="clients-container">
                    <?php foreach ($comentarios as $comentario): ?>
                    <?php if ($comentario['parent_id'] == null): // Solo mostrar comentarios principales ?>
                    <div class="client">
                        <p><strong><?php echo htmlspecialchars($comentario['nombre_usuario']); ?></strong></p>
                        <p><small><?php echo date('d/m/Y \a \l\a\s H:i', strtotime($comentario['fecha'])); ?></small></p>
                        <p><?php echo htmlspecialchars($comentario['comentario']); ?></p>
                        <?php if (isset($_SESSION['user_level']) && ($_SESSION['user_level'] == 1 || $_SESSION['user_level'] == 2)): ?>
                            <form action="index.php#clientes" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este comentario?');">
                                <input type="hidden" name="comentario_id" value="<?php echo $comentario['id']; ?>">
                                <button type="submit" name="eliminar_comentario" class="action-button">Eliminar</button>
                            </form>
                            <form action="index.php#clientes" method="POST">
                                <textarea name="respuesta" placeholder="Escribe tu respuesta" required></textarea>
                                <input type="hidden" name="comentario_id" value="<?php echo $comentario['id']; ?>">
                                <button type="submit" class="action-button">Responder</button>
                            </form>
                        <?php endif; ?>

                        <!-- Mostrar respuestas -->
                        <?php
                        $stmtRespuestas = $pdo->prepare("SELECT * FROM comentarios WHERE parent_id = ?");
                        $stmtRespuestas->execute([$comentario['id']]);
                        $respuestas = $stmtRespuestas->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($respuestas as $respuesta): ?>
                            <div class="response">
                                <p><strong><?php echo htmlspecialchars($respuesta['nombre_usuario']); ?></strong></p>
                                <p><small><?php echo date('d/m/Y \a \l\a\s H:i', strtotime($respuesta['fecha'])); ?></small></p>
                                <p><?php echo htmlspecialchars($respuesta['comentario']); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- Formulario para enviar comentarios -->
                <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] == 4): ?>
                <form action="index.php#clientes" method="POST" class="comment-form">
                    <label for="comentario">Deja tu comentario:</label>
                    <textarea id="comentario" name="comentario" rows="4" required></textarea>
                    <button type="submit" class="action-button">Enviar Comentario</button>
                </form>
                <?php endif; ?>
        </section>

        <!-- Sección Nuestro Equipo -->
        <section class="equipo" id="nuestro_equipo">
            <h2>Nuestro Equipo</h2>
            <div class="team-container">
                <div class="team-member">
                    <img src="images/propietaria.jpg" alt="Miembro del Equipo 1">
                    <div class="member-info">
                        <p>Claudia Patricia Carrillo Reyes  - Propietaria</p>
                    </div>
                </div>
            </div>
        </section>

    </main>
    <!-- Footer -->
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

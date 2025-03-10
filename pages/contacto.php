<?php
// Inicializar mensaje de éxito/error
$mensaje_exito = '';
$mensaje_error = '';

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexión a la base de datos
    $host = 'localhost';
    $db_name = 'compuya_db';
    $username = 'root';
    $password = '';
    
    // Crear conexión
    $conn = new mysqli($host, $username, $password, $db_name);
    
    // Verificar conexión
    if ($conn->connect_error) {
        $mensaje_error = "Error de conexión a la base de datos";
    } else {
        // Recoger y sanitizar datos
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $phone = $conn->real_escape_string($_POST['phone'] ?? '');
        $subject = $conn->real_escape_string($_POST['subject'] ?? '');
        $message = $conn->real_escape_string($_POST['message'] ?? '');
        
        // Validar datos esenciales
        if (!empty($name) && !empty($email) && !empty($subject) && !empty($message)) {
            // Preparar consulta SQL
            $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
                    VALUES ('$name', '$email', '$phone', '$subject', '$message')";
            
            // Ejecutar consulta
            if ($conn->query($sql) === TRUE) {
                $mensaje_exito = "¡Gracias por tu mensaje! Te contactaremos pronto.";
            } else {
                $mensaje_error = "Error al guardar el mensaje: " . $conn->error;
            }
        } else {
            $mensaje_error = "Por favor, completa todos los campos obligatorios.";
        }
        
        // Cerrar conexión
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Contacta con COMPU YA para cualquier consulta sobre nuestros productos y servicios. Ubicados en Av. Uruguay 375, Lima.">
    <title>Contacto | COMPU YA</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/styles.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .alert-danger {
            background-color: #fee2e2;
            color: #b91c1c;
        }
        .contact-section {
            padding: var(--spacing-xxl) 0;
        }
        .contact-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-xl);
        }
        .contact-info {
            background-color: white;
            padding: var(--spacing-xl);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-sm);
        }
        .info-item {
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-lg);
        }
        .info-icon {
            color: var(--primary-color);
            font-size: 1.5rem;
            flex-shrink: 0;
            width: 24px;
            text-align: center;
        }
        .info-content h4 {
            margin-bottom: var(--spacing-xs);
            font-size: 1.1rem;
        }
        .info-content p {
            color: var(--text-light);
            line-height: 1.5;
        }
        .contact-map {
            border-radius: var(--border-radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
            height: 100%;
            min-height: 300px;
        }
        .contact-form {
            background-color: white;
            padding: var(--spacing-xl);
            border-radius: var(--border-radius-md);
            box-shadow: var(--shadow-sm);
        }
        .contact-form h3 {
            color: var(--primary-color);
            margin-bottom: var(--spacing-md);
            font-size: 1.5rem;
        }
        .form-group {
            margin-bottom: var(--spacing-md);
        }
        .form-label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid var(--border-color);
            border-radius: var(--border-radius-sm);
            font-family: 'Roboto', sans-serif;
            font-size: 1rem;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        textarea.form-control {
            resize: vertical;
            min-height: 150px;
        }
        .form-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
            font-weight: 500;
            border-radius: var(--border-radius-md);
            cursor: pointer;
            transition: background-color var(--transition-fast);
        }
        .form-submit:hover {
            background-color: var(--primary-dark);
        }
        .business-hours {
            margin-top: var(--spacing-xl);
        }
        .hours-grid {
            display: grid;
            grid-template-columns: auto 1fr;
            gap: var(--spacing-sm) var(--spacing-lg);
        }
        .day {
            font-weight: 500;
        }
        .time {
            color: var(--text-light);
        }
        @media (max-width: 768px) {
            .contact-container {
                grid-template-columns: 1fr;
            }
            .contact-map {
                order: -1;
                margin-bottom: var(--spacing-lg);
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../index.html">
                        <h1>COMPU YA</h1>
                    </a>
                </div>
                <div class="search-bar">
                    <form action="#" method="GET">
                        <input type="text" placeholder="Buscar productos...">
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="user-actions">
                    <a href="carrito.html" class="cart-link"><i class="fas fa-shopping-cart"></i> Carrito <span class="cart-count" id="headerCartCount">0</span></a>
                </div>
                <button class="menu-toggle" id="menuToggle">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Navegación -->
    <nav class="main-nav">
        <div class="container">
            <ul class="nav-list">
                <li class="nav-item"><a href="../index.html">Inicio</a></li>
                <li class="nav-item dropdown">
                    <a href="#" class="dropdown-toggle">Productos <i class="fas fa-chevron-down"></i></a>
                    <ul class="dropdown-menu">
                        <li><a href="productos.html?categoria=perifericos">Periféricos</a></li>
                        <li><a href="productos.html?categoria=monitores">Monitores</a></li>
                        <li><a href="productos.html?categoria=laptops">Laptops</a></li>
                        <li><a href="productos.html?categoria=impresoras">Impresoras</a></li>
                        <li class="dropdown-submenu">
                            <a href="productos.html?categoria=componentes" class="dropdown-toggle">Componentes <i class="fas fa-chevron-right"></i></a>
                            <ul class="dropdown-menu submenu">
                                <li><a href="productos.html?categoria=componentes&subcategoria=cases">Cases</a></li>
                                <li><a href="productos.html?categoria=componentes&subcategoria=procesadores">Procesadores</a></li>
                                <li><a href="productos.html?categoria=componentes&subcategoria=tarjetas">Tarjetas</a></li>
                                <li><a href="productos.html?categoria=componentes&subcategoria=placas">Placas</a></li>
                            </ul>
                        </li>
                        <li><a href="productos.html?categoria=pcs-armadas">PCs Armadas</a></li>
                    </ul>
                </li>
                <li class="nav-item"><a href="nosotros.html">Nosotros</a></li>
                <li class="nav-item"><a href="contacto.php" class="active">Contacto</a></li>
            </ul>
        </div>
    </nav>

    <!-- Banner de la página -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <h2>Contacto</h2>
                <p>Estamos aquí para ayudarte. Contáctanos para resolver cualquier duda o solicitud.</p>
            </div>
        </div>
    </section>

    <!-- Sección de Contacto -->
    <section class="contact-section">
        <div class="container">
            <!-- Información de contacto y mapa -->
            <div class="contact-container">
                <div class="contact-info">
                    <!-- Mostrar mensajes de éxito o error -->
                    <?php if (!empty($mensaje_exito)): ?>
                        <div class="alert alert-success">
                            <?php echo $mensaje_exito; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($mensaje_error)): ?>
                        <div class="alert alert-danger">
                            <?php echo $mensaje_error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <h3>Información de Contacto</h3>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="info-content">
                            <h4>Dirección</h4>
                            <p>Av. Uruguay 375, Lima, Perú</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="info-content">
                            <h4>Teléfonos</h4>
                            <p>960 387918 / 980961060</p>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="info-content">
                            <h4>Email</h4>
                            <p>info@compuya.com</p>
                        </div>
                    </div>
                    
                    <div class="business-hours">
                        <h4>Horario de Atención</h4>
                        <div class="hours-grid">
                            <div class="day">Lunes - Sabado:</div>
                            <div class="time">10:00 AM - 8:00 PM</div>
                            
                            <div class="day">Domingos:</div>
                            <div class="time">Cerrado</div>
                        </div>
                    </div>
                </div>
                
                <div class="contact-map">
                    <!-- Iframe de Google Maps (reemplazar con la ubicación correcta) -->
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3901.7805401711146!2d-77.03493318516866!3d-12.056982391451843!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9105c8c5d6dec345%3A0x84c1dd91b329089c!2sAv.%20Uruguay%20375%2C%20Lima%2015001%2C%20Per%C3%BA!5e0!3m2!1ses!2s!4v1678054098765!5m2!1ses!2s" width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
            
            <!-- Formulario de contacto -->
            <div class="contact-form">
                <h3>Envíanos un Mensaje</h3>
                <form id="contactForm" action="contacto.php" method="POST">
                    <div class="form-group">
                        <label for="name" class="form-label">Nombre completo *</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email" class="form-label">Correo electrónico *</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="phone" class="form-label">Teléfono</label>
                        <input type="tel" id="phone" name="phone" class="form-control">
                    </div>
                    
                    <div class="form-group">
                        <label for="subject" class="form-label">Asunto *</label>
                        <input type="text" id="subject" name="subject" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">Mensaje *</label>
                        <textarea id="message" name="message" class="form-control" required></textarea>
                    </div>
                    
                    <button type="submit" class="form-submit">Enviar Mensaje</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-column">
                    <h3>Categorías</h3>
                    <ul>
                        <li><a href="productos.html?categoria=perifericos">Periféricos</a></li>
                        <li><a href="productos.html?categoria=monitores">Monitores</a></li>
                        <li><a href="productos.html?categoria=laptops">Laptops</a></li>
                        <li><a href="productos.html?categoria=impresoras">Impresoras</a></li>
                        <li><a href="productos.html?categoria=componentes">Componentes</a></li>
                        <li><a href="productos.html?categoria=pcs-armadas">PCs Armadas</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Información</h3>
                    <ul>
                        <li><a href="nosotros.html">Sobre nosotros</a></li>
                        <li><a href="envios.html">Envíos y devoluciones</a></li>
                        <li><a href="privacidad.html">Política de privacidad</a></li>
                        <li><a href="terminos.html">Términos y condiciones</a></li>
                        <li><a href="contacto.php">Contacto</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Compras</h3>
                    <ul>
                        <li><a href="carrito.html">Carrito de compras</a></li>
                        <li><a href="#">Proceso de compra</a></li>
                        <li><a href="#">Seguimiento de pedidos</a></li>
                        <li><a href="#">Métodos de pago</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h3>Contacto</h3>
                    <p><i class="fas fa-map-marker-alt"></i> Av. Uruguay 375, Lima</p>
                    <p><i class="fas fa-phone"></i> 960 387918 / 980961060</p>
                    <p><i class="fas fa-envelope"></i> info@compuya.com</p>
                    <div class="social-media">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2025 COMPU YA. Todos los derechos reservados.</p>
                <div class="payment-methods">
                    <i class="fab fa-cc-visa"></i>
                    <i class="fab fa-cc-mastercard"></i>
                    <i class="fab fa-cc-amex"></i>
                    <i class="fab fa-cc-paypal"></i>
                    <i class="fab fa-mercado-pago"></i>
                </div>
            </div>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
    // Función para añadir al carrito
    function addToCart(productName, productBrand, productPrice, productImage, quantity = 1) {
        // Obtener el carrito actual de localStorage
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        
        // Verificar si el producto ya está en el carrito
        const existingProductIndex = cart.findIndex(item => 
            item.name === productName && item.brand === productBrand
        );
        
        if (existingProductIndex !== -1) {
            // Si ya existe, aumentar la cantidad
            cart[existingProductIndex].quantity += quantity;
        } else {
            // Si no existe, añadir nuevo item
            cart.push({
                name: productName,
                brand: productBrand,
                price: productPrice,
                image: productImage,
                quantity: quantity
            });
        }
        
        // Guardar carrito actualizado
        localStorage.setItem('cart', JSON.stringify(cart));
        
        // Disparar evento de almacenamiento para sincronizar entre pestañas
        window.dispatchEvent(new Event('storage'));
        
        // Mostrar mensaje de confirmación
        alert(`Se ha añadido "${productName}" al carrito`);
    }

    // Función para actualizar el contador del carrito
    function updateCartCount() {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        const count = cart.reduce((total, item) => total + item.quantity, 0);
        
        // Actualizar todos los elementos con clase cart-count
        const cartCountElements = document.querySelectorAll('.cart-count');
        cartCountElements.forEach(element => {
            element.textContent = count;
        });
    }

    // Escuchar cambios en localStorage
    window.addEventListener('storage', function(e) {
        updateCartCount();
    });

    // Actualizar contador al cargar la página
    document.addEventListener('DOMContentLoaded', updateCartCount);
</script>
</body>
</html>
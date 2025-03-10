<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inicializar variables
$mensaje_exito = '';
$mensaje_error = '';

// Verificar si el formulario fue enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conectar a la base de datos directamente
    $host = 'localhost';
    $db_name = 'compuya_db';
    $username = 'root';
    $password = '';
    
    // Crear conexión
    $conn = new mysqli($host, $username, $password, $db_name);
    
    // Verificar conexión
    if ($conn->connect_error) {
        $mensaje_error = "Error de conexión: " . $conn->connect_error;
    } else {
        // Recoger y escapar los datos
        $name = $conn->real_escape_string($_POST['name'] ?? '');
        $email = $conn->real_escape_string($_POST['email'] ?? '');
        $phone = $conn->real_escape_string($_POST['phone'] ?? '');
        $subject = $conn->real_escape_string($_POST['subject'] ?? '');
        $message = $conn->real_escape_string($_POST['message'] ?? '');
        
        // Solo intentar insertar si se recibieron los datos requeridos
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
            $mensaje_error = "Por favor, completa todos los campos requeridos.";
        }
        
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto | COMPU YA</title>
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/styles.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!-- Font Awesome para iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .contact-section {
            padding: 40px 0;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 15px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .form-submit {
            background-color: #2563eb;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .message-success {
            background-color: #d1fae5;
            color: #065f46;
        }
        .message-error {
            background-color: #fee2e2;
            color: #b91c1c;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="index.html">COMPU YA</a></h1>
            <nav>
                <ul>
                    <li><a href="index.html">Inicio</a></li>
                    <li><a href="#">Productos</a></li>
                    <li><a href="contacto_directo.php">Contacto</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="contact-section">
        <div class="container">
            <h2>Contacto</h2>
            
            <?php if (!empty($mensaje_exito)): ?>
                <div class="message message-success">
                    <?php echo $mensaje_exito; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($mensaje_error)): ?>
                <div class="message message-error">
                    <?php echo $mensaje_error; ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="contacto_directo.php">
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
                    <textarea id="message" name="message" class="form-control" rows="5" required></textarea>
                </div>
                
                <button type="submit" class="form-submit">Enviar Mensaje</button>
            </form>
            
            <div style="margin-top: 20px;">
                <a href="index.html">← Volver al inicio</a>
            </div>
        </div>
    </section>
</body>
</html>
<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Parámetros de conexión a la base de datos
$host = 'localhost';
$db_name = 'compuya_db';
$username = 'root';
$password = '';

// Crear conexión
$conn = new mysqli($host, $username, $password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8");

// Verificar si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger y sanitizar datos del formulario
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
            // Redireccionar con mensaje de éxito
            header("Location: pages/contacto.php?status=success");
        } else {
            // Redireccionar con error
            header("Location: pages/contacto.php?status=error&message=" . urlencode($conn->error));
        }
    } else {
        // Redireccionar con error de validación
        header("Location: pages/contacto.php?status=error&message=campos_incompletos");
    }
    
    $conn->close();
    exit;
}
?>
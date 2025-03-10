<?php
// Configuración para mostrar errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Parámetros de conexión
$host = 'localhost';
$db_name = 'compuya_db';
$username = 'root';
$password = '';

echo "Iniciando prueba...<br>";

// Crear conexión
$conn = new mysqli($host, $username, $password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
} 

echo "Conexión exitosa a la base de datos.<br>";

// Verificar si la tabla existe
$result = $conn->query("SHOW TABLES LIKE 'contact_messages'");
if ($result->num_rows == 0) {
    echo "La tabla 'contact_messages' no existe.<br>";
} else {
    echo "La tabla 'contact_messages' existe.<br>";
    
    // Insertar un mensaje de prueba
    $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) 
            VALUES ('Usuario Prueba', 'test@ejemplo.com', '123456789', 'Mensaje de prueba', 'Este es un mensaje de prueba insertado directamente.')";
    
    if ($conn->query($sql) === TRUE) {
        echo "Mensaje de prueba insertado correctamente con ID: " . $conn->insert_id;
    } else {
        echo "Error al insertar mensaje: " . $conn->error;
    }
}

$conn->close();
?>
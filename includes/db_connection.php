<?php
// Parámetros de conexión a la base de datos
$host = 'localhost';
$db_name = 'compuya_db';
$username = 'root';
$password = ''; // Por defecto está vacío en XAMPP

// Crear conexión
$conn = new mysqli($host, $username, $password, $db_name);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Establecer charset
$conn->set_charset("utf8");
?>
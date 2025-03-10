<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Verificar si se proporcionaron parámetros necesarios
if (!isset($_GET['id']) || empty($_GET['id']) || !isset($_GET['status']) || empty($_GET['status'])) {
    header("Location: index.php");
    exit;
}

// Incluir conexión a la base de datos
require_once '../includes/db_connection.php';

// Obtener parámetros
$message_id = (int)$_GET['id'];
$status = $_GET['status'];

// Validar estado
if (!in_array($status, ['read', 'replied'])) {
    header("Location: index.php");
    exit;
}

// Actualizar estado
$sql = "UPDATE contact_messages SET status = '$status' WHERE id = $message_id";
$conn->query($sql);

// Redireccionar
if (isset($_GET['redirect']) && $_GET['redirect'] === 'view') {
    header("Location: view_message.php?id=$message_id");
} else {
    header("Location: index.php");
}
exit;
?>
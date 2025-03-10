<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: index.php");
    exit;
}

// Verificar si se proporcionó un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php");
    exit;
}

// Incluir conexión a la base de datos
require_once '../includes/db_connection.php';

// Obtener ID del mensaje
$message_id = (int)$_GET['id'];

// Consultar mensaje
$sql = "SELECT * FROM contact_messages WHERE id = $message_id";
$result = $conn->query($sql);

// Verificar si el mensaje existe
if (!$result || $result->num_rows == 0) {
    header("Location: index.php");
    exit;
}

// Obtener datos del mensaje
$message = $result->fetch_assoc();

// Actualizar estado a leído si es nuevo
if ($message['status'] === 'new') {
    $update_sql = "UPDATE contact_messages SET status = 'read' WHERE id = $message_id";
    $conn->query($update_sql);
    $message['status'] = 'read';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Mensaje | Panel de Administración</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        header {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        h1 {
            color: #2563eb;
        }
        .message-details {
            margin-bottom: 30px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }
        .detail-label {
            width: 120px;
            font-weight: bold;
        }
        .message-content {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            white-space: pre-wrap;
        }
        .btn {
            padding: 10px 15px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            text-decoration: none;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        .btn-group {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
        .btn-reply {
            background-color: #10b981;
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Ver Mensaje</h1>
            <a href="index.php" class="btn">Volver</a>
        </header>
        
        <div class="message-details">
            <div class="detail-row">
                <div class="detail-label">Estado:</div>
                <div>
                    <?php 
                    switch($message['status']) {
                        case 'new':
                            echo 'Nuevo';
                            break;
                        case 'read':
                            echo 'Leído';
                            break;
                        case 'replied':
                            echo 'Respondido';
                            break;
                    }
                    ?>
                </div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Fecha:</div>
                <div><?php echo date('d/m/Y H:i', strtotime($message['created_at'])); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Nombre:</div>
                <div><?php echo htmlspecialchars($message['name']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div><a href="mailto:<?php echo htmlspecialchars($message['email']); ?>"><?php echo htmlspecialchars($message['email']); ?></a></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Teléfono:</div>
                <div><?php echo htmlspecialchars($message['phone']); ?></div>
            </div>
            <div class="detail-row">
                <div class="detail-label">Asunto:</div>
                <div><?php echo htmlspecialchars($message['subject']); ?></div>
            </div>
        </div>
        
        <h2>Mensaje:</h2>
        <div class="message-content">
            <?php echo htmlspecialchars($message['message']); ?>
        </div>
        
        <div class="btn-group">
            <a href="mailto:<?php echo htmlspecialchars($message['email']); ?>?subject=Re: <?php echo htmlspecialchars($message['subject']); ?>" class="btn btn-reply">Responder por Email</a>
            <a href="update_status.php?id=<?php echo $message_id; ?>&status=replied&redirect=view" class="btn btn-reply">Marcar como Respondido</a>
        </div>
    </div>
</body>
</html>
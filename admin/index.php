<?php
// Iniciar sesión
session_start();

// Verificar si el usuario está logueado
$logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Datos de acceso (en un sistema real, esto estaría en la base de datos)
$admin_username = "admin";
$admin_password = "compuya2025"; // Usa una contraseña fuerte en producción

// Procesar formulario de login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if ($username === $admin_username && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        $logged_in = true;
    } else {
        $error_message = "Usuario o contraseña incorrectos";
    }
}

// Procesar cierre de sesión
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Incluir conexión a la base de datos si está logueado
if ($logged_in) {
    require_once '../includes/db_connection.php';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración | COMPU YA</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f5f5f5;
        }
        .status-new {
            background-color: #fef3c7;
        }
        .status-read {
            background-color: #e0f2fe;
        }
        .status-replied {
            background-color: #dcfce7;
        }
        .login-form {
            max-width: 400px;
            margin: 100px auto;
            padding: 20px;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        .btn {
            padding: 10px 15px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        .error {
            color: #ef4444;
            margin-bottom: 15px;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .btn-sm {
            padding: 5px 10px;
            font-size: 14px;
        }
        .btn-read {
            background-color: #3b82f6;
        }
        .btn-replied {
            background-color: #10b981;
        }
    </style>
</head>
<body>
    <?php if (!$logged_in): ?>
        <!-- Formulario de login -->
        <div class="login-form">
            <h2>Acceso al Panel de Administración</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="error"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" id="username" name="username" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" name="login" class="btn">Iniciar sesión</button>
            </form>
        </div>
    <?php else: ?>
        <!-- Panel de administración -->
        <div class="container">
            <header>
                <h1>Panel de Administración - COMPU YA</h1>
                <a href="?logout=1" class="btn">Cerrar sesión</a>
            </header>
            
            <h2>Mensajes de contacto</h2>
            
            <?php
            // Consultar mensajes de contacto
            $sql = "SELECT * FROM contact_messages ORDER BY created_at DESC";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0):
            ?>
                <table>
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Teléfono</th>
                            <th>Asunto</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr class="status-<?php echo $row['status']; ?>">
                                <td><?php echo date('d/m/Y H:i', strtotime($row['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                <td><a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></td>
                                <td><?php echo htmlspecialchars($row['phone']); ?></td>
                                <td><?php echo htmlspecialchars($row['subject']); ?></td>
                                <td>
                                    <?php 
                                    switch($row['status']) {
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
                                </td>
                                <td class="actions">
                                    <a href="view_message.php?id=<?php echo $row['id']; ?>" class="btn btn-sm">Ver</a>
                                    <a href="update_status.php?id=<?php echo $row['id']; ?>&status=read" class="btn btn-sm btn-read">Marcar como leído</a>
                                    <a href="update_status.php?id=<?php echo $row['id']; ?>&status=replied" class="btn btn-sm btn-replied">Marcar como respondido</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>No hay mensajes de contacto todavía.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</body>
</html>
<?php
// Mostrar errores (quitar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Iniciar sesión
session_start();

// Verificar que la carpeta temp exista y tenga permisos
$tempDir = '../temp';
if (!file_exists($tempDir)) {
    mkdir($tempDir, 0777, true);
} elseif (!is_writable($tempDir)) {
    chmod($tempDir, 0777);
}

// Verificar si el usuario está logueado
$logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Datos de acceso (en un sistema real, esto estaría en la base de datos)
$admin_username = "admin";
$admin_password = "compuya2025"; // Usar una contraseña segura en producción

// Procesar login
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

// Procesar logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Incluir Composer autoload
require '../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

// Función de validación de datos de inventario
function validarDatosInventario($datos) {
    $errores = [];

    // Verificar que haya datos además de los encabezados
    if (count($datos) <= 1) {
        $errores[] = "El archivo no contiene datos de productos. Solo se encontraron encabezados.";
        return $errores;
    }

    // Verificar columnas obligatorias
    $columnasRequeridas = ['nombre', 'marca', 'stock_actual', 'precio_venta'];
    foreach ($columnasRequeridas as $columna) {
        if (!isset($datos[0][$columna])) {
            $errores[] = "Columna '$columna' no encontrada en el archivo.";
        }
    }

    // Validar cada fila
    foreach ($datos as $index => $fila) {
        // Omitir validación de encabezados
        if ($index === 0) continue;

        // Validar nombre y marca no vacíos
        if (empty(trim($fila['nombre']))) {
            $errores[] = "Fila " . ($index + 1) . ": Nombre de producto no puede estar vacío.";
        }

        if (empty(trim($fila['marca']))) {
            $errores[] = "Fila " . ($index + 1) . ": Marca no puede estar vacía.";
        }

        // Validar stock
        if (!is_numeric($fila['stock_actual']) || $fila['stock_actual'] < 0) {
            $errores[] = "Fila " . ($index + 1) . ": Stock debe ser un número positivo.";
        }

        // Validar precio
        if (!is_numeric($fila['precio_venta']) || $fila['precio_venta'] <= 0) {
            $errores[] = "Fila " . ($index + 1) . ": Precio de venta debe ser mayor a 0.";
        }
    }

    return $errores;
}

// Función para generar código de producto
function generarCodigoProducto($marca, $nombre) {
    // Abreviaturas de marcas
    $abrevMarcas = [
        'HP' => 'HP',
        'Epson' => 'EP',
        'Lenovo' => 'LV',
        'Dell' => 'DL',
        'Asus' => 'AS'
    ];
    
    // Determinar la categoría basada en el nombre del producto
    if (strpos(strtolower($nombre), 'impresora') !== false || 
        strpos(strtolower($nombre), 'printer') !== false ||
        strpos(strtolower($nombre), 'tank') !== false ||
        strpos(strtolower($nombre), 'ecotank') !== false) {
        $categoria = 'IMP';
    } elseif (strpos(strtolower($nombre), 'laptop') !== false || 
              strpos(strtolower($nombre), 'notebook') !== false) {
        $categoria = 'LPT';
    } elseif (strpos(strtolower($nombre), 'monitor') !== false) {
        $categoria = 'MON';
    } elseif (strpos(strtolower($nombre), 'teclado') !== false || 
              strpos(strtolower($nombre), 'mouse') !== false || 
              strpos(strtolower($nombre), 'auricular') !== false) {
        $categoria = 'PER';
    } else {
        $categoria = 'GEN'; // Genérico
    }
    
    // Abreviatura de marca
    $abrevMarca = isset($abrevMarcas[$marca]) ? $abrevMarcas[$marca] : substr(strtoupper($marca), 0, 2);
    
    // Abreviatura de modelo (primeras 5 letras sin espacios)
    $abrevModelo = strtoupper(preg_replace('/[^a-zA-Z0-9]/', '', substr($nombre, 0, 10)));
    
    // Generar código único con número secuencial
    return $abrevMarca . '-' . $categoria . '-' . $abrevModelo . '-' . sprintf("%02d", rand(1, 99));
}

// Procesar carga de inventario
$mensaje = '';
$errores_validacion = [];

if ($logged_in && $_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['inventario'])) {
    // Incluir conexión a la base de datos
    require_once '../includes/db_connection.php';
    
    // Validar archivo
    $archivo = $_FILES['inventario'];
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    
    if ($extension != 'xlsx' && $extension != 'xls') {
        $mensaje = "Error: Solo se permiten archivos Excel (.xlsx, .xls)";
    } else {
        // Procesar archivo
        try {
            // Mover archivo a una ubicación temporal
            $rutaTemporal = '../temp/inventario_' . time() . '.' . $extension;
            
            // Verificar si la carga fue exitosa
            if (!move_uploaded_file($archivo['tmp_name'], $rutaTemporal)) {
                throw new Exception("Error al mover el archivo. Verifica permisos de la carpeta 'temp'.");
            }
            
            // Cargar archivo Excel
            $spreadsheet = IOFactory::load($rutaTemporal);
            $worksheet = $spreadsheet->getActiveSheet();
            
            // Obtener datos
            $data = $worksheet->toArray(null, true, true, true);
            
            // Verificar que haya datos
            if (count($data) <= 1) {
                throw new Exception("El archivo no contiene datos de productos o está vacío.");
            }
            
            // Convertir a array asociativo
            $headers = array_shift($data);
            $inventario = [];
            
            foreach ($data as $row) {
                if (!empty(array_filter($row))) { // Ignorar filas vacías
                    $inventario[] = array_combine($headers, $row);
                }
            }
            
            // Validar datos
            $errores_validacion = validarDatosInventario($inventario);
            
            if (empty($errores_validacion)) {
                // Procesar y actualizar inventario
                $actualizados = 0;
                $insertados = 0;
                $errores = 0;
                
                foreach ($inventario as $producto) {
                    try {
                        // Verificar si el producto existe
                        $stmt = $conn->prepare("SELECT id, codigo FROM productos WHERE nombre = ? AND marca = ?");
                        $stmt->bind_param("ss", $producto['nombre'], $producto['marca']);
                        $stmt->execute();
                        $result = $stmt->get_result();
                        
                        if ($result->num_rows > 0) {
                            // Actualizar producto existente
                            $row = $result->fetch_assoc();
                            $stmt = $conn->prepare("UPDATE productos SET stock = ?, precio_venta = ? WHERE id = ?");
                            $stmt->bind_param("idi", $producto['stock_actual'], $producto['precio_venta'], $row['id']);
                            $stmt->execute();
                            $actualizados++;
                        } else {
                            // Generar código de producto
                            $codigo = generarCodigoProducto($producto['marca'], $producto['nombre']);
                            
                            // Insertar nuevo producto
                            $stmt = $conn->prepare("INSERT INTO productos (codigo, nombre, marca, stock, precio_venta) VALUES (?, ?, ?, ?, ?)");
                            $stmt->bind_param("sssid", $codigo, $producto['nombre'], $producto['marca'], $producto['stock_actual'], $producto['precio_venta']);
                            $stmt->execute();
                            $insertados++;
                        }
                    } catch (Exception $e) {
                        $errores++;
                        // Registrar error detallado
                        error_log("Error procesando producto {$producto['nombre']}: " . $e->getMessage());
                    }
                }
                
                // Mensaje más completo
                $mensaje = "Inventario actualizado con éxito. Resumen: <br>
                            - Productos actualizados: $actualizados <br>
                            - Productos nuevos insertados: $insertados <br>
                            - Total de productos procesados: " . count($inventario);
                
                if ($errores > 0) {
                    $mensaje .= "<br>- Errores encontrados: $errores (ver log para detalles)";
                }
                
                // Eliminar archivo temporal
                unlink($rutaTemporal);
            }
        } catch (Exception $e) {
            $mensaje = "Error al procesar el archivo: " . $e->getMessage();
            // Registrar error detallado
            error_log("Error procesando inventario: " . $e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Inventario | COMPU YA</title>
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
        .mensaje {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .mensaje-exito {
            background-color: #d4edda;
            color: #155724;
        }
        .mensaje-error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .lista-errores {
            background-color: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #2563eb;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #1d4ed8;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .example-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 20px;
        }
        .example-table th, .example-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .example-table th {
            background-color: #f2f2f2;
        }
        .help-section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <?php if (!$logged_in): ?>
        <div class="container">
            <h2>Acceso a Gestión de Inventario</h2>
            
            <?php if (isset($error_message)): ?>
                <div class="mensaje mensaje-error">
                    <?php echo $error_message; ?>
                </div>
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
        <div class="container">
            <h1>Gestión de Inventario</h1>
            
            <?php if (!empty($mensaje)): ?>
                <div class="mensaje <?php echo strpos($mensaje, 'Error') !== false ? 'mensaje-error' : 'mensaje-exito'; ?>">
                    <?php echo $mensaje; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errores_validacion)): ?>
                <div class="lista-errores">
                    <h3>Errores de validación:</h3>
                    <ul>
                        <?php foreach ($errores_validacion as $error): ?>
                            <li><?php echo $error; ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="inventario">Cargar archivo de inventario (Excel):</label>
                    <input type="file" id="inventario" name="inventario" accept=".xlsx, .xls" required>
                </div>
                
                <button type="submit" class="btn">Subir y Procesar Inventario</button>
            </form>
            
            <div class="help-section">
                <h3>Formato requerido del archivo Excel</h3>
                <p>El archivo debe tener las siguientes columnas:</p>
                <table class="example-table">
                    <thead>
                        <tr>
                            <th>nombre</th>
                            <th>marca</th>
                            <th>stock_actual</th>
                            <th>precio_venta</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>HP Smart Tank 530</td>
                            <td>HP</td>
                            <td>15</td>
                            <td>549</td>
                        </tr>
                        <tr>
                            <td>Epson EcoTank L3250</td>
                            <td>Epson</td>
                            <td>8</td>
                            <td>569</td>
                        </tr>
                    </tbody>
                </table>
                <p><strong>Importante:</strong> Los nombres de las columnas deben ser exactamente como se muestra arriba.</p>
            </div>
            
            <div style="margin-top: 20px;">
                <a href="index.php" class="btn" style="background-color: #4a5568;">Volver al Panel</a>
                <a href="?logout=1" class="btn" style="background-color: #e53e3e;">Cerrar sesión</a>
            </div>
        </div>
    <?php endif; ?>
</body>
</html>
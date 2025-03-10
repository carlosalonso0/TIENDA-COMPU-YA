<?php
// Mostrar todos los errores
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Datos recibidos:</h1>";
echo "<pre>";
print_r($_POST);
echo "</pre>";
?>

<form action="form-test.php" method="POST">
    <input type="text" name="test" placeholder="Texto de prueba">
    <button type="submit">Enviar</button>
</form>
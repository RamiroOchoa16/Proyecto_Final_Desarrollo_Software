<?php
// Conexión a la base de datos
// Configuración de la base de datos
$server = "localhost";
$user = "root";
$pass = "";
$db = "farmacia";

// Crear conexión a la base de datos usando MySQLi
$conexion = mysqli_connect($server, $user, $pass, $db);

// Verificar la conexión
if (!$conexion) {
    die("Conexión fallida: " . mysqli_connect_error());
} else {
    echo "Conectado<br>";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Proveedores</title>
    <link rel="icon" type="image/x-icon" href="img/agregar-usuario.png">
    <link rel="stylesheet" type="text/css" href="css_agregar_provedor.css">
</head>
<body>
    <div class="form-container">
        <h1>Agregar Nuevo Proveedor</h1>
        <form id="employee-form" action="ListaProvedores.php" method="post">
    <div class="form-group">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="Nombre" required>
    </div>
    <div class="form-group">
        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="Telefono" required>
    </div>
    <div class="form-group">
        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="Direccion">
    </div>
    <div class="button-container">
        <button type="submit">Agregar Proveedor</button>
        <button type="button" class="exit-button" onclick="window.location.href='Pagina_Principal.html';">Salir</button>
    </div>
</form>
    </div>
</body>
</html>

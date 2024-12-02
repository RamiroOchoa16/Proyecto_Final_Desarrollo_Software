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
    <title>Agregar Nueva Sucursal</title>
    <link rel="icon" type="image/x-icon" href="imagenes/Logo1.jpg">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        h1 {
            color: #4e73df;
            margin-bottom: 20px;
        }

        form {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
            max-width: 400px;
            width: 100%;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }

        input:focus {
            border-color: #4e73df;
            outline: none;
        }

        button {
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            font-size: 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button[type="submit"] {
            background-color: #4e73df;
            color: white;
        }

        button[type="submit"]:hover {
            background-color: #3b62c3;
        }

        .exit-button {
            background-color: #ddd;
            color: #333;
        }

        .exit-button:hover {
            background-color: #bbb;
        }
    </style>
</head>
<body>
    <h1>Agregar Nueva Sucursal</h1>
    <form id="employee-form" action="ListaSucursales.php" method="post">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="Nombre" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" id="telefono" name="Telefono" required>

        <label for="direccion">Dirección:</label>
        <input type="text" id="direccion" name="Direccion">

        <label for="usuario">Usuario:</label>
        <input type="text" id="usuario" name="Usuario">

        <label for="contraseña">Contraseña:</label>
        <input type="password" id="contraseña" name="Clave" required>

        <button type="submit">Agregar Sucursal</button>
        <button type="button" class="exit-button" onclick="window.location.href='Pagina_Principal.html';">Salir</button>
    </form>
</body>
</html>

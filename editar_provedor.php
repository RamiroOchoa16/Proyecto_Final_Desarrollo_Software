<?php
$host = 'localhost';
$dbname = 'farmacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . $e->getMessage());
}


if (isset($_GET['id'])) {
    $idProvedor = intval($_GET['id']); 

    
    $query = "SELECT * FROM provedores WHERE idProvedor = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idProvedor, PDO::PARAM_INT);
    $stmt->execute();
    $provedor = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$provedor) {
        die("Proveedor no encontrado.");
    }
} else {
    die("ID de Proveedor no especificado.");
}

// Procesar el formulario de actualización
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Sanitizar entradas
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    

    // Encriptar la clave
    //$claveEncriptada = password_hash($clave, PASSWORD_BCRYPT);

    try {
        $query = "UPDATE provedores 
                  SET Nombre = :nombre, Telefono = :telefono, Direccion = :direccion
                  WHERE idProvedor = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':id', $idProvedor, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: ListaProvedores.php?mensaje=Proveedor actualizado exitosamente");
            exit();
        } else {
            echo "<p>Error al actualizar Proveedor.</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Error en la base de datos: " . $e->getMessage() . "</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <link rel="icon" type="image/x-icon" href="img/Editar.png">
    <link rel="stylesheet" type="text/css" href="css_editar_provedor.css">
</head>
<body>
    <h1>Editar  Proveedor</h1>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($provedor['Nombre']); ?>" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($provedor['Telefono']); ?>" required>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" value="<?php echo htmlspecialchars($provedor['Direccion']); ?>" required>
        <div class="form-container">
        <div>
        <button class="act" type="submit">Guardar Cambios</button>
        </div>
        <div>
        <button class="exit-button" type="button" onclick="window.location.href='ListaProvedores.php'">Salir sin Guardar</button>
        
        </div></div>
    </form>
</body>
</html>

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

// Verificar si se recibió un ID de sucursal
if (isset($_GET['id'])) {
    $idSucursal = intval($_GET['id']); // Conversión segura a entero

    // Obtener los datos de la sucursal
    $query = "SELECT * FROM sucursal WHERE idSucursal = :id";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':id', $idSucursal, PDO::PARAM_INT);
    $stmt->execute();
    $sucursal = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$sucursal) {
        die("Sucursal no encontrada.");
    }
} else {
    die("ID de Sucursal no especificado.");
}


if ($_SERVER["REQUEST_METHOD"] === "POST") {
    
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $telefono = htmlspecialchars(trim($_POST['telefono']));
    $direccion = htmlspecialchars(trim($_POST['direccion']));
    $usuario = htmlspecialchars(trim($_POST['usuario']));
    $clave = htmlspecialchars(trim($_POST['clave']));

    

    try {
        $query = "UPDATE sucursal 
                  SET Nombre = :nombre, Telefono = :telefono, Direccion = :direccion, Usuario = :usuario, Clave = :clave 
                  WHERE idSucursal = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':telefono', $telefono);
        $stmt->bindParam(':direccion', $direccion);
        $stmt->bindParam(':usuario', $usuario);
        $stmt->bindParam(':clave', $clave);
        $stmt->bindParam(':id', $idSucursal, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: ListaSucursales.php?mensaje=Sucursal actualizada exitosamente");
            exit();
        } else {
            echo "<p>Error al actualizar la sucursal.</p>";
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
    <title>Editar Sucursal</title>
    <link rel="icon" type="image/x-icon" href="img/Editar.png">
    <link rel="stylesheet" type="text/css" href="css_editar_sucursal.css">
</head>
<body>
    <h1>Editar  Sucursal</h1>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" name="nombre" value="<?php echo htmlspecialchars($sucursal['Nombre']); ?>" required>

        <label for="telefono">Teléfono:</label>
        <input type="text" name="telefono" value="<?php echo htmlspecialchars($sucursal['Telefono']); ?>" required>

        <label for="direccion">Dirección:</label>
        <input type="text" name="direccion" value="<?php echo htmlspecialchars($sucursal['Direccion']); ?>" required>

        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" value="<?php echo htmlspecialchars($sucursal['Usuario']); ?>" required>

        <label for="clave">Clave:</label>
        <input type="text" name="clave" value="<?php echo htmlspecialchars($sucursal['Clave']); ?>" required>
        <div class="form-container">
        <div>
        <button  class="act" type="submit">Guardar Cambios</button>
        </div>
        <div>
        <button class="exit-button" type="button" onclick="window.location.href='ListaSucursales.php'">Salir sin Guardar</button>
        </div></div>
    </form>
</body>
</html>
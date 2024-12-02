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



if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idEmpleado = $_GET['id'];

    
    $query = "SELECT * FROM empleados WHERE idEmpleado = :idEmpleado";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idEmpleado', $idEmpleado);
    $stmt->execute();
    $empleado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$empleado) {
        die("Empleado no encontrado.");
    }
} else {
    die("ID de empleado no válido.");
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $apellido_p = strtoupper($_POST['apellido_p']);
    $apellido_m = strtoupper($_POST['apellido_m']);
    $telefono = $_POST['telefono'];
    $curp = strtoupper($_POST['curp']);
    $rfc = strtoupper($_POST['rfc']);
    $idPuesto = $_POST['idPuesto'];
    $idSucursal = $_POST['idSucursal'];

    $query = "UPDATE empleados 
              SET Nombre = :nombre, ApellidoP = :apellido_p, ApellidoM = :apellido_m, 
                  Telefono = :telefono, CURP = :curp, RFC = :rfc, idPuesto = :idPuesto, idSucursal = :idSucursal 
              WHERE idEmpleado = :idEmpleado";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':apellido_p', $apellido_p);
    $stmt->bindParam(':apellido_m', $apellido_m);
    $stmt->bindParam(':telefono', $telefono);
    $stmt->bindParam(':curp', $curp);
    $stmt->bindParam(':rfc', $rfc);
    $stmt->bindParam(':idPuesto', $idPuesto);
    $stmt->bindParam(':idSucursal', $idSucursal);
    $stmt->bindParam(':idEmpleado', $idEmpleado);

    if ($stmt->execute()) {
        
        header("Location: ListaEmpleados.php?mensaje=Empleado actualizado con éxito.");
        exit;
    } else {
        
        header("Location: ListaEmpleados.php?mensaje=Error al actualizar el empleado.");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empleado</title>
    <link rel="icon" type="image/x-icon" href="img/Editar.png">
    <link rel="stylesheet" type="text/css" href="css_editar_empleado.css">
</head>
<body>
    <div class="container">
        <h1>Editar Empleado</h1>
        <form method="POST">
            <div class="form-container">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($empleado['Nombre']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido_p">Apellido Paterno:</label>
                    <input type="text" id="apellido_p" name="apellido_p" value="<?php echo htmlspecialchars($empleado['ApellidoP']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="apellido_m">Apellido Materno:</label>
                    <input type="text" id="apellido_m" name="apellido_m" value="<?php echo htmlspecialchars($empleado['ApellidoM']); ?>">
                </div>
                <div class="form-group">
                    <label for="telefono">Teléfono:</label>
                    <input type="text" id="telefono" name="telefono" value="<?php echo htmlspecialchars($empleado['Telefono']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="curp">CURP:</label>
                    <input type="text" id="curp" name="curp" value="<?php echo htmlspecialchars($empleado['CURP']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="rfc">RFC:</label>
                    <input type="text" id="rfc" name="rfc" value="<?php echo htmlspecialchars($empleado['RFC']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="puesto">Puesto:</label>
                    <select id="puesto" name="idPuesto" required>
                        <?php
                        $puestos = $pdo->query("SELECT idPuesto, Puesto FROM puestos")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($puestos as $puesto) {
                            $selected = $puesto['idPuesto'] == $empleado['idPuesto'] ? 'selected' : '';
                            echo "<option value='{$puesto['idPuesto']}' $selected>{$puesto['Puesto']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sucursal">Sucursal:</label>
                    <select id="sucursal" name="idSucursal" required>
                        <?php
                        $sucursales = $pdo->query("SELECT idSucursal, Nombre FROM sucursal")->fetchAll(PDO::FETCH_ASSOC);
                        foreach ($sucursales as $sucursal) {
                            $selected = $sucursal['idSucursal'] == $empleado['idSucursal'] ? 'selected' : '';
                            echo "<option value='{$sucursal['idSucursal']}' $selected>{$sucursal['Nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="button-container">
                <button class="act" type="submit">Actualizar</button>
                <button class="exit-button" type="button" onclick="window.location.href='Listaempleados.php';">Salir sin Guardar</button>
            </div>
        </form>
    </div>
</body>
</html>

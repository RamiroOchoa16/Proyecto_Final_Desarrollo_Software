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

// Verificar si se recibió un ID de medicamento
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idMedicamento = $_GET['id'];

    // Obtener los datos del medicamento
    $query = "SELECT * FROM medicamentos WHERE idMedicamento = :idMedicamento";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':idMedicamento', $idMedicamento);
    $stmt->execute();
    $medicamento = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$medicamento) {
        die("Medicamento no encontrado.");
    }
} else {
    die("ID de medicamento no válido.");
}

// Actualizar datos del medicamento si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $cantidad = $_POST['cantidad'];
    $precio_c = $_POST['precio_c'];
    $precio_v = $_POST['precio_v'];
    $descripcion = strtoupper($_POST['descripcion']);
    $idClasificacion = $_POST['idClasificacion'];
    $idEliminacion = $_POST['idEliminacion'];
    $idProveedor = $_POST['idProvedor'];

    $query = "UPDATE medicamentos 
              SET Nombre = :nombre, Cantidad = :cantidad, PrecioCompra = :precio_c, 
                  PrecioVenta = :precio_v, Descripcion = :descripcion, 
                  idClasificacion = :idClasificacion, idEliminacion = :idEliminacion, idProvedor = :idProvedor
              WHERE idMedicamento = :idMedicamento";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':precio_c', $precio_c);
    $stmt->bindParam(':precio_v', $precio_v);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':idClasificacion', $idClasificacion);
    $stmt->bindParam(':idEliminacion', $idEliminacion);
    $stmt->bindParam(':idProvedor', $idProveedor);
    $stmt->bindParam(':idMedicamento', $idMedicamento);

    if ($stmt->execute()) {
        
        header("Location: ListaMedicamentos.php");
        exit;
    } else {
        echo "<p>Error al actualizar el medicamento.</p>";
    }
    
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Medicamento</title>
    <link rel="icon" type="image/x-icon" href="img/Editar.png">
    <link rel="stylesheet" type="text/css" href="styleEditar.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-top: 20px;
        }

        .form-container {
            display: flex;
            justify-content: space-around;
            margin-top: 40px;
            margin-bottom: 40px;
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .form-container div {
            flex: 1;
            padding: 15px;
            box-sizing: border-box;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            font-size: 14px;
            color: #333;
            margin-bottom: 5px;
            display: block;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            font-size: 14px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="text"]:focus,
        input[type="number"]:focus,
        select:focus {
            border-color: #4e73df;
            outline: none;
        }

        button {
            background-color: #4e73df;
            color: white;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 20px;
        }

        button:hover {
            background-color: #2e59d9;
        }

        .exit-button {
            background-color: #e74a3b;
            width: 100%;
            padding: 10px;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .exit-button:hover {
            background-color: #c0392b;
        }

        .form-container div:last-child {
            margin-left: 20px;
        }

        .exit-button {
            margin-top: 20px;
        }

    </style>
</head>
<body>

<h1>Editar Medicamento</h1>
<form method="POST">
    <div class="form-container">
        <div>
            <div class="form-group">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($medicamento['Nombre']); ?>" required>
            </div>

            <div class="form-group">
                <label for="cantidad">Cantidad:</label>
                <input type="number" id="cantidad" name="cantidad" value="<?php echo htmlspecialchars($medicamento['Cantidad']); ?>" required>
            </div>

            <div class="form-group">
                <label for="precio_c">Precio Compra:</label>
                <input type="number" step="0.01" id="precio_c" name="precio_c" value="<?php echo htmlspecialchars($medicamento['PrecioCompra']); ?>" required>
            </div>

            <div class="form-group">
                <label for="precio_v">Precio Venta:</label>
                <input type="number" step="0.01" id="precio_v" name="precio_v" value="<?php echo htmlspecialchars($medicamento['PrecioVenta']); ?>" required>
            </div>

            <button class="act" type="submit">Actualizar</button>
        </div>

        <div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <input type="text" id="descripcion" name="descripcion" value="<?php echo htmlspecialchars($medicamento['Descripcion']); ?>" required>
            </div>

            <div class="form-group">
                <label for="clasificacion">Clasificación:</label>
                <select id="clasificacion" name="idClasificacion" required>
                    <?php
                    $clasificacion = $pdo->query("SELECT idClasificacion, Tipo FROM clasificacion")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($clasificacion as $clas) {
                        $selected = $clas['idClasificacion'] == $medicamento['idClasificacion'] ? 'selected' : '';
                        echo "<option value='{$clas['idClasificacion']}' $selected>{$clas['Tipo']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="eliminacion">En caso de no venderse:</label>
                <select id="eliminacion" name="idEliminacion" required>
                    <?php
                    $eliminacion = $pdo->query("SELECT idEliminacion, MedRegresable FROM eliminacionmedicamento")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($eliminacion as $eli) {
                        $selected = $eli['idEliminacion'] == $medicamento['idEliminacion'] ? 'selected' : '';
                        echo "<option value='{$eli['idEliminacion']}' $selected>{$eli['MedRegresable']}</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="form-group">
                <label for="provedor">Proveedor:</label>
                <select id="provedor" name="idProvedor" required>
                    <?php
                    $provedores = $pdo->query("SELECT idProvedor, Nombre FROM Provedores")->fetchAll(PDO::FETCH_ASSOC);
                    foreach ($provedores as $provedor) {
                        $selected = $provedor['idProvedor'] == $medicamento['idProvedor'] ? 'selected' : '';
                        echo "<option value='{$provedor['idProvedor']}' $selected>{$provedor['Nombre']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button class="exit-button" type="button" onclick="window.location.href='ListaMedicamentos.php';">Salir sin Guardar</button>
        </div>
    </div>
</form>

</body>
</html>

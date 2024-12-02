<?php
// Conexión a la base de datos
$host = 'localhost'; 
$dbname = 'farmacia';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión: " . htmlspecialchars($e->getMessage()));
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idSucursal = $_POST['idSucursal'];
    $idProveedor = $_POST['idProvedor'];
    $fechaPedido = date('Y-m-d');
    $estado = 'Pendiente';
    $medicamentos = $_POST['medicamento'] ?? [];
    $cantidades = $_POST['cantidad'] ?? [];

    if (empty($medicamentos) || empty($cantidades)) {
        echo "<p style='color:red;'>Debe seleccionar al menos un medicamento con cantidad válida.</p>";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insertar el pedido
            $stmt = $pdo->prepare("INSERT INTO pedidos (idSucursal, idProvedor, FechaPedido, Estado) VALUES (?, ?, ?, ?)");
            $stmt->execute([$idSucursal, $idProveedor, $fechaPedido, $estado]);
            $idPedido = $pdo->lastInsertId();

            // Insertar detalles del pedido
            $stmtDetalle = $pdo->prepare("INSERT INTO detallepedidos (idPedido, idMedicamento, Cantidad, Precio) VALUES (?, ?, ?, ?)");
            foreach ($medicamentos as $index => $idMedicamento) {
                $cantidad = filter_var($cantidades[$index], FILTER_VALIDATE_INT);
                if ($cantidad && $cantidad > 0) {
                    $stmtPrecio = $pdo->prepare("SELECT PrecioVenta FROM medicamentos WHERE idMedicamento = ?");
                    $stmtPrecio->execute([$idMedicamento]);
                    $precio = $stmtPrecio->fetchColumn();
                    
                    if ($precio !== false) {
                        $stmtDetalle->execute([$idPedido, $idMedicamento, $cantidad, $precio]);
                    } else {
                        echo "<p style='color:red;'>Medicamento con ID " . htmlspecialchars($idMedicamento) . " no encontrado.</p>";
                    }
                } else {
                    echo "<p style='color:red;'>Cantidad inválida para el medicamento: " . htmlspecialchars($idMedicamento) . "</p>";
                }
            }

            $pdo->commit();
            echo "<p style='color:green;'>Pedido realizado con éxito.</p>";
        } catch (PDOException $e) {
            $pdo->rollBack();
            echo "Error al realizar el pedido: " . htmlspecialchars($e->getMessage());
        }
    }
}

// Consultar medicamentos y proveedores
$medicamentos = $pdo->query("SELECT idMedicamento, Nombre FROM medicamentos WHERE Estatus = 'Disponible'")->fetchAll(PDO::FETCH_ASSOC);
$proveedores = $pdo->query("SELECT idProvedor, Nombre FROM provedores")->fetchAll(PDO::FETCH_ASSOC);
$sucursales = $pdo->query("SELECT idSucursal, Nombre FROM sucursal")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Realizar Pedido</title>
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

        form {
            width: 90%;
            max-width: 600px;
            margin: 30px auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            font-size: 16px;
            margin-bottom: 8px;
            color: #333;
        }

        select, input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 14px;
        }

        input[type="checkbox"] {
            margin-right: 10px;
        }

        button {
            width: calc(50% - 10px);
            background-color: #6c83d0;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin: 5px;
        }

        button:hover {
            background-color: #5a6dbd;
        }

        button:active {
            background-color: #4e73df;
        }

        .cancelar {
            background-color: #e74a3b;
        }

        .cancelar:hover {
            background-color: #d64533;
        }

        .medicamento-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .medicamento-row input[type="number"] {
            width: 40%;
        }

        .button-container {
            display: flex;
            justify-content: space-between;
        }
    </style>
    <script>
        
        function toggleCantidad(checkbox, index) {
            const cantidadInput = document.getElementById('cantidad_' + index);
            cantidadInput.disabled = !checkbox.checked;
            if (!checkbox.checked) {
                cantidadInput.value = ''; 
            }
        }
    </script>
</head>
<body>
    <h1>Realizar Pedido</h1>
    <form method="POST">
        <label for="idSucursal">Sucursal:</label>
        <select name="idSucursal" required>
            <?php foreach ($sucursales as $sucursal): ?>
                <option value="<?= $sucursal['idSucursal'] ?>"><?= $sucursal['Nombre'] ?></option>
            <?php endforeach; ?>
        </select>

        <label for="idProvedor">Proveedor:</label>
        <select name="idProvedor" required>
            <?php foreach ($proveedores as $proveedor): ?>
                <option value="<?= $proveedor['idProvedor'] ?>"><?= $proveedor['Nombre'] ?></option>
            <?php endforeach; ?>
        </select>

        <h3>Medicamentos</h3>
        <?php foreach ($medicamentos as $index => $medicamento): ?>
            <div class="medicamento-row">
                <div>
                    <input type="checkbox" name="medicamento[]" value="<?= $medicamento['idMedicamento'] ?>" 
                        onclick="toggleCantidad(this, <?= $index ?>)">
                    <?= $medicamento['Nombre'] ?>
                </div>
                <input type="number" name="cantidad[]" id="cantidad_<?= $index ?>" min="1" placeholder="Cantidad" disabled required>
            </div>
        <?php endforeach; ?>

        <div class="button-container">
            <button type="submit">Realizar Pedido</button>
            <button type="button" class="cancelar" onclick="window.location.href='Pagina_Principal.html';">Cancelar</button>
        </div>
    </form>
</body>
</html>

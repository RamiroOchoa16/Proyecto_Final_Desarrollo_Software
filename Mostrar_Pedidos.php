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
// Manejo de la acción de Aprobar/Rechazar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'], $_POST['idPedido'])) {
    $pedidoID = filter_input(INPUT_POST, 'idPedido', FILTER_VALIDATE_INT);
    $accion = trim($_POST['accion']);

    if (in_array($accion, ['Aprobado', 'Rechazado']) && $pedidoID) {
        try {
            $stmt = $pdo->prepare("UPDATE pedidos SET Estado = ? WHERE idPedido = ?");
            $stmt->execute([$accion, $pedidoID]);
            echo "<p style='color: green;'>Pedido $accion con éxito.</p>";
        } catch (Exception $e) {
            echo "<p style='color: red;'>Error al cambiar el estado: " . htmlspecialchars($e->getMessage()) . "</p>";
        }
    } else {
        echo "<p style='color: red;'>Datos inválidos.</p>";
    }
}


// Obtener todos los pedidos
$stmt = $pdo->query("SELECT p.idPedido, dp.Cantidad, p.Estado, m.Nombre 
        FROM detallepedidos dp 
        JOIN medicamentos m ON dp.idMedicamento = m.idMedicamento
        JOIN pedidos p ON dp.idPedido = p.idPedido
        WHERE Estado='Pendiente'
        ORDER BY p.idPedido DESC");
$pedidos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Pedidos</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
            padding: 20px;
        }

        .pedido {
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            width: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .pedido:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        }

        .pedido h3 {
            margin-top: 0;
            font-size: 18px;
            color: #4e73df;
        }

        .pedido p {
            font-size: 14px;
            margin: 5px 0;
            color: #666;
        }

        .pedido .buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }

        .pedido button {
            background-color: #6c83d0;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .pedido button:hover {
            background-color: #5a6dbd;
        }

        .pedido em {
            font-size: 14px;
            color: #666;
            margin: auto;
        }

        .aprobado {
            color: green;
            font-weight: bold;
        }

        .rechazado {
            color: red;
            font-weight: bold;
        }

        .pendiente {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>Gestión de Pedidos</h1>
    <div class="container">
        <?php foreach ($pedidos as $pedido): ?>
            <div class="pedido">
                <h3><?php echo htmlspecialchars($pedido['Nombre']); ?></h3>
                <p><strong>Cantidad:</strong> <?php echo htmlspecialchars($pedido['Cantidad']); ?></p>
                <p><strong>Estado:</strong> 
                    <span class="<?php echo strtolower($pedido['Estado']); ?>">
                        <?php echo htmlspecialchars($pedido['Estado']); ?>
                    </span>
                </p>
                <form method="POST">
                    <input type="hidden" name="idPedido" value="<?php echo htmlspecialchars($pedido['idPedido']); ?>">

                    <div class="buttons">
                        <?php if ($pedido['Estado'] === 'Pendiente'): ?>
                            <button type="submit" name="accion" value="Aprobado">Aprobar</button>
                            <button type="submit" name="accion" value="Rechazado">Rechazar</button>
                        <?php else: ?>
                            <em>Acción completada</em>
                        <?php endif; ?>
                    </div>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
</body>
</html>

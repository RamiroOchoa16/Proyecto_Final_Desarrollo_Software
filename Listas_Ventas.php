<?php
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


$ventas = $pdo->query("
    SELECT v.idVenta, v.FechaVenta, s.Nombre AS Sucursal, e.Nombre AS Empleado,
           dv.Cantidad, dv.PrecioTotal, m.Nombre AS Medicamento
    FROM ventas v
    JOIN sucursal s ON v.idSucursal = s.idSucursal
    JOIN empleados e ON v.idEmpleado = e.idEmpleado
    JOIN detalleventas dv ON v.idVenta = dv.idVenta
    JOIN medicamentos m ON dv.idMedicamento = m.idMedicamento
    ORDER BY v.FechaVenta DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ventas Registradas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f6f8;
        }

        h1 {
            color: #4e73df;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #4e73df;
            color: white;
        }

        table tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <h1>Ventas Registradas</h1>
    <table>
        <thead>
            <tr>
                <th>ID Venta</th>
                <th>Fecha</th>
                <th>Sucursal</th>
                <th>Empleado</th>
                <th>Medicamento</th>
                <th>Cantidad</th>
                <th>Precio Total</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($ventas) > 0): ?>
                <?php foreach ($ventas as $venta): ?>
                    <tr>
                        <td><?= htmlspecialchars($venta['idVenta']) ?></td>
                        <td><?= htmlspecialchars($venta['FechaVenta']) ?></td>
                        <td><?= htmlspecialchars($venta['Sucursal']) ?></td>
                        <td><?= htmlspecialchars($venta['Empleado']) ?></td>
                        <td><?= htmlspecialchars($venta['Medicamento']) ?></td>
                        <td><?= htmlspecialchars($venta['Cantidad']) ?></td>
                        <td>$<?= number_format($venta['PrecioTotal'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="7">No hay ventas registradas.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>

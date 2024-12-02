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

function obtenerMedicamentos($pdo, $estatus, $idSucursal = null) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad, M.PrecioCompra, M.PrecioVenta, 
            E.MedRegresable, P.Nombre AS Provedor, M.Descripcion, M.Estatus
        FROM medicamentos M
        JOIN clasificacion C ON M.idClasificacion = C.idClasificacion
        JOIN eliminacionmedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN provedores P ON M.idProvedor = P.idProvedor
        WHERE M.Estatus = :estatus";
    
    
    if (!is_null($idSucursal)) {
        $query .= " AND M.idSucursal = :idSucursal";
    }
    
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    
    
    if (!is_null($idSucursal)) {
        $stmt->bindParam(':idSucursal', $idSucursal, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


$idSucursal = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;


$medicamentos = obtenerMedicamentos($pdo, 'Eliminado', $idSucursal);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medicamentos Eliminados</title>
    <link rel="icon" type="image/x-icon" href="img/Eliminar.png">
    <link rel="stylesheet" type="text/css" href="styleListas.css">
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

        table {
            width: 90%;
            margin: 30px auto;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4e73df;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        button {
            background-color: #4e73df;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        button:hover {
            background-color: #2e59d9;
        }
    </style>
</head>
<body>

<h1>Medicamentos Eliminados</h1>

<table>
    <thead>
        <tr>
            <th>Nombre</th>
            <th>Clasificación</th>
            <th>Cantidad</th>
            <th>Precio Compra</th>
            <th>Precio Venta</th>
            <th>Medicación Regresable</th>
            <th>Proveedor</th>
            <th>Descripción</th>
            <th>Estatus</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($medicamentos)): ?>
            <?php foreach ($medicamentos as $medicamento): ?>
                <tr>
                    <td><?php echo htmlspecialchars($medicamento['Nombre']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['Clasificacion']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['Cantidad']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['PrecioCompra']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['PrecioVenta']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['MedRegresable']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['Provedor']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['Descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($medicamento['Estatus']); ?></td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="9" style="text-align:center;">No se encontraron medicamentos eliminados.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<button onclick="window.location.href='ListaSucursales.php';">Regresar</button>

</body>
</html>

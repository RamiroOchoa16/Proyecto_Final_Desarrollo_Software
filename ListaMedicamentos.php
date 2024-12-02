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


function insertarMedicamento($pdo, $nombre, $idClasificacion, $cantidad, $precio_c, $precio_v, $idEliminacion, $idProveedor, $descripcion, $estatus, $fechaCaducidad, $idSucursal) {
    $query = "
        INSERT INTO medicamentos (Nombre, idClasificacion, Cantidad, PrecioCompra, PrecioVenta, idEliminacion, idProvedor, Descripcion, Estatus, FechaCaducidad, idSucursal) 
        VALUES (:nombre, :idClasificacion, :cantidad, :precio_c, :precio_v, :idEliminacion, :idProvedor, :descripcion, :estatus, :fechaCaducidad, :idSucursal)";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nombre', $nombre);
    $stmt->bindParam(':idClasificacion', $idClasificacion);
    $stmt->bindParam(':cantidad', $cantidad);
    $stmt->bindParam(':precio_c', $precio_c);
    $stmt->bindParam(':precio_v', $precio_v);
    $stmt->bindParam(':idEliminacion', $idEliminacion);
    $stmt->bindParam(':idProvedor', $idProveedor);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->bindParam(':fechaCaducidad', $fechaCaducidad);
    $stmt->bindParam(':idSucursal', $idSucursal);

    if ($stmt->execute()) {
        echo "<p>Medicamento agregado con éxito.</p>";
    } else {
        echo "<p>Error al agregar el medicamento.</p>";
    }
}



$idSucursal = isset($_GET['idSucursal']) ? $_GET['idSucursal'] : null; 

function obtenerMedicamentosPorEstatusYSucursal($pdo, $estatus, $idSucursal) {
    $query = "
        SELECT 
            M.idMedicamento, M.Nombre, C.Tipo AS Clasificacion, M.Cantidad, M.PrecioCompra, M.PrecioVenta, 
            E.MedRegresable, P.Nombre AS Provedor, M.Descripcion, M.Estatus
        FROM medicamentos M
        JOIN clasificacion C ON M.idClasificacion = C.idClasificacion
        JOIN eliminacionmedicamento E ON M.idEliminacion = E.idEliminacion
        JOIN provedores P ON M.idProvedor = P.idProvedor
        WHERE M.Estatus = :estatus AND M.idSucursal = :idSucursal"; 

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':estatus', $estatus);
    $stmt->bindParam(':idSucursal', $idSucursal, PDO::PARAM_INT);
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = strtoupper($_POST['nombre']);
    $idClasificacion = $_POST['idClasificacion'];
    $cantidad = $_POST['cantidad'];
    $precio_c = $_POST['precio_c'];
    $precio_v = $_POST['precio_v'];
    $idEliminacion = $_POST['idEliminacion'];
    $idProveedor = $_POST['idProvedor'];
    $descripcion = strtoupper($_POST['descripcion']);
    $estatus = 'Disponible'; 
    $fechaCaducidad = $_POST['FechaCaducidad'];
    $idSucursal = $_POST['idSucursal'];

    insertarMedicamento($pdo, $nombre, $idClasificacion, $cantidad, $precio_c, $precio_v, $idEliminacion, $idProveedor, $descripcion, $estatus,$fechaCaducidad,$idSucursal);
}

$medicamentos = obtenerMedicamentosPorEstatusYSucursal($pdo, 'Disponible', $idSucursal);


?>

<?php

if (isset($_GET['mensaje'])) {
    echo "<div class='mensaje'>" . htmlspecialchars($_GET['mensaje']) . "</div>";
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Medicamentos</title>
    <link rel="stylesheet" href="styleListas.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f6f8;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            min-height: 100vh;
            padding-top: 20px;
        }

        h1 {
            color: #4e73df;
            margin: 20px 0;
        }

        table {
            width: 90%;
            max-width: 1200px;
            border-collapse: collapse;
            margin-bottom: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        table thead tr {
            background-color: #4e73df;
            color: white;
        }

        table th, table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table tbody tr:nth-child(even) {
            background-color: #f9fafb;
        }

        table tbody tr:hover {
            background-color: #f1f5f9;
        }

        table img {
            width: 24px;
            height: 24px;
            cursor: pointer;
            margin-right: 10px;
        }

        button {
            margin: 10px;
            padding: 10px 20px;
            background-color: #4e73df;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        button:hover {
            background-color: #3b62c3;
        }

        @media (max-width: 768px) {
            table th, table td {
                font-size: 12px;
                padding: 8px;
            }

            button {
                font-size: 14px;
                padding: 8px 16px;
            }

            table img {
                width: 20px;
                height: 20px;
            }
        }
    </style>
</head>
<body>

<h1>Lista de Medicamentos</h1>


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
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($medicamentos as $medicamento): ?>
            <tr>
                <td><?php echo htmlspecialchars($medicamento['Nombre']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Clasificacion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Cantidad']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['PrecioCompra']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['PrecioVenta']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['MedRegresable']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Proveedor']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Descripcion']); ?></td>
                <td><?php echo htmlspecialchars($medicamento['Estatus']); ?></td>
                <td>
                    <span>
                        <a href="editar_medicamento.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>">
                            <img src="imagenes/Editar.png" alt="Editar">
                        </a>
                        <a href="eliminar_medicamento.php?id=<?php echo htmlspecialchars($medicamento['idMedicamento']); ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este medicamento?');">
                            <img src="imagenes/Eliminar.png" alt="Eliminar">
                        </a>
                    </span>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<button onclick="window.location.href='ListaSucursales.php';">Regresar</button>

</body>
</html>
